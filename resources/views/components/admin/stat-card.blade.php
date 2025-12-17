@props([
    'title' => '',
    'value' => 0,
    'icon' => null,
    'color' => 'blue',
    'change' => null,
    'changeLabel' => null
])

@php
    $colors = [
        'blue' => 'bg-blue-100 text-blue-600',
        'green' => 'bg-green-100 text-green-600',
        'red' => 'bg-red-100 text-red-600',
        'yellow' => 'bg-yellow-100 text-yellow-600',
        'purple' => 'bg-purple-100 text-purple-600',
        'indigo' => 'bg-indigo-100 text-indigo-600',
        'pink' => 'bg-pink-100 text-pink-600',
    ];
    
    $changeColors = [
        'positive' => 'text-green-600',
        'negative' => 'text-red-600',
        'neutral' => 'text-gray-600',
    ];
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $value }}</p>
            
            @if($change)
                <div class="flex items-center mt-2">
                    @if($change['value'] > 0)
                        <x-admin.icon name="trending-up" class="w-4 h-4 text-green-500 mr-1" />
                    @elseif($change['value'] < 0)
                        <x-admin.icon name="trending-down" class="w-4 h-4 text-red-500 mr-1" />
                    @else
                        <x-admin.icon name="minus" class="w-4 h-4 text-gray-500 mr-1" />
                    @endif
                    <span @class([
                        'text-sm font-medium',
                        $changeColors[$change['value'] > 0 ? 'positive' : ($change['value'] < 0 ? 'negative' : 'neutral')]
                    ])>
                        {{ abs($change['value']) }}% {{ $change['label'] ?? '' }}
                    </span>
                </div>
            @elseif($changeLabel)
                <p class="text-sm text-gray-500 mt-2">{{ $changeLabel }}</p>
            @endif
        </div>
        
        @if($icon)
            <div class="p-3 rounded-lg {{ $colors[$color] }}">
                <x-admin.icon :name="$icon" class="w-8 h-8" />
            </div>
        @endif
    </div>
</div>