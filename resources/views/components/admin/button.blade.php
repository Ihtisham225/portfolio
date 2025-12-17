@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'fullWidth' => false,
    'disabled' => false,
])

@php
    $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
        'secondary' => 'bg-gray-200 text-gray-900 hover:bg-gray-300 focus:ring-gray-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
        'warning' => 'bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500',
        'outline-primary' => 'border border-blue-600 text-blue-600 hover:bg-blue-50 focus:ring-blue-500',
        'outline-gray' => 'border border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-gray-500',
    ];
    
    $sizes = [
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];
    
    $classes = [
        'inline-flex items-center justify-center font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors',
        $variants[$variant],
        $sizes[$size],
        $fullWidth ? 'w-full' : '',
        $disabled ? 'opacity-50 cursor-not-allowed' : '',
    ];
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => implode(' ', $classes)]) }}
    {{ $disabled ? 'disabled' : '' }}
>
    {{ $slot }}
</button>