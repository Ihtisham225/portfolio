@props([
    'href' => '#',
    'active' => false,
    'icon' => null,
    'badge' => null,
    'badgeColor' => 'blue'
])

<a 
    href="{{ $href }}"
    @class([
        'flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors',
        'bg-blue-600 text-white' => $active,
        'text-gray-300 hover:bg-gray-800 hover:text-white' => !$active
    ])
>
    @if($icon)
        <x-admin.icon :name="$icon" class="w-5 h-5 mr-3" />
    @endif
    
    <span class="flex-1">{{ $slot }}</span>
    
    @if($badge)
        <span @class([
            'inline-flex items-center justify-center px-2 py-1 text-xs font-bold rounded-full',
            'bg-white text-blue-600' => $active,
            'bg-' . $badgeColor . '-500 text-white' => !$active
        ])>
            {{ $badge }}
        </span>
    @endif
</a>