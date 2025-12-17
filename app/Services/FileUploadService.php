<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;

class FileUploadService
{
    /**
     * Upload an image with optional resized versions
     *
     * @param UploadedFile $file
     * @param string $path
     * @param array $sizes
     * @return array
     */
    public function uploadImage(UploadedFile $file, string $path, array $sizes = []): array
    {
        // Generate unique filename
        $filename = $this->generateUniqueFilename($file, $path);
        $fullPath = $path . '/' . $filename;
        
        // Store original
        $file->storeAs($path, $filename, 'public');
        
        $images = ['original' => $fullPath];
        
        // Create resized versions if requested
        if (!empty($sizes)) {
            foreach ($sizes as $sizeName => $dimensions) {
                [$width, $height] = $dimensions;
                
                try {
                    // Create Intervention Image instance
                    $image = Image::read($file);
                    
                    // Apply resize/fit based on dimensions
                    if ($width && $height) {
                        $image->cover($width, $height); // Changed from fit() to cover() for better cropping
                    } elseif ($width) {
                        $image->scale(width: $width);
                    } elseif ($height) {
                        $image->scale(height: $height);
                    }
                    
                    // Determine encoder based on file type
                    $encoder = $this->getEncoder($file);
                    
                    // Generate resized filename
                    $resizedFilename = $sizeName . '_' . $filename;
                    $resizedPath = $path . '/' . $resizedFilename;
                    
                    // Save resized image with quality settings
                    $encodedImage = $image->encode($encoder);
                    Storage::disk('public')->put($resizedPath, $encodedImage);
                    
                    $images[$sizeName] = $resizedPath;
                    
                } catch (\Exception $e) {
                    // Log error but continue with other sizes
                    Log::error("Failed to create resized image {$sizeName}: " . $e->getMessage());
                    continue;
                }
            }
        }
        
        return $images;
    }
    
