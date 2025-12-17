@props([
    'label' => null,
    'error' => null,
    'placeholder' => null,
    'required' => false,
    'rows' => 4,
])

<div>
    @if($label)
        <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <textarea
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition']) }}
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($required) required @endif
    ></textarea>
    
    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>