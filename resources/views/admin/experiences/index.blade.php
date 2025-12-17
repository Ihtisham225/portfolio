<x-admin.layout title="Experiences">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Work Experiences</h1>
                <p class="text-gray-600">Manage your professional work experiences</p>
            </div>
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('admin.experiences.create') }}" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center"
                >
                    <x-admin.icon name="plus" class="w-5 h-5 mr-2" />
                    Add Experience
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form action="{{ route('admin.experiences.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="Search experiences..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
                
                <!-- Current -->
                <div>
                    <label for="current" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select
                        name="current"
                        id="current"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="all" {{ request('current') === 'all' ? 'selected' : '' }}>All Experiences</option>
                        <option value="true" {{ request('current') === 'true' ? 'selected' : '' }}>Current Only</option>
                        <option value="false" {{ request('current') === 'false' ? 'selected' : '' }}>Past Only</option>
                    </select>
                </div>
                
                <!-- Sort By -->
                <div>
                    <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select
                        name="sort_by"
                        id="sort_by"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="sort_order" {{ request('sort_by') === 'sort_order' ? 'selected' : '' }}>Sort Order</option>
                        <option value="start_date_desc" {{ request('sort_by') === 'start_date_desc' ? 'selected' : '' }}>Start Date (Newest)</option>
                        <option value="start_date_asc" {{ request('sort_by') === 'start_date_asc' ? 'selected' : '' }}>Start Date (Oldest)</option>
                        <option value="company" {{ request('sort_by') === 'company' ? 'selected' : '' }}>Company Name</option>
                    </select>
                </div>
                
                <!-- Actions -->
                <div class="flex items-end space-x-2">
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        Filter
                    </button>
                    <a
                        href="{{ route('admin.experiences.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </x-admin.card>

    <x-admin.card>
        <div class="flex items-center justify-between mb-6">
            <div class="text-sm text-gray-600">
                {{ $experiences->total() }} experiences found
            </div>
        </div>

        <!-- Experiences Table -->
        <x-admin.table :headers="['Position', 'Company', 'Duration', 'Location', 'Status', '']" actions>
            @forelse($experiences as $experience)
                <x-admin.table.row>
                    <x-admin.table.cell>
                        <div>
                            <a 
                                href="{{ route('admin.experiences.edit', $experience) }}" 
                                class="font-medium text-gray-900 hover:text-blue-600"
                            >
                                {{ $experience->title }}
                            </a>
                            <p class="text-sm text-gray-500 line-clamp-2 mt-1">
                                {{ Str::limit($experience->description, 80) }}
                            </p>
                        </div>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <div class="font-medium text-gray-900">{{ $experience->company }}</div>
                        <div class="text-sm text-gray-500">{{ $experience->formatted_start_date }} - {{ $experience->formatted_end_date }}</div>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <div class="text-sm text-gray-900">{{ $experience->duration }}</div>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <div class="text-sm text-gray-600">
                            @if($experience->location)
                                <div class="flex items-center">
                                    <x-admin.icon name="location-marker" class="w-4 h-4 mr-1 text-gray-400" />
                                    {{ $experience->location }}
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </div>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        @if($experience->is_current)
                            <x-admin.badge color="green">Current</x-admin.badge>
                        @else
                            <span class="text-sm text-gray-500">Past</span>
                        @endif
                    </x-admin.table.cell>
                    
                    <x-admin.table.actions>
                        <a 
                            href="{{ route('admin.experiences.edit', $experience) }}" 
                            class="text-blue-600 hover:text-blue-800"
                            title="Edit"
                        >
                            <x-admin.icon name="edit" class="w-5 h-5" />
                        </a>
                        <form 
                            action="{{ route('admin.experiences.destroy', $experience) }}" 
                            method="POST" 
                            class="inline"
                            onsubmit="return confirm('Are you sure you want to delete this experience?')"
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

                <!-- Technologies Row -->
                @if(!empty($experience->technologies))
                    <tr class="bg-gray-50">
                        <td colspan="6" class="px-6 py-3">
                            <div class="flex flex-wrap gap-2">
                                @foreach($experience->technologies as $technology)
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">
                                        {{ $technology }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <x-admin.icon name="briefcase" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                        <p class="text-lg font-medium text-gray-900 mb-2">No experiences found</p>
                        <p class="text-gray-600">Get started by adding your first work experience.</p>
                        <a 
                            href="{{ route('admin.experiences.create') }}" 
                            class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        >
                            <x-admin.icon name="plus" class="w-5 h-5 mr-2" />
                            Add Experience
                        </a>
                    </td>
                </tr>
            @endforelse
        </x-admin.table>

        <!-- Pagination -->
        @if($experiences->hasPages())
            <div class="mt-6">
                <x-admin.pagination :paginator="$experiences" />
            </div>
        @endif
    </x-admin.card>
</x-admin.layout>