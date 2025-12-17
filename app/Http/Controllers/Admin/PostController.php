<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['categories', 'tags']);
        
        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%');
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

        $posts = $query->latest()->paginate(20);
        $categories = Category::postType()->get();
        $tags = Tag::postType()->get();

        return view('admin.posts.index', compact('posts', 'categories', 'tags'));
    }

    public function create()
    {
        $categories = Category::postType()->get();
        $tags = Tag::postType()->get();
        $post = new Post();
        
        return view('admin.posts.form', compact('post', 'categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:5120', // File upload input
            'image_path' => 'nullable|string', // Media library selection (hidden input)
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'meta.title' => 'nullable|string|max:255',
            'meta.description' => 'nullable|string|max:500',
            'meta.keywords' => 'nullable|string',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Handle featured image - exactly like projects
        if ($request->hasFile('featured_image')) {
            // Upload new image
            $validated['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        } elseif ($request->filled('image_path')) {
            // Use image from media library
            $validated['featured_image'] = $request->image_path;
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Set published_at if publishing
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $validated['is_featured'] = $request->has('is_featured');
        $validated['meta'] = [
            'title' => $request->input('meta.title'),
            'description' => $request->input('meta.description'),
            'keywords' => $request->input('meta.keywords'),
        ];

        $post = Post::create($validated);

        // Sync categories and tags
        if ($request->has('categories')) {
            $post->categories()->sync($request->categories);
        }

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post created successfully.');
    }

    public function show(Post $post)
    {
        return view('admin.posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $categories = Category::postType()->get();
        $tags = Tag::postType()->get();
        
        return view('admin.posts.form', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug,' . $post->id,
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:5120',
            'image_path' => 'nullable|string',
            'remove_image' => 'nullable|boolean', // Changed from remove_featured_image
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'meta.title' => 'nullable|string|max:255',
            'meta.description' => 'nullable|string|max:500',
            'meta.keywords' => 'nullable|string',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Handle featured image - exactly like projects
        $imageChanged = false;
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('posts', 'public');
            $imageChanged = true;
        } elseif ($request->filled('image_path') && $request->image_path !== $post->featured_image) {
            // Image selected from media library
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $validated['featured_image'] = $request->image_path;
            $imageChanged = true;
        } elseif ($request->has('remove_image') && $request->boolean('remove_image')) {
            // Remove image if requested
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $validated['featured_image'] = null;
            $imageChanged = true;
        }
        
        // If image hasn't changed, keep the existing one
        if (!$imageChanged) {
            $validated['featured_image'] = $post->featured_image;
        }

        // Set published_at if publishing from draft
        if ($validated['status'] === 'published' && $post->status === 'draft' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $validated['is_featured'] = $request->has('is_featured');
        $validated['meta'] = [
            'title' => $request->input('meta.title'),
            'description' => $request->input('meta.description'),
            'keywords' => $request->input('meta.keywords'),
        ];

        $post->update($validated);

        // Sync categories and tags
        $post->categories()->sync($request->categories ?? []);
        $post->tags()->sync($request->tags ?? []);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        // Delete featured image
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post deleted successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,publish,draft,archive',
            'ids' => 'required|array',
            'ids.*' => 'exists:posts,id',
        ]);

        $action = $request->action;
        $ids = $request->ids;

        switch ($action) {
            case 'delete':
                Post::whereIn('id', $ids)->delete();
                $message = 'Posts deleted successfully.';
                break;
            case 'publish':
                Post::whereIn('id', $ids)->update([
                    'status' => 'published',
                    'published_at' => now()
                ]);
                $message = 'Posts published successfully.';
                break;
            case 'draft':
                Post::whereIn('id', $ids)->update(['status' => 'draft']);
                $message = 'Posts moved to draft.';
                break;
            case 'archive':
                Post::whereIn('id', $ids)->update(['status' => 'archived']);
                $message = 'Posts archived successfully.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }
}