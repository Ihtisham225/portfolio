@props([
    'title' => '',
    'action' => null,
    'class' => ''
])

<div class="bg-white rounded-xl shadow-sm border border-gray-200 {{ $class }}">
    @if($title || $action)
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                @if($title)
                    <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
                @endif
                
                @if($action)
                    <a 
                        href="{{ $action['href'] }}" 
                        class="text-sm font-medium text-blue-600 hover:text-blue-800"
                    >
                        {{ $action['label'] }}
                    </a>
                @endif
            </div>
        </div>
    @endif
    
    <div class="p-6">
        {{ $slot }}
    </div>
</div>