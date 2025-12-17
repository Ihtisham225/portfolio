<x-admin.layout title="Messages">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
                <p class="text-gray-600">Manage contact form submissions</p>
            </div>
            <div class="flex items-center space-x-4">
                @php
                    $unreadCount = \App\Models\Message::where('status', 'unread')->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                        {{ $unreadCount }} unread
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form action="{{ route('admin.messages.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="Search messages..."
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
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Messages</option>
                        <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                        <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>Replied</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
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
                        href="{{ route('admin.messages.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </x-admin.card>

    <!-- Bulk Actions -->
    <form id="bulkForm" action="{{ route('admin.messages.bulk-action') }}" method="POST">
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
                    <option value="read">Mark as Read</option>
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
                {{ $messages->total() }} messages found
            </div>
        </div>

        <!-- Messages Table -->
        <x-admin.table :headers="['', 'From', 'Subject', 'Message', 'Date', 'Status', '']" actions>
            @forelse($messages as $message)
                <x-admin.table.row class="{{ $message->is_unread ? 'bg-blue-50' : '' }}">
                    <x-admin.table.cell class="w-4">
                        <input
                            type="checkbox"
                            name="selected[]"
                            value="{{ $message->id }}"
                            class="bulk-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                    </x-admin.table.cell>
                    
                    <!-- Sender -->
                    <x-admin.table.cell>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-10 h-10 mr-3 flex items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                @if($message->user)
                                    <x-admin.icon name="user" class="w-5 h-5" />
                                @else
                                    {{ strtoupper(substr($message->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $message->name }}</div>
                                <div class="text-sm text-gray-500">{{ $message->email }}</div>
                                @if($message->user)
                                    <span class="text-xs text-blue-600">Registered User</span>
                                @endif
                            </div>
                        </div>
                    </x-admin.table.cell>
                    
                    <!-- Subject -->
                    <x-admin.table.cell>
                        <a 
                            href="{{ route('admin.messages.show', $message) }}" 
                            class="font-medium text-gray-900 hover:text-blue-600"
                        >
                            {{ $message->subject }}
                        </a>
                    </x-admin.table.cell>
                    
                    <!-- Message Preview -->
                    <x-admin.table.cell>
                        <div class="text-sm text-gray-600 line-clamp-2">
                            {{ $message->short_message }}
                        </div>
                    </x-admin.table.cell>
                    
                    <!-- Date -->
                    <x-admin.table.cell>
                        <div class="text-sm text-gray-900">{{ $message->created_at->format('M d') }}</div>
                        <div class="text-xs text-gray-500">{{ $message->created_at->format('h:i A') }}</div>
                        <div class="text-xs text-gray-400 mt-1">{{ $message->created_at->diffForHumans() }}</div>
                    </x-admin.table.cell>
                    
                    <!-- Status -->
                    <x-admin.table.cell>
                        @if($message->is_unread)
                            <x-admin.badge color="red">Unread</x-admin.badge>
                        @elseif($message->is_replied)
                            <x-admin.badge color="green">Replied</x-admin.badge>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $message->responded_at->format('M d') }}
                            </div>
                        @elseif($message->status === 'archived')
                            <x-admin.badge color="gray">Archived</x-admin.badge>
                        @else
                            <x-admin.badge color="blue">Read</x-admin.badge>
                        @endif
                    </x-admin.table.cell>
                    
                    <!-- Actions -->
                    <x-admin.table.actions>
                        @if($message->is_unread)
                            <form 
                                action="{{ route('admin.messages.mark-as-read', $message) }}" 
                                method="POST" 
                                class="inline"
                            >
                                @csrf
                                <button 
                                    type="submit" 
                                    class="text-gray-400 hover:text-gray-600"
                                    title="Mark as Read"
                                >
                                    <x-admin.icon name="check-circle" class="w-5 h-5" />
                                </button>
                            </form>
                        @endif
                        
                        @if($message->status !== 'archived')
                            <form 
                                action="{{ route('admin.messages.mark-as-archived', $message) }}" 
                                method="POST" 
                                class="inline"
                            >
                                @csrf
                                <button 
                                    type="submit" 
                                    class="text-gray-400 hover:text-gray-600"
                                    title="Archive"
                                >
                                    <x-admin.icon name="archive" class="w-5 h-5" />
                                </button>
                            </form>
                        @endif
                        
                        <form 
                            action="{{ route('admin.messages.destroy', $message) }}" 
                            method="POST" 
                            class="inline"
                            onsubmit="return confirm('Are you sure you want to delete this message?')"
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
                        <x-admin.icon name="mail" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                        <p class="text-lg font-medium text-gray-900 mb-2">No messages found</p>
                        <p class="text-gray-600">All caught up! No new messages.</p>
                    </td>
                </tr>
            @endforelse
        </x-admin.table>

        <!-- Pagination -->
        @if($messages->hasPages())
            <div class="mt-6">
                <x-admin.pagination :paginator="$messages" />
            </div>
        @endif
    </x-admin.card>

    @push('scripts')
    <script>
        function handleBulkAction() {
            const action = document.getElementById('bulkSelectAction').value;
            const checkboxes = document.querySelectorAll('.bulk-checkbox:checked');
            
            if (checkboxes.length === 0) {
                alert('Please select at least one message.');
                document.getElementById('bulkSelectAction').value = '';
                return;
            }
            
            if (action === 'delete' && !confirm('Are you sure you want to delete the selected messages?')) {
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