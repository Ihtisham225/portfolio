<x-admin.layout title="Certifications">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Certifications</h1>
                <p class="text-gray-600">Manage your professional certifications</p>
            </div>
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('admin.certifications.create') }}" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center"
                >
                    <x-admin.icon name="plus" class="w-5 h-5 mr-2" />
                    Add Certification
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form action="{{ route('admin.certifications.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="Search certifications..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
                
                <!-- Validity -->
                <div>
                    <label for="validity" class="block text-sm font-medium text-gray-700 mb-1">Validity</label>
                    <select
                        name="validity"
                        id="validity"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="all" {{ request('validity') === 'all' ? 'selected' : '' }}>All Certifications</option>
                        <option value="valid" {{ request('validity') === 'valid' ? 'selected' : '' }}>Valid Only</option>
                        <option value="expired" {{ request('validity') === 'expired' ? 'selected' : '' }}>Expired Only</option>
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
                        <option value="issue_date_desc" {{ request('sort_by') === 'issue_date_desc' ? 'selected' : '' }}>Issue Date (Newest)</option>
                        <option value="issue_date_asc" {{ request('sort_by') === 'issue_date_asc' ? 'selected' : '' }}>Issue Date (Oldest)</option>
                        <option value="expiration_date" {{ request('sort_by') === 'expiration_date' ? 'selected' : '' }}>Expiration Date</option>
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
                        href="{{ route('admin.certifications.index') }}"
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
                {{ $certifications->total() }} certifications found
            </div>
        </div>

        <!-- Certifications Table -->
        <x-admin.table :headers="['', 'Certification', 'Issuer', 'Dates', 'Credential', 'Status', '']" actions>
            @forelse($certifications as $cert)
                <x-admin.table.row>
                    <!-- Image -->
                    <x-admin.table.cell class="w-16">
                        @if($cert->image_url)
                            <img 
                                src="{{ $cert->image_url }}" 
                                alt="{{ $cert->name }}"
                                class="w-12 h-12 rounded-lg object-cover border border-gray-200"
                            >
                        @else
                            <div class="w-12 h-12 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center">
                                <x-admin.icon name="badge-check" class="w-6 h-6 text-gray-400" />
                            </div>
                        @endif
                    </x-admin.table.cell>
                    
                    <!-- Certification Details -->
                    <x-admin.table.cell>
                        <div>
                            <a 
                                href="{{ route('admin.certifications.edit', $cert) }}" 
                                class="font-medium text-gray-900 hover:text-blue-600"
                            >
                                {{ $cert->name }}
                            </a>
                            <div class="flex items-center mt-1 space-x-2 text-sm text-gray-500">
                                @if($cert->credential_id)
                                    <span class="px-2 py-1 bg-gray-100 rounded text-xs">
                                        ID: {{ $cert->credential_id }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </x-admin.table.cell>
                    
                    <!-- Issuer -->
                    <x-admin.table.cell>
                        <div class="text-sm text-gray-900">{{ $cert->issuer }}</div>
                    </x-admin.table.cell>
                    
                    <!-- Dates -->
                    <x-admin.table.cell>
                        <div class="space-y-1">
                            <div class="text-sm text-gray-900">
                                <span class="text-gray-500">Issued:</span> {{ $cert->formatted_issue_date }}
                            </div>
                            @if($cert->expiration_date)
                                <div class="text-sm {{ $cert->is_valid ? 'text-gray-600' : 'text-red-600' }}">
                                    <span class="text-gray-500">Expires:</span> {{ $cert->formatted_expiration_date }}
                                </div>
                            @endif
                        </div>
                    </x-admin.table.cell>
                    
                    <!-- Credential URL -->
                    <x-admin.table.cell>
                        @if($cert->credential_url)
                            <a 
                                href="{{ $cert->credential_url }}" 
                                target="_blank"
                                class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800"
                            >
                                <x-admin.icon name="external-link" class="w-4 h-4 mr-1" />
                                Verify
                            </a>
                        @else
                            <span class="text-sm text-gray-400">-</span>
                        @endif
                    </x-admin.table.cell>
                    
                    <!-- Status -->
                    <x-admin.table.cell>
                        @if($cert->is_valid)
                            <x-admin.badge color="green">
                                {{ $cert->expiration_date ? 'Valid' : 'No Expiration' }}
                            </x-admin.badge>
                            @if($cert->expiration_date)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $cert->expiration_date->diffForHumans() }}
                                </div>
                            @endif
                        @else
                            <x-admin.badge color="red">Expired</x-admin.badge>
                            <div class="text-xs text-red-500 mt-1">
                                {{ $cert->expiration_date->diffForHumans() }}
                            </div>
                        @endif
                    </x-admin.table.cell>
                    
                    <!-- Actions -->
                    <x-admin.table.actions>
                        @if($cert->credential_url)
                            <a 
                                href="{{ $cert->credential_url }}" 
                                target="_blank"
                                class="text-gray-400 hover:text-gray-600"
                                title="Verify"
                            >
                                <x-admin.icon name="external-link" class="w-5 h-5" />
                            </a>
                        @endif
                        <a 
                            href="{{ route('admin.certifications.edit', $cert) }}" 
                            class="text-blue-600 hover:text-blue-800"
                            title="Edit"
                        >
                            <x-admin.icon name="edit" class="w-5 h-5" />
                        </a>
                        <form 
                            action="{{ route('admin.certifications.destroy', $cert) }}" 
                            method="POST" 
                            class="inline"
                            onsubmit="return confirm('Are you sure you want to delete this certification?')"
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
                        <x-admin.icon name="badge-check" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                        <p class="text-lg font-medium text-gray-900 mb-2">No certifications found</p>
                        <p class="text-gray-600">Get started by adding your first certification.</p>
                        <a 
                            href="{{ route('admin.certifications.create') }}" 
                            class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        >
                            <x-admin.icon name="plus" class="w-5 h-5 mr-2" />
                            Add Certification
                        </a>
                    </td>
                </tr>
            @endforelse
        </x-admin.table>

        <!-- Pagination -->
        @if($certifications->hasPages())
            <div class="mt-6">
                <x-admin.pagination :paginator="$certifications" />
            </div>
        @endif
    </x-admin.card>
</x-admin.layout>