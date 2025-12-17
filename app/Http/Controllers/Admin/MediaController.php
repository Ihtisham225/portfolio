<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\FileUploadService;

class MediaController extends Controller
{
    protected $fileUploadService;
    
    // Allowed image extensions
    protected $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp'];
    
    // Allowed document extensions
    protected $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
    
    // Allowed archive extensions
    protected $archiveExtensions = ['zip', 'rar', '7z', 'tar', 'gz'];
    
    // Allowed video extensions
    protected $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv'];
    
    // Allowed audio extensions
    protected $audioExtensions = ['mp3', 'wav', 'ogg', 'm4a'];

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function index(Request $request)
    {
        $disk = Storage::disk('public');
        $directory = $request->get('directory', '/');
        $filter = $request->get('filter', 'all');
        
        // Get directories
        $directories = $disk->directories($directory);
        
        // Get files with filtering
        $allFiles = collect($disk->files($directory));
        
        // Apply filter
        $files = $allFiles->filter(function ($file) use ($filter) {
            if ($filter === 'all') {
                return true;
            }
            
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            
            switch ($filter) {
                case 'images':
                    return in_array($extension, $this->imageExtensions);
                case 'documents':
                    return in_array($extension, $this->documentExtensions);
                case 'archives':
                    return in_array($extension, $this->archiveExtensions);
                case 'videos':
                    return in_array($extension, $this->videoExtensions);
                case 'audio':
                    return in_array($extension, $this->audioExtensions);
                default:
                    return true;
            }
        })->map(function ($file) use ($disk) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $mimeType = $this->getMimeType($extension);
            
            return [
                'name' => basename($file),
                'path' => $file,
                'url' => Storage::url($file),
                'size' => $this->formatBytes($disk->size($file)),
                'size_bytes' => $disk->size($file),
                'mime_type' => $mimeType,
                'extension' => $extension,
                'type' => $this->getFileType($extension),
                'last_modified' => date('Y-m-d H:i:s', $disk->lastModified($file)),
                'icon' => $this->getFileIcon($extension),
                'is_image' => in_array($extension, $this->imageExtensions),
                'thumbnail' => in_array($extension, $this->imageExtensions) ? Storage::url($file) : null,
            ];
        });

        // Sort files by type and name
        $files = $files->sortBy('type')->values();

        // Build breadcrumbs
        $breadcrumbs = [];
        $parts = explode('/', trim($directory, '/'));
        $currentPath = '';
        foreach ($parts as $part) {
            $currentPath .= '/' . $part;
            $breadcrumbs[] = [
                'name' => $part ?: 'Home',
                'path' => $currentPath ?: '/',
            ];
        }

        // Get storage statistics
        $storageStats = $this->getStorageStatistics();

