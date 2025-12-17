<x-admin.layout title="Message: {{ $message->subject }}">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $message->subject }}</h1>
                <div class="flex items-center space-x-4 mt-2">
                    @if($message->is_unread)
                        <x-admin.badge color="red">Unread</x-admin.badge>
                    @elseif($message->is_replied)
                        <x-admin.badge color="green">Replied</x-admin.badge>
                    @else
                        <x-admin.badge color="blue">Read</x-admin.badge>
                    @endif
                    <span class="text-sm text-gray-600">
                        Received {{ $message->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                @if($message->is_unread)
                    <form 
                        action="{{ route('admin.messages.mark-as-read', $message) }}" 
                        method="POST" 
                        class="inline"
                    >
                        @csrf
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center"
                        >
                            <x-admin.icon name="check-circle" class="w-5 h-5 mr-2" />
                            Mark as Read
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
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center"
                        >
                            <x-admin.icon name="archive" class="w-5 h-5 mr-2" />
                            Archive
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
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center"
                    >
                        <x-admin.icon name="trash" class="w-5 h-5 mr-2" />
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Message Content -->
            <x-admin.card>
                <div class="space-y-6">
                    <!-- Message Header -->
                    <div class="flex items-start justify-between pb-4 border-b border-gray-200">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                @if($message->user)
                                    <x-admin.icon name="user" class="w-6 h-6" />
                                @else
                                    <span class="text-lg font-semibold">
                                        {{ strtoupper(substr($message->name, 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                <h2 class="text-lg font-medium text-gray-900">{{ $message->name }}</h2>
                                <div class="text-sm text-gray-600">
                                    <a href="mailto:{{ $message->email }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $message->email }}
                                    </a>
                                </div>
                                @if($message->user)
                                    <div class="mt-1">
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                            Registered User
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $message->created_at->format('F d, Y h:i A') }}
                        </div>
                    </div>

                    <!-- Message Body -->
                    <div class="prose max-w-none">
                        {!! nl2br(e($message->message)) !!}
                    </div>

                    <!-- Message Footer -->
                    <div class="pt-4 border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            <div class="flex items-center space-x-2">
                                <x-admin.icon name="location-marker" class="w-4 h-4" />
                                <span>IP: {{ $message->ip_address }}</span>
                            </div>
                            <div class="flex items-center space-x-2 mt-1">
                                <x-admin.icon name="computer-desktop" class="w-4 h-4" />
                                <span>{{ $message->browser }} on {{ $message->platform }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <!-- Reply Section -->
            @if(!$message->is_replied)
                <x-admin.card title="Reply to {{ $message->name }}">
                    <form action="{{ route('admin.messages.reply', $message) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="response" class="block text-sm font-medium text-gray-700 mb-1">
                                Your Response *
                            </label>
                            <textarea
                                id="response"
                                name="response"
                                rows="8"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                                placeholder="Type your response here..."
                            ></textarea>
                            <p class="mt-1 text-sm text-gray-500">This response will be sent via email to {{ $message->email }}</p>
                            @error('response')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="text-sm text-gray-600">
                                <p>To: {{ $message->name }} &lt;{{ $message->email }}&gt;</p>
                                <p>Subject: Re: {{ $message->subject }}</p>
                            </div>
                            <button
                                type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium"
                            >
                                Send Reply
                            </button>
                        </div>
                    </form>
                </x-admin.card>
            @endif

            <!-- Previous Response -->
            @if($message->is_replied)
                <x-admin.card title="Previous Response">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                <p>Replied on {{ $message->responded_at->format('F d, Y h:i A') }}</p>
                            </div>
                            <form action="{{ route('admin.messages.mark-as-read', $message) }}" method="POST">
                                @csrf
                                <button 
                                    type="submit" 
                                    class="text-sm text-blue-600 hover:text-blue-800"
                                >
                                    Mark as Unread
                                </button>
                            </form>
                        </div>
                        
                        <div class="prose max-w-none bg-blue-50 p-4 rounded-lg">
                            {!! nl2br(e($message->response)) !!}
                        </div>
                        
                        <div class="pt-4 border-t border-gray-200">
                            <form action="{{ route('admin.messages.reply', $message) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="new_response" class="block text-sm font-medium text-gray-700 mb-1">
                                        Send Another Response
                                    </label>
                                    <textarea
                                        id="new_response"
                                        name="response"
                                        rows="4"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Type another response..."
                                    ></textarea>
                                </div>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm"
                                >
                                    Send Another Reply
                                </button>
                            </form>
                        </div>
                    </div>
                </x-admin.card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Message Information -->
            <x-admin.card title="Message Information">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Status</h3>
                        <div class="mt-1">
                            @if($message->is_unread)
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">Unread</span>
                            @elseif($message->is_replied)
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Replied</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">Read</span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Date & Time</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $message->created_at->format('F d, Y') }}</p>
                        <p class="text-sm text-gray-600">{{ $message->created_at->format('h:i A') }}</p>
                    </div>
                    
                    @if($message->is_replied)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Replied On</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $message->responded_at->format('F d, Y') }}</p>
                            <p class="text-sm text-gray-600">{{ $message->responded_at->format('h:i A') }}</p>
                        </div>
                    @endif
                </div>
            </x-admin.card>

            <!-- Technical Information -->
            <x-admin.card title="Technical Information">
                <div class="space-y-3">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">IP Address</h3>
                        <p class="mt-1 text-sm text-gray-900 font-mono">{{ $message->ip_address }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Browser</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $message->browser }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Platform</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $message->platform }}</p>
                    </div>
                </div>
            </x-admin.card>

            <!-- Quick Actions -->
            <x-admin.card title="Quick Actions">
                <div class="space-y-3">
                    @if(!$message->is_replied)
                        <a 
                            href="#reply"
                            onclick="document.getElementById('response').focus()"
                            class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center"
                        >
                            <x-admin.icon name="reply" class="w-5 h-5 inline mr-2" />
                            Reply
                        </a>
                    @endif
                    
                    @if($message->is_unread)
                        <form action="{{ route('admin.messages.mark-as-read', $message) }}" method="POST" class="w-full">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                            >
                                <x-admin.icon name="check-circle" class="w-5 h-5 inline mr-2" />
                                Mark as Read
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.messages.mark-as-read', $message) }}" method="POST" class="w-full">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50"
                            >
                                <x-admin.icon name="clock" class="w-5 h-5 inline mr-2" />
                                Mark as Unread
                            </button>
                        </form>
                    @endif
                    
                    @if($message->status !== 'archived')
                        <form action="{{ route('admin.messages.mark-as-archived', $message) }}" method="POST" class="w-full">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                            >
                                <x-admin.icon name="archive" class="w-5 h-5 inline mr-2" />
                                Archive Message
                            </button>
                        </form>
                    @endif
                    
                    <form 
                        action="{{ route('admin.messages.destroy', $message) }}" 
                        method="POST" 
                        class="w-full"
                        onsubmit="return confirm('Are you sure you want to delete this message?')"
                    >
                        @csrf
                        @method('DELETE')
                        <button 
                            type="submit" 
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                        >
                            <x-admin.icon name="trash" class="w-5 h-5 inline mr-2" />
                            Delete Message
                        </button>
                    </form>
                </div>
            </x-admin.card>

            <!-- Contact Information -->
            <x-admin.card title="Contact Information">
                <div class="space-y-3">
                    <div class="flex items-center space-x-2">
                        <x-admin.icon name="user" class="w-5 h-5 text-gray-400" />
                        <span class="text-sm text-gray-900">{{ $message->name }}</span>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <x-admin.icon name="mail" class="w-5 h-5 text-gray-400" />
                        <a href="mailto:{{ $message->email }}" class="text-sm text-blue-600 hover:text-blue-800">
                            {{ $message->email }}
                        </a>
                    </div>
                    
                    @if($message->user)
                        <div class="flex items-center space-x-2">
                            <x-admin.icon name="user-circle" class="w-5 h-5 text-gray-400" />
                            <span class="text-sm text-gray-600">Registered User</span>
                        </div>
                    @endif
                </div>
            </x-admin.card>
        </div>
    </div>

    @push('styles')
    <style>
        .prose pre {
            background-color: #f3f4f6;
            padding: 1rem;
            border-radius: 0.375rem;
            overflow-x: auto;
            margin: 1rem 0;
        }
        
        .prose code {
            background-color: #f3f4f6;
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
    </style>
    @endpush
</x-admin.layout>