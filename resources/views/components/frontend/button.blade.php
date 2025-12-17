@props([
    'variant' => 'primary',
    'size' => 'md',
    'fullWidth' => false,
    'type' => 'button'
])

@php
    $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
        'secondary' => 'bg-gray-200 text-gray-900 hover:bg-gray-300 focus:ring-gray-500',
        'outline' => 'border border-blue-600 text-blue-600 hover:bg-blue-50 focus:ring-blue-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    ];
    
    $sizes = [
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-6 py-3 text-sm',
        'lg' => 'px-8 py-4 text-base',
    ];
    
    $classes = [
        'inline-flex items-center justify-center font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors',
        $variants[$variant],
        $sizes[$size],
        $fullWidth ? 'w-full' : '',
    ];
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => implode(' ', $classes)]) }}
>
    {{ $slot }}
</button>