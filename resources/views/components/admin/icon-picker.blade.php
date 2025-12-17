@props(['name' => 'icon', 'value' => '', 'label' => 'Icon'])

<div class="space-y-2">
    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    <div class="grid grid-cols-6 gap-2">
        @foreach([
            'fas fa-code',
            'fas fa-laptop-code',
            'fas fa-paint-brush',
            'fas fa-database',
            'fas fa-server',
            'fas fa-mobile-alt',
            'fas fa-chart-line',
            'fas fa-cogs',
            'fas fa-layer-group',
            'fas fa-rocket',
            'fas fa-globe',
            'fas fa-shield-alt',
            'fas fa-bolt',
            'fas fa-lightbulb',
            'fas fa-tools',
        ] as $icon)
            <label class="relative cursor-pointer">
                <input 
                    type="radio" 
                    name="{{ $name }}" 
                    value="{{ $icon }}" 
                    class="sr-only"
                    {{ $value === $icon ? 'checked' : '' }}
                >
                <div class="w-full aspect-square flex items-center justify-center rounded-lg border-2 {{ $value === $icon ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                    <i class="{{ $icon }} text-gray-600"></i>
                </div>
            </label>
        @endforeach
    </div>
</div>