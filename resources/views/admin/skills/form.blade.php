<x-admin.layout :title="$title ?? ($skill->id ? 'Edit Skill' : 'Create Skill')">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $skill->id ? 'Edit Skill' : 'Create New Skill' }}
                </h1>
                <p class="text-gray-600">
                    {{ $skill->id ? 'Update skill details' : 'Add a new skill' }}
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('admin.skills.index') }}" 
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                    Cancel
                </a>
            </div>
        </div>
    </x-slot>

    <form 
        action="{{ $skill->id ? route('admin.skills.update', $skill) : route('admin.skills.store') }}" 
        method="POST"
    >
        @csrf
        @if($skill->id)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <x-admin.card title="Basic Information">
                    <div class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Skill Name *
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $skill->name) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                                oninput="document.getElementById('slug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '')"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                                URL Slug *
                            </label>
                            <input
                                type="text"
                                id="slug"
                                name="slug"
                                value="{{ old('slug', $skill->slug) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Percentage -->
                        <div>
                            <label for="percentage" class="block text-sm font-medium text-gray-700 mb-1">
                                Skill Level (Percentage) *
                            </label>
                            <div class="flex items-center space-x-4">
                                <input
                                    type="range"
                                    id="percentage_slider"
                                    min="0"
                                    max="100"
                                    value="{{ old('percentage', $skill->percentage ?? 50) }}"
                                    class="flex-1"
                                    oninput="document.getElementById('percentage').value = this.value; document.getElementById('percentage_value').textContent = this.value + '%'"
                                >
                                <div class="flex items-center space-x-2">
                                    <input
                                        type="number"
                                        id="percentage"
                                        name="percentage"
                                        value="{{ old('percentage', $skill->percentage ?? 50) }}"
                                        min="0"
                                        max="100"
                                        class="w-20 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required
                                        oninput="document.getElementById('percentage_slider').value = this.value; document.getElementById('percentage_value').textContent = this.value + '%'"
                                    >
                                    <span id="percentage_value" class="text-sm font-medium text-gray-700">
                                        {{ old('percentage', $skill->percentage ?? 50) }}%
                                    </span>
                                </div>
                            </div>
                            @error('percentage')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preview -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Preview
                            </label>
                            <div class="p-4 border border-gray-300 rounded-lg bg-gray-50">
                                <div class="flex items-center mb-3">
                                    <div class="w-8 h-8 mr-3 flex items-center justify-center rounded-lg"
                                         style="background-color: {{ old('color', $skill->color ?? '#3B82F6') }}20; color: {{ old('color', $skill->color ?? '#3B82F6') }};">
                                        <i id="icon_preview" class="{{ old('icon', $skill->icon ?? 'fas fa-code') }}"></i>
                                    </div>
                                    <span id="name_preview" class="font-medium">{{ old('name', $skill->name ?? 'Skill Name') }}</span>
                                </div>
                                <div class="w-full">
                                    <div class="flex items-center justify-between mb-1">
                                        <span id="percentage_preview" class="text-sm font-medium text-gray-700">{{ old('percentage', $skill->percentage ?? 50) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div 
                                            id="progress_preview"
                                            class="h-2 rounded-full"
                                            style="width: {{ old('percentage', $skill->percentage ?? 50) }}%; background-color: {{ old('color', $skill->color ?? '#3B82F6') }};"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Settings -->
                <x-admin.card title="Settings">
                    <div class="space-y-6">
                        <!-- Color -->
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 mb-1">
                                Color *
                            </label>
                            <div class="flex items-center space-x-4">
                                <input
                                    type="color"
                                    id="color"
                                    name="color"
                                    value="{{ old('color', $skill->color ?? '#3B82F6') }}"
                                    class="w-12 h-12 border border-gray-300 rounded-lg cursor-pointer"
                                    onchange="updatePreview()"
                                >
                                <input
                                    type="text"
                                    value="{{ old('color', $skill->color ?? '#3B82F6') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    onchange="document.getElementById('color').value = this.value; updatePreview()"
                                    placeholder="#3B82F6"
                                >
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Hex color code (e.g., #3B82F6)</p>
                            @error('color')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Icon -->
                        <div>
                            <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">
                                Icon
                            </label>
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    <select
                                        id="icon"
                                        name="icon"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none"
                                        onchange="updatePreview()"
                                    >
                                        <option value="">Select Icon</option>
                                        <option value="fas fa-code" {{ old('icon', $skill->icon) === 'fas fa-code' ? 'selected' : '' }}>Code</option>
                                        <option value="fas fa-laptop-code" {{ old('icon', $skill->icon) === 'fas fa-laptop-code' ? 'selected' : '' }}>Laptop Code</option>
                                        <option value="fas fa-paint-brush" {{ old('icon', $skill->icon) === 'fas fa-paint-brush' ? 'selected' : '' }}>Paint Brush</option>
                                        <option value="fas fa-database" {{ old('icon', $skill->icon) === 'fas fa-database' ? 'selected' : '' }}>Database</option>
                                        <option value="fas fa-server" {{ old('icon', $skill->icon) === 'fas fa-server' ? 'selected' : '' }}>Server</option>
                                        <option value="fas fa-mobile-alt" {{ old('icon', $skill->icon) === 'fas fa-mobile-alt' ? 'selected' : '' }}>Mobile</option>
                                        <option value="fas fa-chart-line" {{ old('icon', $skill->icon) === 'fas fa-chart-line' ? 'selected' : '' }}>Chart Line</option>
                                        <option value="fas fa-cogs" {{ old('icon', $skill->icon) === 'fas fa-cogs' ? 'selected' : '' }}>Cogs</option>
                                        <option value="fas fa-layer-group" {{ old('icon', $skill->icon) === 'fas fa-layer-group' ? 'selected' : '' }}>Layer Group</option>
                                        <option value="fas fa-rocket" {{ old('icon', $skill->icon) === 'fas fa-rocket' ? 'selected' : '' }}>Rocket</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                                <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-100">
                                    <i id="icon_selected" class="{{ old('icon', $skill->icon ?? 'fas fa-code') }} text-gray-600"></i>
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Leave empty for default icon</p>
                            @error('icon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sort Order -->
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                                Sort Order
                            </label>
                            <input
                                type="number"
                                id="sort_order"
                                name="sort_order"
                                value="{{ old('sort_order', $skill->sort_order) }}"
                                min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                            @error('sort_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Featured -->
                        <div>
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="is_featured"
                                    name="is_featured"
                                    value="1"
                                    {{ old('is_featured', $skill->is_featured) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                                <span class="ml-2 text-sm text-gray-700">Mark as featured skill</span>
                            </label>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Submit Button -->
                <div class="sticky bottom-6">
                    <div class="bg-white p-4 rounded-lg shadow-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            <button
                                type="button"
                                onclick="window.history.back()"
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            
                            <button
                                type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium"
                            >
                                {{ $skill->id ? 'Update Skill' : 'Create Skill' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        function updatePreview() {
            // Update icon preview
            const icon = document.getElementById('icon').value || 'fas fa-code';
            document.getElementById('icon_preview').className = icon;
            document.getElementById('icon_selected').className = icon;
            
            // Update color preview
            const color = document.getElementById('color').value;
            const iconPreview = document.getElementById('icon_preview');
            const iconContainer = iconPreview.parentElement;
            const progressPreview = document.getElementById('progress_preview');
            
            iconPreview.style.color = color;
            iconContainer.style.color = color;
            iconContainer.style.backgroundColor = color + '20'; // Add opacity
            progressPreview.style.backgroundColor = color;
            
            // Update name preview
            const name = document.getElementById('name').value || 'Skill Name';
            document.getElementById('name_preview').textContent = name;
            
            // Update percentage preview
            const percentage = document.getElementById('percentage').value;
            document.getElementById('percentage_preview').textContent = percentage + '%';
            progressPreview.style.width = percentage + '%';
        }
        
        // Initialize preview
        document.addEventListener('DOMContentLoaded', function() {
            updatePreview();
            
            // Update preview on name change
            document.getElementById('name').addEventListener('input', updatePreview);
            document.getElementById('percentage').addEventListener('input', updatePreview);
            document.getElementById('percentage_slider').addEventListener('input', updatePreview);
        });
    </script>
    @endpush
</x-admin.layout>