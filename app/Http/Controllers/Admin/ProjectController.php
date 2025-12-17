<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ProjectController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function index(Request $request)
    {
        $query = Project::with(['tags', 'categories']);
        
        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%')
                  ->orWhere('client', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by tag
        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        $projects = $query->latest()->paginate(20);
        $categories = Category::projectType()->get();
        $tags = Tag::projectType()->get();

        return view('admin.projects.index', compact('projects', 'categories', 'tags'));
    }

    public function create()
    {
        $categories = Category::projectType()->get();
        $tags = Tag::projectType()->get();
        $project = new Project();
        
        return view('admin.projects.form', compact('project', 'categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:projects,slug',
            'excerpt' => 'required|string|max:500',
            'description' => 'required|string',
            'image' => 'nullable|image|max:5120', // 5MB for new uploads
            'image_path' => 'nullable|string', // For media library selection
            'gallery.*' => 'nullable|image|max:5120', // New gallery uploads
            'gallery_paths' => 'nullable|array', // Existing paths from media library
            'gallery_paths.*' => 'nullable|string',
            'client' => 'nullable|string|max:255',
            'project_date' => 'nullable|date',
            'project_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:50',
            'status' => 'required|in:draft,published,archived',
            'sort_order' => 'nullable|integer',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Handle main image - priority: new upload > media library selection
        if ($request->hasFile('image')) {
            // Upload new image with multiple sizes
            $sizes = [
                'thumbnail' => [400, 300],
                'medium' => [800, 600],
                'large' => [1200, 800],
            ];
            
            $uploadedImages = $this->fileUploadService->uploadImage(
                $request->file('image'),
                'projects',
                $sizes
            );
            
            $validated['image'] = $uploadedImages['original'];
            $validated['image_variants'] = $uploadedImages; // Store all image variants
            
        } elseif ($request->filled('image_path')) {
            // Use image from media library
            // Check if the image exists in storage
            if (Storage::disk('public')->exists($request->image_path)) {
                $validated['image'] = $request->image_path;
                
                // Create variants for the selected image if needed
                $imagePath = Storage::disk('public')->path($request->image_path);
                $pathInfo = pathinfo($request->image_path);
                
                // Generate variants for the selected image
                $variants = [];
                $variants['original'] = $request->image_path;
                
                $sizes = [
                    'thumbnail' => [400, 300],
                    'medium' => [800, 600],
                    'large' => [1200, 800],
                ];
                
                foreach ($sizes as $sizeName => $dimensions) {
                    $variantPath = $this->createImageVariant(
                        $imagePath,
                        $pathInfo['dirname'] . '/' . $sizeName . '_' . $pathInfo['basename'],
                        $dimensions[0],
                        $dimensions[1]
                    );
                    $variants[$sizeName] = $variantPath;
                }
                
                $validated['image_variants'] = $variants;
            }
        }

        // Handle gallery images - combine new uploads and media library selections
        $gallery = [];
        $galleryVariants = [];
        
        // Process new gallery uploads
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $uploadedImages = $this->fileUploadService->uploadImage(
                    $file,
                    'projects/gallery',
                    ['thumbnail' => [400, 300]]
                );
                
                $gallery[] = $uploadedImages['original'];
                $galleryVariants[] = $uploadedImages;
            }
        }
        
        // Process existing images from media library
        if ($request->filled('gallery_paths')) {
            foreach ($request->gallery_paths as $imagePath) {
                if (Storage::disk('public')->exists($imagePath)) {
                    $gallery[] = $imagePath;
                    
                    // Create thumbnail for gallery image
                    $imageFullPath = Storage::disk('public')->path($imagePath);
                    $pathInfo = pathinfo($imagePath);
                    
                    $thumbnailPath = $this->createImageVariant(
                        $imageFullPath,
                        $pathInfo['dirname'] . '/thumbnail_' . $pathInfo['basename'],
                        400,
                        300
                    );
                    
                    $galleryVariants[] = [
                        'original' => $imagePath,
                        'thumbnail' => $thumbnailPath,
                    ];
                }
            }
        }
        
        if (!empty($gallery)) {
            $validated['gallery'] = $gallery;
            $validated['gallery_variants'] = $galleryVariants;
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Ensure slug is unique
            $count = Project::where('slug', $validated['slug'])->count();
            if ($count > 0) {
                $validated['slug'] = $validated['slug'] . '-' . time();
            }
        }

        $validated['technologies'] = $request->technologies ?? [];

        $project = Project::create($validated);

        // Sync categories and tags
        if ($request->has('categories')) {
            $project->categories()->sync($request->categories);
        }

        if ($request->has('tags')) {
            $project->tags()->sync($request->tags);
        }

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $categories = Category::projectType()->get();
        $tags = Tag::projectType()->get();
        
        return view('admin.projects.form', compact('project', 'categories', 'tags'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:projects,slug,' . $project->id,
            'excerpt' => 'required|string|max:500',
            'description' => 'required|string',
            'image' => 'nullable|image|max:5120',
            'image_path' => 'nullable|string',
            'gallery.*' => 'nullable|image|max:5120',
            'gallery_paths' => 'nullable|array',
            'gallery_paths.*' => 'nullable|string',
            'remove_gallery' => 'nullable|array', // For removing specific gallery items
            'remove_gallery.*' => 'nullable|string',
            'client' => 'nullable|string|max:255',
            'project_date' => 'nullable|date',
            'project_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:50',
            'status' => 'required|in:draft,published,archived',
            'sort_order' => 'nullable|integer',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Handle main image
        $imageChanged = false;
        if ($request->hasFile('image')) {
            // Delete old image and all its variants
            if ($project->image) {
                $this->deleteProjectImageWithVariants($project);
            }
            
            $sizes = [
                'thumbnail' => [400, 300],
                'medium' => [800, 600],
                'large' => [1200, 800],
            ];
            
            $uploadedImages = $this->fileUploadService->uploadImage(
                $request->file('image'),
                'projects',
                $sizes
            );
            
            $validated['image'] = $uploadedImages['original'];
            $validated['image_variants'] = $uploadedImages;
            $imageChanged = true;
            
        } elseif ($request->filled('image_path') && $request->image_path !== $project->image) {
            // Image selected from media library and it's different from current
            if ($project->image) {
                $this->deleteProjectImageWithVariants($project);
            }
            
            if (Storage::disk('public')->exists($request->image_path)) {
                $validated['image'] = $request->image_path;
                
                // Create variants for the selected image
                $imagePath = Storage::disk('public')->path($request->image_path);
                $pathInfo = pathinfo($request->image_path);
                
                $variants = [];
                $variants['original'] = $request->image_path;
                
                $sizes = [
                    'thumbnail' => [400, 300],
                    'medium' => [800, 600],
                    'large' => [1200, 800],
                ];
                
                foreach ($sizes as $sizeName => $dimensions) {
                    $variantPath = $this->createImageVariant(
                        $imagePath,
                        $pathInfo['dirname'] . '/' . $sizeName . '_' . $pathInfo['basename'],
                        $dimensions[0],
                        $dimensions[1]
                    );
                    $variants[$sizeName] = $variantPath;
                }
                
                $validated['image_variants'] = $variants;
                $imageChanged = true;
            }
        } elseif ($request->has('remove_image') && $request->boolean('remove_image')) {
            // Remove image if requested
            if ($project->image) {
                $this->deleteProjectImageWithVariants($project);
                $validated['image'] = null;
                $validated['image_variants'] = null;
                $imageChanged = true;
            }
        }
        
        // If image hasn't changed, keep the existing one
        if (!$imageChanged && $project->image) {
            $validated['image'] = $project->image;
            $validated['image_variants'] = $project->image_variants;
        }

        // Handle gallery images
        $currentGallery = $project->gallery ?? [];
        $currentGalleryVariants = $project->gallery_variants ?? [];
        
        // Remove specified gallery images
        if ($request->filled('remove_gallery')) {
            foreach ($request->remove_gallery as $imagePath) {
                if ($imagePath) { // Skip empty values
                    $key = array_search($imagePath, $currentGallery);
                    if ($key !== false) {
                        // Delete all variants of this image
                        if (isset($currentGalleryVariants[$key])) {
                            foreach ($currentGalleryVariants[$key] as $variantPath) {
                                $this->fileUploadService->deleteImage($variantPath);
                            }
                        }
                        
                        unset($currentGallery[$key]);
                        unset($currentGalleryVariants[$key]);
                    }
                }
            }
            
            // Re-index arrays
            $currentGallery = array_values($currentGallery);
            $currentGalleryVariants = array_values($currentGalleryVariants);
        }
        
        // Add new gallery uploads
        $newGallery = [];
        $newGalleryVariants = [];
        
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $uploadedImages = $this->fileUploadService->uploadImage(
                    $file,
                    'projects/gallery',
                    ['thumbnail' => [400, 300]]
                );
                
                $newGallery[] = $uploadedImages['original'];
                $newGalleryVariants[] = $uploadedImages;
            }
        }
        
        // Add existing images from media library
        if ($request->filled('gallery_paths')) {
            foreach ($request->gallery_paths as $imagePath) {
                // Check if image already exists in gallery
                if (!in_array($imagePath, $currentGallery) && Storage::disk('public')->exists($imagePath)) {
                    $currentGallery[] = $imagePath;
                    
                    // Create thumbnail for gallery image
                    $imageFullPath = Storage::disk('public')->path($imagePath);
                    $pathInfo = pathinfo($imagePath);
                    
                    $thumbnailPath = $this->createImageVariant(
                        $imageFullPath,
                        $pathInfo['dirname'] . '/thumbnail_' . $pathInfo['basename'],
                        400,
                        300
                    );
                    
                    $currentGalleryVariants[] = [
                        'original' => $imagePath,
                        'thumbnail' => $thumbnailPath,
                    ];
                }
            }
        }
        
        // Merge old and new gallery items
        $finalGallery = array_merge($currentGallery, $newGallery);
        $finalGalleryVariants = array_merge($currentGalleryVariants, $newGalleryVariants);
        
        if (!empty($finalGallery)) {
            $validated['gallery'] = $finalGallery;
            $validated['gallery_variants'] = $finalGalleryVariants;
        } else {
            $validated['gallery'] = null;
            $validated['gallery_variants'] = null;
        }

        $validated['technologies'] = $request->technologies ?? [];

        $project->update($validated);

        // Sync categories and tags
        $project->categories()->sync($request->categories ?? []);
        $project->tags()->sync($request->tags ?? []);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        // Delete main image and all its variants
        if ($project->image) {
            $this->deleteProjectImageWithVariants($project);
        }
        
        // Delete gallery images and their variants
        if ($project->gallery && $project->gallery_variants) {
            foreach ($project->gallery_variants as $variants) {
                foreach ($variants as $variantPath) {
                    $this->fileUploadService->deleteImage($variantPath);
                }
            }
        }

        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,publish,draft,archive',
            'ids' => 'required|array',
            'ids.*' => 'exists:projects,id',
        ]);

        $action = $request->action;
        $ids = $request->ids;

        switch ($action) {
            case 'delete':
                // Delete images and variants for each project
                foreach ($ids as $id) {
                    $project = Project::withTrashed()->find($id);
                    if ($project->image) {
                        $this->deleteProjectImageWithVariants($project);
                    }
                    if ($project->gallery && $project->gallery_variants) {
                        foreach ($project->gallery_variants as $variants) {
                            foreach ($variants as $variantPath) {
                                $this->fileUploadService->deleteImage($variantPath);
                            }
                        }
                    }
                }
                
                Project::whereIn('id', $ids)->delete();
                $message = 'Projects deleted successfully.';
                break;
            case 'publish':
                Project::whereIn('id', $ids)->update(['status' => 'published']);
                $message = 'Projects published successfully.';
                break;
            case 'draft':
                Project::whereIn('id', $ids)->update(['status' => 'draft']);
                $message = 'Projects moved to draft.';
                break;
            case 'archive':
                Project::whereIn('id', $ids)->update(['status' => 'archived']);
                $message = 'Projects archived successfully.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Create image variant (resize and save)
     */
    private function createImageVariant(string $sourcePath, string $relativePath, int $width, int $height): string
    {
        try {
            // Create the image using Intervention Image
            $image = Image::make($sourcePath);
            
            // Resize the image while maintaining aspect ratio
            $image->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            
            // Save the resized image
            $fullPath = Storage::disk('public')->path($relativePath);
            $image->save($fullPath);
            
            return $relativePath;
        } catch (\Exception $e) {
            // If variant creation fails, return original path
            Log::error('Failed to create image variant: ' . $e->getMessage());
            return $relativePath;
        }
    }

    /**
     * Delete project image and all its variants
     */
    private function deleteProjectImageWithVariants(Project $project): void
    {
        if ($project->image) {
            // Delete original image
            $this->fileUploadService->deleteImage($project->image);
            
            // Delete all variants
            if ($project->image_variants) {
                foreach ($project->image_variants as $variantPath) {
                    if ($variantPath !== $project->image) { // Don't delete original twice
                        $this->fileUploadService->deleteImage($variantPath);
                    }
                }
            }
        }
        
        // Delete gallery images and variants
        if ($project->gallery_variants) {
            foreach ($project->gallery_variants as $variants) {
                foreach ($variants as $variantPath) {
                    $this->fileUploadService->deleteImage($variantPath);
                }
            }
        }
    }
}