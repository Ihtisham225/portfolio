<x-admin.layout title="Projects">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Projects</h1>
                <p class="text-gray-600">Manage your portfolio projects</p>
            </div>
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('admin.projects.create') }}" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center"
                >
                    <x-admin.icon name="plus" class="w-5 h-5 mr-2" />
                    New Project
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form action="{{ route('admin.projects.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="Search projects..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
                
                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select
                        name="status"
                        id="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                
                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select
                        name="category"
                        id="category"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option 
                                value="{{ $category->slug }}" 
                                {{ request('category') === $category->slug ? 'selected' : '' }}
                            >
                                {{ $category->name }}
                            </option>
                        @endforeach
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
                        href="{{ route('admin.projects.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </x-admin.card>

    <!-- Bulk Actions -->
    <form id="bulkForm" action="{{ route('admin.projects.bulk-action') }}" method="POST">
        @csrf
        <input type="hidden" name="ids" id="bulkIds">
        <input type="hidden" name="action" id="bulkAction">
    </form>

    <x-admin.card>
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <select
                    id="bulkSelectAction"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    onchange="handleBulkAction()"
                >
                    <option value="">Bulk Actions</option>
                    <option value="publish">Publish</option>
                    <option value="draft">Move to Draft</option>
                    <option value="archive">Archive</option>
                    <option value="delete">Delete</option>
                </select>
                
                <button
                    type="button"
                    onclick="applyBulkAction()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                    Apply
                </button>
            </div>
            
            <div class="text-sm text-gray-600">
                {{ $projects->total() }} projects found
            </div>
        </div>

        <!-- Projects Table -->
        <x-admin.table :headers="['', 'Title', 'Status', 'Categories', 'Views', 'Date', '']" actions>
            @forelse($projects as $project)
                <x-admin.table.row>
                    <x-admin.table.cell class="w-4">
                        <input
                            type="checkbox"
                            name="selected[]"
                            value="{{ $project->id }}"
                            class="bulk-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12 mr-4">
                                @if($project->image)
                                    <img 
                                        src="{{ Storage::url($project->image) }}" 
                                        alt="{{ $project->title }}"
                                        class="h-12 w-12 rounded-lg object-cover"
                                    >
                                @else
                                    <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <x-admin.icon name="folder" class="w-6 h-6 text-gray-500" />
                                    </div>
                                @endif
                            </div>
                            <div>
                                <a 
                                    href="{{ route('admin.projects.show', $project) }}" 
                                    class="font-medium text-gray-900 hover:text-blue-600"
                                >
                                    {{ $project->title }}
                                </a>
                                <p class="text-sm text-gray-500">{{ Str::limit($project->excerpt, 60) }}</p>
                            </div>
                        </div>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <x-admin.badge :color="$project->status === 'published' ? 'green' : ($project->status === 'draft' ? 'yellow' : 'gray')">
                            {{ ucfirst($project->status) }}
                        </x-admin.badge>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <div class="flex flex-wrap gap-1">
                            @foreach($project->categories->take(2) as $category)
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">
                                    {{ $category->name }}
                                </span>
                            @endforeach
                            @if($project->categories->count() > 2)
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">
                                    +{{ $project->categories->count() - 2 }}
                                </span>
                            @endif
                        </div>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <div class="text-sm text-gray-900">{{ $project->views }}</div>
                        <div class="text-xs text-gray-500">views</div>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <div class="text-sm text-gray-900">{{ $project->created_at->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $project->created_at->format('h:i A') }}</div>
                    </x-admin.table.cell>
                    
                    <x-admin.table.actions>
                        <a 
                            href="{{ route('portfolio.detail', $project->slug) }}" 
                            target="_blank"
                            class="text-gray-400 hover:text-gray-600"
                            title="View"
                        >
                            <x-admin.icon name="eye" class="w-5 h-5" />
                        </a>
                        <a 
                            href="{{ route('admin.projects.edit', $project) }}" 
                            class="text-blue-600 hover:text-blue-800"
                            title="Edit"
                        >
                            <x-admin.icon name="edit" class="w-5 h-5" />
                        </a>
                        <form 
                            action="{{ route('admin.projects.destroy', $project) }}" 
                            method="POST" 
                            class="inline"
                            onsubmit="return confirm('Are you sure you want to delete this project?')"
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
                        <x-admin.icon name="folder" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                        <p class="text-lg font-medium text-gray-900 mb-2">No projects found</p>
                        <p class="text-gray-600">Get started by creating your first project.</p>
                        <a 
                            href="{{ route('admin.projects.create') }}" 
                            class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        >
                            <x-admin.icon name="plus" class="w-5 h-5 mr-2" />
                            Create Project
                        </a>
                    </td>
                </tr>
            @endforelse
        </x-admin.table>

        <!-- Pagination -->
        @if($projects->hasPages())
            <div class="mt-6">
                <x-admin.pagination :paginator="$projects" />
            </div>
        @endif
    </x-admin.card>

    @push('scripts')
    <script>
        function handleBulkAction() {
            const action = document.getElementById('bulkSelectAction').value;
            const checkboxes = document.querySelectorAll('.bulk-checkbox:checked');
            
            if (checkboxes.length === 0) {
                alert('Please select at least one project.');
                document.getElementById('bulkSelectAction').value = '';
                return;
            }
            
            if (action === 'delete' && !confirm('Are you sure you want to delete the selected projects?')) {
                document.getElementById('bulkSelectAction').value = '';
                return;
            }
            
            document.getElementById('bulkAction').value = action;
            
            const ids = Array.from(checkboxes).map(cb => cb.value);
            document.getElementById('bulkIds').value = JSON.stringify(ids);
            
            document.getElementById('bulkForm').submit();
        }
        
        function applyBulkAction() {
            const action = document.getElementById('bulkSelectAction').value;
            if (action) {
                handleBulkAction();
            }
        }
        
        // Select all checkboxes
        document.getElementById('selectAll')?.addEventListener('change', function(e) {
            const checkboxes = document.querySelectorAll('.bulk-checkbox');
            checkboxes.forEach(cb => cb.checked = e.target.checked);
        });
    </script>
    @endpush
</x-admin.layout>