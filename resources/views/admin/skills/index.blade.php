<x-admin.layout title="Skills">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Skills</h1>
                <p class="text-gray-600">Manage your skills and expertise</p>
            </div>
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('admin.skills.create') }}" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center"
                >
                    <x-admin.icon name="plus" class="w-5 h-5 mr-2" />
                    New Skill
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form action="{{ route('admin.skills.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="Search skills..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
                
                <!-- Featured -->
                <div>
                    <label for="featured" class="block text-sm font-medium text-gray-700 mb-1">Featured</label>
                    <select
                        name="featured"
                        id="featured"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="all" {{ request('featured') === 'all' ? 'selected' : '' }}>All Skills</option>
                        <option value="true" {{ request('featured') === 'true' ? 'selected' : '' }}>Featured Only</option>
                        <option value="false" {{ request('featured') === 'false' ? 'selected' : '' }}>Not Featured</option>
                    </select>
                </div>
                
                <!-- Sort Order -->
                <div>
                    <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select
                        name="sort_by"
                        id="sort_by"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="sort_order" {{ request('sort_by') === 'sort_order' ? 'selected' : '' }}>Sort Order</option>
                        <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Name A-Z</option>
                        <option value="percentage" {{ request('sort_by') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                        <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Date Created</option>
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
                        href="{{ route('admin.skills.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </x-admin.card>

    <!-- Bulk Actions -->
    <form id="bulkForm" action="{{ route('admin.skills.bulk-action') }}" method="POST">
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
                    <option value="featured">Mark as Featured</option>
                    <option value="unfeatured">Remove from Featured</option>
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
                {{ $skills->total() }} skills found
            </div>
        </div>

        <!-- Skills Table -->
        <x-admin.table :headers="['', 'Skill', 'Progress', 'Icon', 'Sort Order', 'Status', '']" actions>
            @forelse($skills as $skill)
                <x-admin.table.row>
                    <x-admin.table.cell class="w-4">
                        <input
                            type="checkbox"
                            name="selected[]"
                            value="{{ $skill->id }}"
                            class="bulk-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-10 h-10 mr-4 flex items-center justify-center rounded-lg"
                                 style="background-color: {{ $skill->progress_color }}20; color: {{ $skill->progress_color }};">
                                <i class="{{ $skill->icon_class }}"></i>
                            </div>
                            <div>
                                <a 
                                    href="{{ route('admin.skills.edit', $skill) }}" 
                                    class="font-medium text-gray-900 hover:text-blue-600"
                                >
                                    {{ $skill->name }}
                                </a>
                                <p class="text-sm text-gray-500">{{ $skill->slug }}</p>
                            </div>
                        </div>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <div class="w-full">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ $skill->percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div 
                                    class="h-2 rounded-full"
                                    style="width: {{ $skill->percentage }}%; background-color: {{ $skill->progress_color }};"
                                ></div>
                            </div>
                        </div>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <div class="flex items-center text-gray-600">
                            <i class="{{ $skill->icon_class }} mr-2"></i>
                            <span class="text-sm">{{ $skill->icon ?: 'Default' }}</span>
                        </div>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        <div class="text-sm text-gray-900">{{ $skill->sort_order ?? '-' }}</div>
                    </x-admin.table.cell>
                    
                    <x-admin.table.cell>
                        @if($skill->is_featured)
                            <x-admin.badge color="yellow">Featured</x-admin.badge>
                        @else
                            <span class="text-sm text-gray-500">Normal</span>
                        @endif
                    </x-admin.table.cell>
                    
                    <x-admin.table.actions>
                        <a 
                            href="{{ route('admin.skills.edit', $skill) }}" 
                            class="text-blue-600 hover:text-blue-800"
                            title="Edit"
                        >
                            <x-admin.icon name="edit" class="w-5 h-5" />
                        </a>
                        <form 
                            action="{{ route('admin.skills.destroy', $skill) }}" 
                            method="POST" 
                            class="inline"
                            onsubmit="return confirm('Are you sure you want to delete this skill?')"
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
                        <x-admin.icon name="code" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                        <p class="text-lg font-medium text-gray-900 mb-2">No skills found</p>
                        <p class="text-gray-600">Get started by creating your first skill.</p>
                        <a 
                            href="{{ route('admin.skills.create') }}" 
                            class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        >
                            <x-admin.icon name="plus" class="w-5 h-5 mr-2" />
                            Create Skill
                        </a>
                    </td>
                </tr>
            @endforelse
        </x-admin.table>

        <!-- Pagination -->
        @if($skills->hasPages())
            <div class="mt-6">
                <x-admin.pagination :paginator="$skills" />
            </div>
        @endif
    </x-admin.card>

    @push('scripts')
    <script>
        function handleBulkAction() {
            const action = document.getElementById('bulkSelectAction').value;
            const checkboxes = document.querySelectorAll('.bulk-checkbox:checked');
            
            if (checkboxes.length === 0) {
                alert('Please select at least one skill.');
                document.getElementById('bulkSelectAction').value = '';
                return;
            }
            
            if (action === 'delete' && !confirm('Are you sure you want to delete the selected skills?')) {
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
    </script>
    @endpush
</x-admin.layout>