        return view('admin.media.index', compact(
            'directories', 
            'files', 
            'directory', 
            'breadcrumbs',
            'filter',
            'storageStats'
        ));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240', // 10MB max
            'directory' => 'nullable|string',
            'create_thumbnails' => 'nullable|boolean',
        ]);

        $directory = $request->directory ?: 'uploads';
        $createThumbnails = $request->boolean('create_thumbnails', false);
        $uploadedFiles = [];

        foreach ($request->file('files') as $file) {
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            
            // Generate unique filename
            $filename = Str::slug($originalName) . '-' . time() . '.' . $extension;
            
            // Check if it's an image and create thumbnails if requested
            if (in_array(strtolower($extension), $this->imageExtensions) && $createThumbnails) {
                // Upload with multiple sizes
                $sizes = [
                    'thumbnail' => [150, 150],
                    'medium' => [300, 300],
                    'large' => [800, 600],
                ];
                
                $uploadedImages = $this->fileUploadService->uploadImage(
                    $file,
                    $directory,
                    $sizes
                );
                
                $uploadedFiles[] = [
                    'original' => Storage::url($uploadedImages['original']),
                    'thumbnail' => Storage::url($uploadedImages['thumbnail']),
                    'medium' => Storage::url($uploadedImages['medium']),
                    'name' => $filename,
                    'path' => $uploadedImages['original'],
                ];
            } else {
                // Regular file upload
                $path = $file->storeAs($directory, $filename, 'public');
                $uploadedFiles[] = [
                    'original' => Storage::url($path),
                    'thumbnail' => in_array(strtolower($extension), $this->imageExtensions) 
                        ? Storage::url($path) 
                        : null,
                    'name' => $filename,
                    'path' => $path,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'files' => $uploadedFiles,
            'message' => 'Files uploaded successfully.',
        ]);
    }

    public function createDirectory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9-_ ]+$/',
            'directory' => 'nullable|string',
        ]);

        $path = $request->directory ? $request->directory . '/' . $request->name : $request->name;
        
        // Check if directory already exists
        if (Storage::disk('public')->exists($path)) {
            return redirect()->back()
                ->with('error', 'Directory already exists.');
        }
        
        Storage::disk('public')->makeDirectory($path);

        return redirect()->back()
            ->with('success', 'Directory created successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'type' => 'required|in:file,directory',
        ]);

        if ($request->type === 'file') {
            // Check if it's an image with variants
            $extension = strtolower(pathinfo($request->path, PATHINFO_EXTENSION));
            
            if (in_array($extension, $this->imageExtensions)) {
                // Try to delete image variants
                $this->deleteImageVariants($request->path);
            }
            
            Storage::disk('public')->delete($request->path);
            $message = 'File deleted successfully.';
        } else {
            // Check if directory is empty
            $files = Storage::disk('public')->files($request->path);
            $directories = Storage::disk('public')->directories($request->path);
            
            if (count($files) > 0 || count($directories) > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete non-empty directory. Please delete all files first.');
            }
            
            Storage::disk('public')->deleteDirectory($request->path);
            $message = 'Directory deleted successfully.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.path' => 'required|string',
            'items.*.type' => 'required|in:file,directory',
        ]);

        $deletedCount = 0;
        $errors = [];

        foreach ($request->items as $item) {
            try {
                if ($item['type'] === 'file') {
                    // Check if it's an image with variants
                    $extension = strtolower(pathinfo($item['path'], PATHINFO_EXTENSION));
                    
                    if (in_array($extension, $this->imageExtensions)) {
                        $this->deleteImageVariants($item['path']);
                    }
                    
                    Storage::disk('public')->delete($item['path']);
                } else {
                    // Check if directory is empty
                    $files = Storage::disk('public')->files($item['path']);
                    $directories = Storage::disk('public')->directories($item['path']);
                    
                    if (count($files) === 0 && count($directories) === 0) {
                        Storage::disk('public')->deleteDirectory($item['path']);
                    } else {
                        $errors[] = "Directory '{$item['path']}' is not empty.";
                        continue;
                    }
                }
                
                $deletedCount++;
            } catch (\Exception $e) {
                $errors[] = "Error deleting '{$item['path']}': " . $e->getMessage();
            }
        }

        $response = [
            'success' => true,
            'message' => "Deleted {$deletedCount} items successfully.",
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
            $response['message'] .= ' Some items could not be deleted.';
        }

        return response()->json($response);
    }

    public function rename(Request $request)
    {
        $request->validate([
            'old_path' => 'required|string',
            'new_name' => 'required|string|max:255|regex:/^[a-zA-Z0-9-_. ]+$/',
            'type' => 'required|in:file,directory',
        ]);

        $oldPath = $request->old_path;
        $directory = dirname($oldPath);
        $newPath = $directory . '/' . $request->new_name;

        // Check if new path already exists
        if (Storage::disk('public')->exists($newPath)) {
            return response()->json([
                'success' => false,
                'message' => 'A file or directory with this name already exists.',
            ], 422);
        }

        try {
            Storage::disk('public')->move($oldPath, $newPath);
            
            // If it's an image, rename variants too
            if ($request->type === 'file') {
                $extension = strtolower(pathinfo($oldPath, PATHINFO_EXTENSION));
                if (in_array($extension, $this->imageExtensions)) {
                    $this->renameImageVariants($oldPath, $newPath);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Renamed successfully.',
                'new_path' => $newPath,
                'new_url' => Storage::url($newPath),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error renaming: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getFileInfo(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $disk = Storage::disk('public');
        
        if (!$disk->exists($request->path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found.',
            ], 404);
        }

        $extension = strtolower(pathinfo($request->path, PATHINFO_EXTENSION));
        $mimeType = $this->getMimeType($extension);
        
        $info = [
            'name' => basename($request->path),
            'path' => $request->path,
            'url' => Storage::url($request->path),
            'size' => $this->formatBytes($disk->size($request->path)),
            'size_bytes' => $disk->size($request->path),
            'mime_type' => $mimeType,
            'extension' => $extension,
            'type' => $this->getFileType($extension),
            'last_modified' => date('Y-m-d H:i:s', $disk->lastModified($request->path)),
            'created_at' => date('Y-m-d H:i:s', $disk->lastModified($request->path)), // Note: Laravel doesn't store creation time
            'is_image' => in_array($extension, $this->imageExtensions),
            'dimensions' => null,
        ];

        // Get image dimensions if it's an image
        if (in_array($extension, $this->imageExtensions)) {
            try {
                $imagePath = Storage::disk('public')->path($request->path);
                list($width, $height) = getimagesize($imagePath);
                $info['dimensions'] = [
                    'width' => $width,
                    'height' => $height,
                    'ratio' => round($width / $height, 2),
                ];
            } catch (\Exception $e) {
                // Could not get image dimensions
            }
        }

        return response()->json([
            'success' => true,
            'data' => $info,
        ]);
    }

    /**
     * Delete image variants based on the original image path
     */
    private function deleteImageVariants(string $originalPath): void
    {
        $pathInfo = pathinfo($originalPath);
        $dirname = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'];
        
        // Common variant patterns
        $variantPatterns = [
            'thumbnail_' . $filename . '.' . $extension,
            'medium_' . $filename . '.' . $extension,
            'large_' . $filename . '.' . $extension,
            'small_' . $filename . '.' . $extension,
            $filename . '_thumbnail.' . $extension,
            $filename . '_medium.' . $extension,
            $filename . '_large.' . $extension,
            $filename . '_small.' . $extension,
        ];
        
        foreach ($variantPatterns as $variant) {
            $variantPath = $dirname . '/' . $variant;
            if (Storage::disk('public')->exists($variantPath)) {
                Storage::disk('public')->delete($variantPath);
            }
        }
    }

    /**
     * Rename image variants when the original is renamed
     */
    private function renameImageVariants(string $oldPath, string $newPath): void
    {
        $oldInfo = pathinfo($oldPath);
        $newInfo = pathinfo($newPath);
        
        $oldDirname = $oldInfo['dirname'];
        $oldFilename = $oldInfo['filename'];
        $extension = $oldInfo['extension'];
        
        $newDirname = $newInfo['dirname'];
        $newFilename = $newInfo['filename'];
        
        // Common variant patterns to check
        $patterns = [
            'thumbnail_',
            'medium_',
            'large_',
            'small_',
        ];
        
        foreach ($patterns as $prefix) {
            $oldVariant = $oldDirname . '/' . $prefix . $oldFilename . '.' . $extension;
            $newVariant = $newDirname . '/' . $prefix . $newFilename . '.' . $extension;
            
            if (Storage::disk('public')->exists($oldVariant)) {
                Storage::disk('public')->move($oldVariant, $newVariant);
            }
        }
    }

    /**
     * Get mime type based on file extension
     */
    private function getMimeType(string $extension): string
    {
        $mimeTypes = [
            // Images
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            'bmp' => 'image/bmp',
            
            // Documents
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt' => 'text/plain',
            
            // Archives
            'zip' => 'application/zip',
            'rar' => 'application/vnd.rar',
            '7z' => 'application/x-7z-compressed',
            'tar' => 'application/x-tar',
            'gz' => 'application/gzip',
            
            // Videos
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
            'wmv' => 'video/x-ms-wmv',
            'flv' => 'video/x-flv',
            'mkv' => 'video/x-matroska',
            
            // Audio
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'm4a' => 'audio/mp4',
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }

    /**
     * Get file type category
     */
    private function getFileType(string $extension): string
    {
        if (in_array($extension, $this->imageExtensions)) {
            return 'image';
        } elseif (in_array($extension, $this->documentExtensions)) {
            return 'document';
        } elseif (in_array($extension, $this->archiveExtensions)) {
            return 'archive';
        } elseif (in_array($extension, $this->videoExtensions)) {
            return 'video';
        } elseif (in_array($extension, $this->audioExtensions)) {
            return 'audio';
        } else {
            return 'other';
        }
    }

    /**
     * Get file icon based on extension
     */
    private function getFileIcon(string $extension): string
    {
        $icons = [
            'image' => 'fa-file-image',
            'pdf' => 'fa-file-pdf',
            'word' => 'fa-file-word',
            'excel' => 'fa-file-excel',
            'powerpoint' => 'fa-file-powerpoint',
            'archive' => 'fa-file-archive',
            'video' => 'fa-file-video',
            'audio' => 'fa-file-audio',
            'text' => 'fa-file-alt',
            'default' => 'fa-file',
        ];

        if (in_array($extension, $this->imageExtensions)) {
            return $icons['image'];
        } elseif ($extension === 'pdf') {
            return $icons['pdf'];
        } elseif (in_array($extension, ['doc', 'docx'])) {
            return $icons['word'];
        } elseif (in_array($extension, ['xls', 'xlsx'])) {
            return $icons['excel'];
        } elseif (in_array($extension, ['ppt', 'pptx'])) {
            return $icons['powerpoint'];
        } elseif (in_array($extension, $this->archiveExtensions)) {
            return $icons['archive'];
        } elseif (in_array($extension, $this->videoExtensions)) {
            return $icons['video'];
        } elseif (in_array($extension, $this->audioExtensions)) {
            return $icons['audio'];
        } elseif ($extension === 'txt') {
            return $icons['text'];
        } else {
            return $icons['default'];
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Get storage statistics
     */
    private function getStorageStatistics(): array
    {
        $totalSpace = disk_total_space(storage_path('app/public'));
        $freeSpace = disk_free_space(storage_path('app/public'));
        $usedSpace = $totalSpace - $freeSpace;
        $usedPercentage = $totalSpace > 0 ? ($usedSpace / $totalSpace) * 100 : 0;
        
        return [
            'total' => $this->formatBytes($totalSpace),
            'total_bytes' => $totalSpace,
            'used' => $this->formatBytes($usedSpace),
            'used_bytes' => $usedSpace,
            'free' => $this->formatBytes($freeSpace),
            'free_bytes' => $freeSpace,
            'used_percentage' => round($usedPercentage, 2),
            'free_percentage' => round(100 - $usedPercentage, 2),
        ];
    }

    // simple view for selecting images for gallery
    public function simpleIndex(Request $request)
    {
        try {
            $disk = Storage::disk('public');
            $directory = $request->get('directory', '/projects');
            $search = $request->get('search', '');
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 24);
            
            // Get all image files
            $allFiles = collect($disk->files($directory))
                ->filter(function ($file) {
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    return in_array($extension, $this->imageExtensions);
                })
                ->map(function ($file) use ($disk) {
                    return [
                        'name' => basename($file),
                        'path' => $file,
                        'url' => Storage::url($file),
                        'size' => $this->formatBytes($disk->size($file)),
                        'size_bytes' => $disk->size($file),
                        'is_image' => true,
                        'last_modified' => date('Y-m-d H:i:s', $disk->lastModified($file)),
                    ];
                });
            
            // Apply search filter if provided
            if ($search) {
                $allFiles = $allFiles->filter(function ($file) use ($search) {
                    return stripos($file['name'], $search) !== false;
                });
            }
            
            // Sort by last modified (newest first)
            $allFiles = $allFiles->sortByDesc('last_modified')->values();
            
            // Paginate results
            $total = $allFiles->count();
            $offset = ($page - 1) * $perPage;
            $files = $allFiles->slice($offset, $perPage);
            
            // Get directories for navigation
            $directories = $disk->directories($directory);
            
            // Build breadcrumbs
            $breadcrumbs = [];
            $parts = explode('/', trim($directory, '/'));
            $currentPath = '';
            foreach ($parts as $part) {
                $currentPath .= '/' . $part;
                $breadcrumbs[] = [
                    'name' => $part ?: 'Home',
                    'path' => $currentPath ?: '/',
                ];
            }
            
            $data = [
                'success' => true,
                'files' => $files,
                'directories' => $directories,
                'directory' => $directory,
                'breadcrumbs' => $breadcrumbs,
                'search' => $search,
                'pagination' => [
                    'current_page' => (int)$page,
                    'per_page' => (int)$perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage),
                ]
            ];
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading media library: ' . $e->getMessage(),
                'files' => [],
                'directories' => [],
                'directory' => '/',
                'breadcrumbs' => [],
                'search' => '',
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 24,
                    'total' => 0,
                    'last_page' => 0,
                ]
            ], 500);
        }
    }
}