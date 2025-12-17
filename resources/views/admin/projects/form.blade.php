<x-admin.layout :title="$title ?? ($project->id ? 'Edit Project' : 'Create Project')">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $project->id ? 'Edit Project' : 'Create New Project' }}
                </h1>
                <p class="text-gray-600">
                    {{ $project->id ? 'Update project details' : 'Add a new project to your portfolio' }}
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('admin.projects.index') }}" 
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
        action="{{ $project->id ? route('admin.projects.update', $project) : route('admin.projects.store') }}" 
        method="POST" 
        enctype="multipart/form-data"
    >
        @csrf
        @if($project->id)
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
                                Project Title *
                            </label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title', $project->title) }}"
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
                                value="{{ old('slug', $project->slug) }}"
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
                                Short Description *
                            </label>
                            <textarea
                                id="excerpt"
                                name="excerpt"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >{{ old('excerpt', $project->excerpt) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">A brief summary of the project (max 500 characters)</p>
                            @error('excerpt')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Full Description *
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="8"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >{{ old('description', $project->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Project Details -->
                <x-admin.card title="Project Details">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Client -->
                        <div>
                            <label for="client" class="block text-sm font-medium text-gray-700 mb-1">
                                Client
                            </label>
                            <input
                                type="text"
                                id="client"
                                name="client"
                                value="{{ old('client', $project->client) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            @error('client')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Project Date -->
                        <div>
                            <label for="project_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Project Date
                            </label>
                            <input
                                type="date"
                                id="project_date"
                                name="project_date"
                                value="{{ old('project_date', $project->project_date?->format('Y-m-d')) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            @error('project_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Project URL -->
                        <div>
                            <label for="project_url" class="block text-sm font-medium text-gray-700 mb-1">
                                Live URL
                            </label>
                            <input
                                type="url"
                                id="project_url"
                                name="project_url"
                                value="{{ old('project_url', $project->project_url) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="https://example.com"
                            >
                            @error('project_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- GitHub URL -->
                        <div>
                            <label for="github_url" class="block text-sm font-medium text-gray-700 mb-1">
                                GitHub URL
                            </label>
                            <input
                                type="url"
                                id="github_url"
                                name="github_url"
                                value="{{ old('github_url', $project->github_url) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="https://github.com/username/project"
                            >
                            @error('github_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                                <option value="draft" {{ old('status', $project->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $project->status) === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status', $project->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sort Order -->
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                                Sort Order
                            </label>
                            <input
                                type="number"
                                id="sort_order"
                                name="sort_order"
                                value="{{ old('sort_order', $project->sort_order ?? 0) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            @error('sort_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                                        {{ in_array($category->id, old('categories', $project->categories->pluck('id')->toArray())) ? 'selected' : '' }}
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
                                        {{ in_array($tag->id, old('tags', $project->tags->pluck('id')->toArray())) ? 'selected' : '' }}
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

                <!-- Featured Image -->
                <!-- Featured Image with Media Library Option -->
                <x-admin.card title="Featured Image">
                    <div class="space-y-4">
                        <!-- Current Image -->
                        @if($project->image)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                                <img 
                                    src="{{ Storage::url($project->image) }}" 
                                    alt="{{ $project->title }}"
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
                        <input type="hidden" id="featuredImagePath" name="image_path" value="{{ old('image_path', $project->image) }}">

                        <!-- Image Upload Options -->
                        <div class="space-y-3">
                            <!-- Upload from Computer -->
                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                                    Upload New Image
                                </label>
                                <input
                                    type="file"
                                    id="image"
                                    name="image"
                                    accept="image/*"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    onchange="previewUploadedImage(this)"
                                >
                                <p class="mt-1 text-sm text-gray-500">Recommended size: 1200x800px</p>
                                @error('image')
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
                            @if($project->image)
                                <button
                                    type="button"
                                    onclick="removeFeaturedImage()"
                                    class="w-full px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50"
                                >
                                    Remove Image
                                </button>
                            @endif

                            @if($project->image)
                                <input type="hidden" name="remove_image" value="0" id="removeImageFlag">
                            @endif
                        </div>
                    </div>
                </x-admin.card>

                <!-- Gallery with Media Library Option -->
                <x-admin.card title="Gallery">
                    <div class="space-y-4">
                        <!-- Gallery Preview -->
                        <div id="galleryPreview" class="grid grid-cols-3 gap-2 mb-4">
                            <!-- Existing gallery images -->
                            @if($project->gallery && count($project->gallery) > 0)
                                @foreach($project->gallery as $index => $image)
                                    <div class="relative gallery-item" data-path="{{ $image }}">
                                        <img 
                                            src="{{ Storage::url($image) }}" 
                                            alt="Gallery Image {{ $index + 1 }}"
                                            class="w-full h-20 object-cover rounded-lg"
                                        >
                                        <button
                                            type="button"
                                            onclick="removeGalleryImage(this)"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                        >
                                            <x-admin.icon name="x" class="w-3 h-3" />
                                        </button>
                                        <input type="hidden" name="gallery_paths[]" value="{{ $image }}">
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Hidden inputs for gallery -->
                        <div id="galleryPathsContainer"></div>

                        <!-- Gallery Actions -->
                        <div class="space-y-3">
                            <!-- Upload Multiple Images -->
                            <div>
                                <label for="gallery" class="block text-sm font-medium text-gray-700 mb-1">
                                    Upload Multiple Images
                                </label>
                                <input
                                    type="file"
                                    id="gallery"
                                    name="gallery[]"
                                    accept="image/*"
                                    multiple
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    onchange="handleGalleryUpload(this)"
                                >
                                <p class="mt-1 text-sm text-gray-500">Select multiple images</p>
                                @error('gallery.*')
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
                                    onclick="openMediaLibrary('gallery')"
                                    class="w-full px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:text-blue-600 text-center"
                                >
                                    <div class="flex items-center justify-center space-x-2">
                                        <x-admin.icon name="folder-open" class="w-5 h-5" />
                                        <span>Add from Media Library</span>
                                    </div>
                                </button>
                            </div>

                            <!-- Clear Gallery -->
                            @if($project->gallery && count($project->gallery) > 0)
                                <button
                                    type="button"
                                    onclick="clearGallery()"
                                    class="w-full px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50"
                                >
                                    Clear All Gallery Images
                                </button>
                            @endif
                        </div>
                    </div>
                </x-admin.card>

                <!-- Technologies Component - Fixed Version -->
                <x-admin.card title="Technologies">
                    <div class="space-y-4" x-data="{
                        technologies: {{ json_encode(old('technologies', $project->technologies ?? [])) }},
                        newTech: '',
                        addTechnology() {
                            if (this.newTech.trim() && !this.technologies.includes(this.newTech.trim())) {
                                this.technologies.push(this.newTech.trim());
                                this.newTech = '';
                            }
                        },
                        removeTechnology(index) {
                            this.technologies.splice(index, 1);
                        }
                    }">
                        <!-- Technologies Input -->
                        <div class="flex space-x-2">
                            <input
                                type="text"
                                x-model="newTech"
                                @keydown.enter.prevent="addTechnology()"
                                placeholder="Add technology"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <button
                                type="button"
                                @click="addTechnology()"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                            >
                                Add
                            </button>
                        </div>

                        <!-- Technologies List -->
                        <div class="space-y-2">
                            <template x-for="(tech, index) in technologies" :key="index">
                                <div class="flex items-center justify-between bg-gray-50 px-3 py-2 rounded-lg">
                                    <span x-text="tech" class="text-sm"></span>
                                    <button
                                        type="button"
                                        @click="removeTechnology(index)"
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        <x-admin.icon name="x" class="w-4 h-4" />
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Hidden Inputs -->
                        <template x-for="(tech, index) in technologies" :key="index">
                            <input type="hidden" name="technologies[]" x-bind:value="tech">
                        </template>

                        @error('technologies')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </x-admin.card>

                <!-- Submit Button -->
                <div class="sticky bottom-6">
                    <div class="bg-white p-4 rounded-lg shadow-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            @if($project->id)
                                <a 
                                    href="{{ route('admin.projects.show', $project) }}" 
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
                                {{ $project->id ? 'Update Project' : 'Create Project' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-admin.layout>