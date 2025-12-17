<x-admin.layout title="Tags">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tags</h1>
                <p class="text-gray-600">Manage your blog post and project tags</p>
            </div>
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('admin.tags.create') }}" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center"
                >
                    <x-admin.icon name="plus" class="w-5 h-5 mr-2" />
                    New Tag
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form action="{{ route('admin.tags.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="Search tags..."
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
                        <option value="post" {{ request('type') === 'post' ? 'selected' : '' }}>Post Tags</option>
                        <option value="project" {{ request('type') === 'project' ? 'selected' : '' }}>Project Tags</option>
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
                        href="{{ route('admin.tags.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </x-admin.card>

    <!-- Tags Table -->
    <x-admin.card>
        <div class="flex items-center justify-between mb-6">
            <div class="text-sm text-gray-600">
                {{ $tags->total() }} tags found
            </div>
        </div>

        <x-admin.table :headers="['Name', 'Slug', 'Type', 'Posts', 'Projects', 'Total', '']" actions>
            @forelse($tags as $tag)
                <x-admin.table.row>
                    <x-admin.table.cell>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 mr-3 bg-gray-100 rounded-lg flex items-center justify-center">
                                <x-admin.icon name="tag" class="w-5 h-5 text-gray-500" />
                            </div>
                            <div>
                                <a 
                                    href="{{ route('admin.tags.edit', $tag) }}" 
                                    class="font-medium text-gray-900 hover:text-blue-600"
                                >
                                    {{ $tag->name }}
                                </a>
                            </div>
                        </div>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $tag->slug }}</code>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <x-admin.badge :color="$tag->type === 'post' ? 'blue' : 'purple'">
                            {{ ucfirst($tag->type) }}
                        </x-admin.badge>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        @if($tag->type === 'post')
                            <div class="text-sm">
                                <span class="text-gray-900 font-medium">{{ $tag->posts_count }}</span>
                                <span class="text-gray-500">posts</span>
                            </div>
                        @else
                            <div class="text-sm text-gray-400">—</div>
                        @endif
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        @if($tag->type === 'project')
                            <div class="text-sm">
                                <span class="text-gray-900 font-medium">{{ $tag->projects_count }}</span>
                                <span class="text-gray-500">projects</span>
                            </div>
                        @else
                            <div class="text-sm text-gray-400">—</div>
                        @endif
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <div class="text-sm">
                            <span class="text-gray-900 font-medium">{{ $tag->posts_count + $tag->projects_count }}</span>
                            <span class="text-gray-500">total</span>
                        </div>
                    </x-admin.table.cell>
                    
                    <x-admin.table.actions>
                        <a 
                            href="{{ route('admin.tags.edit', $tag) }}" 
                            class="text-blue-600 hover:text-blue-800"
                            title="Edit"
                        >
                            <x-admin.icon name="edit" class="w-5 h-5" />
                        </a>
                        <form 
                            action="{{ route('admin.tags.destroy', $tag) }}" 
                            method="POST" 
                            class="inline"
                            onsubmit="return confirm('Are you sure you want to delete this tag? This cannot be undone.')"
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
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        <x-admin.icon name="tag" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                        <p class="text-lg font-medium text-gray-900 mb-2">No tags found</p>
                        <p class="text-gray-600">Get started by creating your first tag.</p>
                        <a 
                            href="{{ route('admin.tags.create') }}" 
                            class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        >
                            <x-admin.icon name="plus" class="w-5 h-5 mr-2" />
                            Create Tag
                        </a>
                    </td>
                </tr>
            @endforelse
        </x-admin.table>

        <!-- Pagination -->
        @if($tags->hasPages())
            <div class="mt-6">
                <x-admin.pagination :paginator="$tags" />
            </div>
        @endif
    </x-admin.card>

    <!-- Popular Tags -->
    @php
        $popularTags = \App\Models\Tag::withCount(['posts', 'projects'])
            ->orderByRaw('posts_count + projects_count DESC')
            ->limit(10)
            ->get();
    @endphp
    
    @if($popularTags->isNotEmpty())
        <x-admin.card title="Popular Tags" class="mt-6">
            <div class="flex flex-wrap gap-2">
                @foreach($popularTags as $popularTag)
                    <a 
                        href="{{ route('admin.tags.edit', $popularTag) }}" 
                        class="inline-flex items-center px-3 py-1.5 rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200"
                    >
                        <x-admin.icon name="tag" class="w-4 h-4 mr-1" />
                        <span class="text-sm font-medium">{{ $popularTag->name }}</span>
                        <span class="ml-2 text-xs bg-gray-300 text-gray-700 px-1.5 py-0.5 rounded-full">
                            {{ $popularTag->posts_count + $popularTag->projects_count }}
                        </span>
                    </a>
                @endforeach
            </div>
        </x-admin.card>
    @endif
</x-admin.layout>