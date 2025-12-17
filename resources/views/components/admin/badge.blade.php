@props([
    'color' => 'gray',
    'size' => 'md'
])

@php
    $colors = [
        'gray' => 'bg-gray-100 text-gray-800',
        'red' => 'bg-red-100 text-red-800',
        'yellow' => 'bg-yellow-100 text-yellow-800',
        'green' => 'bg-green-100 text-green-800',
        'blue' => 'bg-blue-100 text-blue-800',
        'indigo' => 'bg-indigo-100 text-indigo-800',
        'purple' => 'bg-purple-100 text-purple-800',
        'pink' => 'bg-pink-100 text-pink-800',
    ];
    
    $sizes = [
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-3 py-1 text-sm',
        'lg' => 'px-4 py-2 text-base',
    ];
@endphp

<span 
    {{ $attributes->merge(['class' => "inline-flex items-center font-medium rounded-full {$colors[$color]} {$sizes[$size]}"]) }}
>
    {{ $slot }}
</span>