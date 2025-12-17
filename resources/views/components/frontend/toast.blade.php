@props([
    'type' => 'success',
    'message' => ''
])

<div 
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 5000)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
    class="fixed bottom-4 right-4 z-50 max-w-sm w-full"
    x-cloak
>
    <div 
        @class([
            'rounded-lg border p-4 shadow-lg',
            'bg-green-50 border-green-200 text-green-800' => $type === 'success',
            'bg-red-50 border-red-200 text-red-800' => $type === 'error',
            'bg-yellow-50 border-yellow-200 text-yellow-800' => $type === 'warning',
            'bg-blue-50 border-blue-200 text-blue-800' => $type === 'info'
        ])
    >
        <div class="flex items-start">
            <div class="flex-shrink-0">
                @if($type === 'success')
                    <x-frontend.icon name="check" class="w-5 h-5 text-green-400" />
                @elseif($type === 'error')
                    <x-frontend.icon name="x" class="w-5 h-5 text-red-400" />
                @elseif($type === 'warning')
                    <x-frontend.icon name="exclamation" class="w-5 h-5 text-yellow-400" />
                @else
                    <x-frontend.icon name="info" class="w-5 h-5 text-blue-400" />
                @endif
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium">{{ $message }}</p>
            </div>
            <button 
                @click="show = false"
                class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500"
            >
                <x-frontend.icon name="x" class="w-5 h-5" />
            </button>
        </div>
    </div>
</div>