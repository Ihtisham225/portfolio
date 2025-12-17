@props([
    'href' => '#',
    'active' => false,
    'mobile' => false
])

<a 
    href="{{ $href }}"
    @class([
        'text-blue-600' => $active && !$mobile,
        'text-gray-700 hover:text-blue-600' => !$active && !$mobile,
        'block px-3 py-2 rounded-md text-base font-medium' => $mobile,
        'text-white bg-blue-600' => $active && $mobile,
        'text-gray-300 hover:text-white hover:bg-gray-700' => !$active && $mobile,
    ])
>
    {{ $slot }}
</a>