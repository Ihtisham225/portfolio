<x-admin.layout title="Categories">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
                <p class="text-gray-600">Manage your blog post and project categories</p>
            </div>
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('admin.categories.create') }}" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center"
                >
                    <x-admin.icon name="plus" class="w-5 h-5 mr-2" />
                    New Category
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form action="{{ route('admin.categories.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="Search categories..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
                
                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select
                        name="type"
                        id="type"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">All Types</option>
                        <option value="post" {{ request('type') === 'post' ? 'selected' : '' }}>Post Categories</option>
                        <option value="project" {{ request('type') === 'project' ? 'selected' : '' }}>Project Categories</option>
                    </select>
                </div>
                
                <!-- Actions -->
                <div class="flex items-end space-x-2 col-span-2">
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        Filter
                    </button>
                    <a
                        href="{{ route('admin.categories.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </x-admin.card>

    <!-- Categories Table -->
    <x-admin.card>
        <div class="flex items-center justify-between mb-6">
            <div class="text-sm text-gray-600">
                {{ $categories->total() }} categories found
            </div>
        </div>

        <x-admin.table :headers="['Name', 'Slug', 'Type', 'Usage', 'Sort Order', '']" actions>
            @forelse($categories as $category)
                <x-admin.table.row>
                    <x-admin.table.cell>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 mr-3 bg-gray-100 rounded-lg flex items-center justify-center">
                                <x-admin.icon name="folder" class="w-5 h-5 text-gray-500" />
                            </div>
                            <div>
                                <a 
                                    href="{{ route('admin.categories.edit', $category) }}" 
                                    class="font-medium text-gray-900 hover:text-blue-600"
                                >
                                    {{ $category->name }}
                                </a>
                                @if($category->description)
                                    <p class="text-sm text-gray-500 mt-1">{{ Str::limit($category->description, 60) }}</p>
                                @endif
                            </div>
                        </div>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $category->slug }}</code>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <x-admin.badge :color="$category->type === 'post' ? 'blue' : 'purple'">
                            {{ ucfirst($category->type) }}
                        </x-admin.badge>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <div class="text-sm">
                            @if($category->type === 'post')
                                <span class="text-gray-900">{{ $category->post_count }}</span>
                                <span class="text-gray-500">posts</span>
                            @else
                                <span class="text-gray-900">{{ $category->project_count }}</span>
                                <span class="text-gray-500">projects</span>
                            @endif
                        </div>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <div class="text-sm text-gray-900">{{ $category->sort_order ?? 0 }}</div>
                    </x-admin.table.cell>
                    
                    <x-admin.table.actions>
                        <a 
                            href="{{ route('admin.categories.edit', $category) }}" 
                            class="text-blue-600 hover:text-blue-800"
                            title="Edit"
                        >
                            <x-admin.icon name="edit" class="w-5 h-5" />
                        </a>
                        <form 
                            action="{{ route('admin.categories.destroy', $category) }}" 
                            method="POST" 
                            class="inline"
                            onsubmit="return confirm('Are you sure you want to delete this category? This cannot be undone.')"
                        >
                            @csrf
                            @method('DELETE')
                            <button 
                                type="submit" 
                                class="text-red-600 hover:text-red-800"
                                title="Delete"
                            >
                                <x-admin.icon name="trash" class="w-5 h-5" />
                            </button>
                        </form>
                    </x-admin.table.actions>
                </x-admin.table.row>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <x-admin.icon name="folder-open" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                        <p class="text-lg font-medium text-gray-900 mb-2">No categories found</p>
                        <p class="text-gray-600">Get started by creating your first category.</p>
                        <a 
                            href="{{ route('admin.categories.create') }}" 
                            class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        >
                            <x-admin.icon name="plus" class="w-5 h-5 mr-2" />
                            Create Category
                        </a>
                    </td>
                </tr>
            @endforelse
        </x-admin.table>

        <!-- Pagination -->
        @if($categories->hasPages())
            <div class="mt-6">
                <x-admin.pagination :paginator="$categories" />
            </div>
        @endif
    </x-admin.card>
</x-admin.layout>