<x-admin.layout :title="$title ?? ($category->id ? 'Edit Category' : 'Create Category')">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $category->id ? 'Edit Category' : 'Create New Category' }}
                </h1>
                <p class="text-gray-600">
                    {{ $category->id ? 'Update category details' : 'Add a new category' }}
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('admin.categories.index') }}" 
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                    Cancel
                </a>
            </div>
        </div>
    </x-slot>

    <form 
        action="{{ $category->id ? route('admin.categories.update', $category) : route('admin.categories.store') }}" 
        method="POST"
    >
        @csrf
        @if($category->id)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <x-admin.card title="Basic Information">
                    <div class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Category Name *
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $category->name) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                                oninput="document.getElementById('slug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '')"
                            >
                            @error('name')
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
                                value="{{ old('slug', $category->slug) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                Category Type *
                            </label>
                            <select
                                id="type"
                                name="type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                                <option value="">Select Type</option>
                                <option value="post" {{ old('type', $category->type) === 'post' ? 'selected' : '' }}>Post Category</option>
                                <option value="project" {{ old('type', $category->type) === 'project' ? 'selected' : '' }}>Project Category</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >{{ old('description', $category->description) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">A brief description of this category</p>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Usage Statistics -->
                @if($category->id)
                    <x-admin.card title="Usage Statistics">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($category->type === 'post')
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Total Posts</h3>
                                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $category->post_count }}</p>
                                </div>
                            @elseif($category->type === 'project')
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Total Projects</h3>
                                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $category->project_count }}</p>
                                </div>
                            @endif
                        </div>
                    </x-admin.card>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Settings -->
                <x-admin.card title="Settings">
                    <div class="space-y-6">
                        <!-- Sort Order -->
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                                Sort Order
                            </label>
                            <input
                                type="number"
                                id="sort_order"
                                name="sort_order"
                                value="{{ old('sort_order', $category->sort_order ?? 0) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                            @error('sort_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Warning if category has content -->
                        @if($category->id && (($category->type === 'post' && $category->post_count > 0) || ($category->type === 'project' && $category->project_count > 0)))
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <div class="flex">
                                    <x-admin.icon name="exclamation" class="w-5 h-5 text-yellow-400 mr-2" />
                                    <div>
                                        <p class="text-sm text-yellow-700">
                                            This category has {{ $category->type === 'post' ? $category->post_count . ' posts' : $category->project_count . ' projects' }} assigned.
                                        </p>
                                        @if($category->type === 'post')
                                            <p class="text-sm text-yellow-700 mt-1">
                                                Changing the type to "Project" will remove it from all posts.
                                            </p>
                                        @else
                                            <p class="text-sm text-yellow-700 mt-1">
                                                Changing the type to "Post" will remove it from all projects.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </x-admin.card>

                <!-- Submit Button -->
                <div class="sticky bottom-6">
                    <div class="bg-white p-4 rounded-lg shadow-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            @if($category->id)
                                <form 
                                    action="{{ route('admin.categories.destroy', $category) }}" 
                                    method="POST" 
                                    class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this category?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit" 
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center"
                                    >
                                        <x-admin.icon name="trash" class="w-5 h-5 mr-2" />
                                        Delete
                                    </button>
                                </form>
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
                                {{ $category->id ? 'Update Category' : 'Create Category' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-admin.layout>