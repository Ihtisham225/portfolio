@props([
    'type' => 'default',
    'title' => '',
    'time' => '',
    'user' => '',
    'avatar' => null
])

<div class="flex items-start">
    <div class="flex-shrink-0">
        @if($type === 'project')
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                <x-admin.icon name="folder" class="w-6 h-6 text-blue-600" />
            </div>
        @elseif($type === 'post')
            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                <x-admin.icon name="file-text" class="w-6 h-6 text-green-600" />
            </div>
        @elseif($type === 'message')
            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                <x-admin.icon name="mail" class="w-6 h-6 text-red-600" />
            </div>
        @else
            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                <x-admin.icon name="activity" class="w-6 h-6 text-gray-600" />
            </div>
        @endif
    </div>
    
    <div class="ml-4 flex-1">
        <p class="text-sm font-medium text-gray-900">{{ $title }}</p>
        
        @if($user)
            <p class="text-sm text-gray-500">By {{ $user }}</p>
        @endif
        
        <p class="text-xs text-gray-400 mt-1">{{ $time }}</p>
    </div>
</div>