    /**
     * Upload multiple images
     *
     * @param array $files
     * @param string $path
     * @param array $sizes
     * @return array
     */
    public function uploadMultipleImages(array $files, string $path, array $sizes = []): array
    {
        $uploadedImages = [];
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $uploadedImages[] = $this->uploadImage($file, $path, $sizes);
            }
        }
        
        return $uploadedImages;
    }
    
    /**
     * Delete an image and all its variants
     *
     * @param string $path
     * @param array|null $variants
     * @return bool
     */
    public function deleteImage(string $path, ?array $variants = null): bool
    {
        $success = true;
        
        // Delete the main image
        if (Storage::disk('public')->exists($path)) {
            $success = Storage::disk('public')->delete($path) && $success;
        }
        
        // Delete variants if provided
        if ($variants) {
            foreach ($variants as $variantPath) {
                if (Storage::disk('public')->exists($variantPath)) {
                    $success = Storage::disk('public')->delete($variantPath) && $success;
                }
            }
        } else {
            // Try to find and delete common variants
            $this->deleteImageVariants($path);
        }
        
        return $success;
    }
    
    /**
     * Delete image variants based on naming patterns
     *
     * @param string $originalPath
     * @return void
     */
    public function deleteImageVariants(string $originalPath): void
    {
        $pathInfo = pathinfo($originalPath);
        $dirname = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'];
        
        // Common variant patterns
        $variantPatterns = [
            'thumbnail_*',
            'medium_*',
            'large_*',
            'small_*',
            '*_thumbnail.*',
            '*_medium.*',
            '*_large.*',
            '*_small.*',
        ];
        
        // Get all files in the directory
        $files = Storage::disk('public')->files($dirname);
        
        foreach ($files as $file) {
            $fileInfo = pathinfo($file);
            
            // Check if it's a variant of the original
            foreach ($variantPatterns as $pattern) {
                if (fnmatch($pattern, $fileInfo['basename'])) {
                    // Check if it contains the original filename
                    if (str_contains($fileInfo['filename'], $filename)) {
                        Storage::disk('public')->delete($file);
                        break;
                    }
                }
            }
        }
    }
    
    /**
     * Resize an existing image
     *
     * @param string $path
     * @param array $sizes
     * @return array
     */
    public function resizeImage(string $path, array $sizes): array
    {
        if (!Storage::disk('public')->exists($path)) {
            throw new \Exception("Image not found: {$path}");
        }
        
        $images = [];
        
        foreach ($sizes as $sizeName => $dimensions) {
            [$width, $height] = $dimensions;
            
            try {
                // Read the original image
                $fullPath = Storage::disk('public')->path($path);
                $image = Image::read($fullPath);
                
                // Apply resize/fit
                if ($width && $height) {
                    $image->cover($width, $height);
                } elseif ($width) {
                    $image->scale(width: $width);
                } elseif ($height) {
                    $image->scale(height: $height);
                }
                
                // Determine encoder based on file type
                $extension = pathinfo($path, PATHINFO_EXTENSION);
                $encoder = $this->getEncoderByExtension($extension);
                
                // Generate resized filename
                $pathInfo = pathinfo($path);
                $resizedFilename = $sizeName . '_' . $pathInfo['filename'] . '.' . $pathInfo['extension'];
                $resizedPath = $pathInfo['dirname'] . '/' . $resizedFilename;
                
                // Save resized image
                $encodedImage = $image->encode($encoder);
                Storage::disk('public')->put($resizedPath, $encodedImage);
                
                $images[$sizeName] = $resizedPath;
                
            } catch (\Exception $e) {
                Log::error("Failed to resize image {$sizeName}: " . $e->getMessage());
                continue;
            }
        }
        
        return $images;
    }
    
    /**
     * Convert image to different format
     *
     * @param string $path
     * @param string $format
     * @param int|null $quality
     * @return string
     */
    public function convertImageFormat(string $path, string $format = 'webp', ?int $quality = null): string
    {
        if (!Storage::disk('public')->exists($path)) {
            throw new \Exception("Image not found: {$path}");
        }
        
        $fullPath = Storage::disk('public')->path($path);
        $image = Image::read($fullPath);
        
        // Create new filename with different extension
        $pathInfo = pathinfo($path);
        $newFilename = $pathInfo['filename'] . '.' . $format;
        $newPath = $pathInfo['dirname'] . '/' . $newFilename;
        
        // Encode with specified format
        switch (strtolower($format)) {
            case 'jpg':
            case 'jpeg':
                $encoder = new JpegEncoder($quality ?? 90);
                break;
            case 'png':
                $encoder = new PngEncoder($quality ?? 90);
                break;
            case 'webp':
                $encoder = new WebpEncoder($quality ?? 90);
                break;
            default:
                throw new \Exception("Unsupported image format: {$format}");
        }
        
        $encodedImage = $image->encode($encoder);
        Storage::disk('public')->put($newPath, $encodedImage);
        
        return $newPath;
    }
    
    /**
     * Generate unique filename
     *
     * @param UploadedFile $file
     * @param string $path
     * @return string
     */
    private function generateUniqueFilename(UploadedFile $file, string $path): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        
        // Sanitize filename
        $sanitizedName = preg_replace('/[^a-zA-Z0-9-_]/', '-', $originalName);
        $sanitizedName = preg_replace('/-+/', '-', $sanitizedName);
        $sanitizedName = trim($sanitizedName, '-');
        
        // Generate unique filename
        $filename = $sanitizedName . '-' . uniqid() . '.' . $extension;
        
        // Ensure filename is unique in the directory
        $counter = 1;
        while (Storage::disk('public')->exists($path . '/' . $filename)) {
            $filename = $sanitizedName . '-' . uniqid() . '-' . $counter . '.' . $extension;
            $counter++;
        }
        
        return $filename;
    }
    
    /**
     * Get appropriate encoder for the file
     *
     * @param UploadedFile $file
     * @return mixed
     */
    private function getEncoder(UploadedFile $file)
    {
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());
        
        return $this->getEncoderByExtension($extension);
    }
    
    /**
     * Get encoder by file extension
     *
     * @param string $extension
     * @return mixed
     */
    private function getEncoderByExtension(string $extension)
    {
        switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
                return new JpegEncoder(90);
            case 'png':
                return new PngEncoder(90);
            case 'webp':
                return new WebpEncoder(90);
            case 'gif':
                return new \Intervention\Image\Encoders\GifEncoder();
            default:
                return new JpegEncoder(90); // Default to JPEG
        }
    }
    
    /**
     * Get image dimensions
     *
     * @param string $path
     * @return array|null
     */
    public function getImageDimensions(string $path): ?array
    {
        if (!Storage::disk('public')->exists($path)) {
            return null;
        }
        
        try {
            $fullPath = Storage::disk('public')->path($path);
            $image = Image::read($fullPath);
            
            return [
                'width' => $image->width(),
                'height' => $image->height(),
                'ratio' => round($image->width() / $image->height(), 2),
            ];
        } catch (\Exception $e) {
            Log::error("Failed to get image dimensions for {$path}: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get file size in human readable format
     *
     * @param string $path
     * @return string
     */
    public function getFileSize(string $path): string
    {
        if (!Storage::disk('public')->exists($path)) {
            return '0 B';
        }
        
        $bytes = Storage::disk('public')->size($path);
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}