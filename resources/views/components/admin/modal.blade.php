@props([
    'id' => 'modal',
    'title' => '',
    'size' => 'md', // sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl
    'closeable' => true
])

@php
    $sizeClasses = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
    ][$size] ?? 'max-w-md';
@endphp

<div 
    id="{{ $id }}" 
    x-data="{ open: false }"
    x-show="open"
    @keydown.escape.window="open = false"
    class="fixed inset-0 z-50 overflow-y-auto hidden"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    <div 
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        aria-hidden="true"
    ></div>

    <div class="flex min-h-screen items-center justify-center p-4 text-center">
        <div 
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative inline-block w-full {{ $sizeClasses }} overflow-hidden rounded-lg bg-white text-left align-middle shadow-xl transition-all"
        >
            @if($title || $closeable)
                <div class="px-6 py-4 border-b border-gray-200">
                    @if($title)
                        <h3 class="text-lg font-medium text-gray-900" id="modal-title">{{ $title }}</h3>
                    @endif
                    @if($closeable)
                        <button 
                            type="button"
                            @click="open = false"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-500"
                        >
                            <span class="sr-only">Close</span>
                            <x-admin.icon name="x" class="w-5 h-5" />
                        </button>
                    @endif
                </div>
            @endif

            <div class="px-6 py-4">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>