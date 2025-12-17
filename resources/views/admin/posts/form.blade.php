<x-admin.layout :title="$title ?? ($post->id ? 'Edit Post' : 'Create Post')">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $post->id ? 'Edit Post' : 'Create New Post' }}
                </h1>
                <p class="text-gray-600">
                    {{ $post->id ? 'Update post details' : 'Add a new blog post' }}
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('admin.posts.index') }}" 
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                    Cancel
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Media Library Modal - AJAX Enhanced -->
    <div id="mediaLibraryModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Select from Media Library
                                </h3>
                                <div class="flex items-center space-x-2">
                                    <!-- Search -->
                                    <div class="relative">
                                        <input
                                            type="text"
                                            id="mediaSearch"
                                            placeholder="Search images..."
                                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Breadcrumbs -->
                            <div id="mediaBreadcrumbs" class="mb-4 flex items-center space-x-2 text-sm">
                                <!-- Breadcrumbs will be loaded here -->
                            </div>
                            
                            <!-- Media content area -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <!-- Loading indicator -->
                                <div id="mediaLoading" class="text-center py-8 hidden">
                                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                                    <p class="mt-2 text-gray-600">Loading images...</p>
                                </div>
                                
                                <!-- Media grid -->
                                <div id="mediaLibraryContent" class="max-h-96 overflow-y-auto">
                                    <!-- Media will be loaded here -->
                                </div>
                                
                                <!-- Pagination -->
                                <div id="mediaPagination" class="mt-4 flex items-center justify-between border-t border-gray-200 pt-4 hidden">
                                    <!-- Pagination will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button
                        type="button"
                        id="closeMediaModal"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form 
        action="{{ $post->id ? route('admin.posts.update', $post) : route('admin.posts.store') }}" 
        method="POST" 
        enctype="multipart/form-data"
    >
        @csrf
        @if($post->id)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <x-admin.card title="Basic Information">
                    <div class="space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                Post Title *
                            </label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title', $post->title) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                                oninput="document.getElementById('slug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '')"
                            >
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                                URL Slug *
                            </label>
                            <input
                                type="text"
                                id="slug"
                                name="slug"
                                value="{{ old('slug', $post->slug) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Excerpt -->
                        <div>
                            <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-1">
                                Excerpt *
                            </label>
                            <textarea
                                id="excerpt"
                                name="excerpt"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >{{ old('excerpt', $post->excerpt) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">A brief summary of the post (max 500 characters)</p>
                            @error('excerpt')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                                Content *
                            </label>
                            <textarea
                                id="content"
                                name="content"
                                rows="15"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >{{ old('content', $post->content) }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- SEO Information -->
                <x-admin.card title="SEO Information">
                    <div class="space-y-6">
                        <!-- Meta Title -->
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">
                                Meta Title
                            </label>
                            <input
                                type="text"
                                id="meta_title"
                                name="meta[title]"
                                value="{{ old('meta.title', $post->meta_title ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="SEO title for search engines"
                            >
                            <p class="mt-1 text-sm text-gray-500">If empty, post title will be used</p>
                        </div>

                        <!-- Meta Description -->
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">
                                Meta Description
                            </label>
                            <textarea
                                id="meta_description"
                                name="meta[description]"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="SEO description for search engines"
                            >{{ old('meta.description', $post->meta_description ?? '') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">If empty, post excerpt will be used</p>
                        </div>

                        <!-- Meta Keywords -->
                        <div>
                            <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">
                                Meta Keywords
                            </label>
                            <input
                                type="text"
                                id="meta_keywords"
                                name="meta[keywords]"
                                value="{{ old('meta.keywords', is_array($post->meta_keywords) ? implode(', ', $post->meta_keywords) : $post->meta_keywords) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="keyword1, keyword2, keyword3"
                            >
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status & Settings -->
                <x-admin.card title="Settings">
                    <div class="space-y-6">
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                Status *
                            </label>
                            <select
                                id="status"
                                name="status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                                <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status', $post->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Published At -->
                        <div>
                            <label for="published_at" class="block text-sm font-medium text-gray-700 mb-1">
                                Publish Date & Time
                            </label>
                            <input
                                type="datetime-local"
                                id="published_at"
                                name="published_at"
                                value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <p class="mt-1 text-sm text-gray-500">Leave empty to publish immediately</p>
                            @error('published_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Featured -->
                        <div>
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="is_featured"
                                    name="is_featured"
                                    value="1"
                                    {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                                <span class="ml-2 text-sm text-gray-700">Mark as featured post</span>
                            </label>
                        </div>

                        <!-- Categories -->
                        <div>
                            <label for="categories" class="block text-sm font-medium text-gray-700 mb-1">
                                Categories
                            </label>
                            <select
                                id="categories"
                                name="categories[]"
                                multiple
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                @foreach($categories as $category)
                                    <option 
                                        value="{{ $category->id }}"
                                        {{ in_array($category->id, old('categories', $post->categories->pluck('id')->toArray())) ? 'selected' : '' }}
                                    >
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categories')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tags -->
                        <div>
                            <label for="tags" class="block text-sm font-medium text-gray-700 mb-1">
                                Tags
                            </label>
                            <select
                                id="tags"
                                name="tags[]"
                                multiple
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                @foreach($tags as $tag)
                                    <option 
                                        value="{{ $tag->id }}"
                                        {{ in_array($tag->id, old('tags', $post->tags->pluck('id')->toArray())) ? 'selected' : '' }}
                                    >
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tags')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Featured Image with Media Library Option -->
                <x-admin.card title="Featured Image">
                    <div class="space-y-4">
                        <!-- Current Image -->
                        @if($post->featured_image)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                                <img 
                                    src="{{ Storage::url($post->featured_image) }}" 
                                    alt="{{ $post->title }}"
                                    class="w-full h-48 object-cover rounded-lg"
                                    id="featuredImagePreview"
                                >
                            </div>
                        @else
                            <div class="mb-4">
                                <div class="w-full h-48 bg-gray-100 rounded-lg flex items-center justify-center" id="featuredImagePreview">
                                    <span class="text-gray-400">No image selected</span>
                                </div>
                            </div>
                        @endif

                        <!-- Hidden input for image path -->
                        <input type="hidden" id="featuredImagePath" name="featured_image_path" value="{{ old('featured_image_path', $post->featured_image) }}">

                        <!-- Image Upload Options -->
                        <div class="space-y-3">
                            <!-- Upload from Computer -->
                            <div>
                                <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-1">
                                    Upload New Image
                                </label>
                                <input
                                    type="file"
                                    id="featured_image"
                                    name="featured_image"
                                    accept="image/*"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    onchange="previewUploadedImage(this)"
                                >
                                <p class="mt-1 text-sm text-gray-500">Recommended size: 1200x630px</p>
                                @error('featured_image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- OR separator -->
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-300"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-2 bg-white text-gray-500">OR</span>
                                </div>
                            </div>

                            <!-- Select from Media Library -->
                            <div>
                                <button
                                    type="button"
                                    onclick="openMediaLibrary('featured')"
                                    class="w-full px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:text-blue-600 text-center"
                                >
                                    <div class="flex items-center justify-center space-x-2">
                                        <x-admin.icon name="folder-open" class="w-5 h-5" />
                                        <span>Select from Media Library</span>
                                    </div>
                                </button>
                            </div>

                            <!-- Remove Image Button -->
                            @if($post->featured_image)
                                <button
                                    type="button"
                                    onclick="removeFeaturedImage()"
                                    class="w-full px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50"
                                >
                                    Remove Image
                                </button>
                            @endif

                            @if($post->featured_image)
                                <input type="hidden" name="remove_featured_image" value="0" id="removeFeaturedImageFlag">
                            @endif
                        </div>
                    </div>
                </x-admin.card>

                <!-- Submit Button -->
                <div class="sticky bottom-6">
                    <div class="bg-white p-4 rounded-lg shadow-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            @if($post->id)
                                <a 
                                    href="{{ route('admin.posts.show', $post) }}" 
                                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                                >
                                    Preview
                                </a>
                            @else
                                <button
                                    type="button"
                                    onclick="window.history.back()"
                                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                                >
                                    Cancel
                                </button>
                            @endif
                            
                            <button
                                type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium"
                            >
                                {{ $post->id ? 'Update Post' : 'Create Post' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-admin.layout>