<x-admin.layout :title="$title ?? ($experience->id ? 'Edit Experience' : 'Add Experience')">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $experience->id ? 'Edit Experience' : 'Add New Experience' }}
                </h1>
                <p class="text-gray-600">
                    {{ $experience->id ? 'Update experience details' : 'Add a new work experience' }}
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('admin.experiences.index') }}" 
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                    Cancel
                </a>
            </div>
        </div>
    </x-slot>

    <form 
        action="{{ $experience->id ? route('admin.experiences.update', $experience) : route('admin.experiences.store') }}" 
        method="POST"
    >
        @csrf
        @if($experience->id)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <x-admin.card title="Basic Information">
                    <div class="space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                Job Title *
                            </label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title', $experience->title) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                                placeholder="e.g., Senior Software Engineer"
                            >
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Company -->
                        <div>
                            <label for="company" class="block text-sm font-medium text-gray-700 mb-1">
                                Company *
                            </label>
                            <input
                                type="text"
                                id="company"
                                name="company"
                                value="{{ old('company', $experience->company) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                                placeholder="e.g., TechCorp Inc."
                            >
                            @error('company')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                                Location
                            </label>
                            <input
                                type="text"
                                id="location"
                                name="location"
                                value="{{ old('location', $experience->location) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="e.g., San Francisco, CA or Remote"
                            >
                            @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description *
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="6"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                                placeholder="Describe your responsibilities and achievements..."
                            >{{ old('description', $experience->description) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Use bullet points or paragraphs to describe your role</p>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Technologies -->
                        <div>
                            <label for="technologies" class="block text-sm font-medium text-gray-700 mb-1">
                                Technologies & Skills
                            </label>
                            <div id="technologiesContainer" class="space-y-2">
                                <template id="technologyTemplate">
                                    <div class="flex items-center gap-2">
                                        <input
                                            type="text"
                                            name="technologies[]"
                                            placeholder="e.g., Laravel, React, MySQL"
                                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                        <button
                                            type="button"
                                            onclick="removeTechnology(this)"
                                            class="px-3 py-2 text-red-600 hover:text-red-800"
                                        >
                                            <x-admin.icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </template>
                                
                                <!-- Existing Technologies -->
                                @foreach(old('technologies', $experience->technologies_array ?? []) as $index => $tech)
                                    <div class="flex items-center gap-2">
                                        <input
                                            type="text"
                                            name="technologies[]"
                                            value="{{ $tech }}"
                                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="e.g., Laravel, React, MySQL"
                                        >
                                        <button
                                            type="button"
                                            onclick="removeTechnology(this)"
                                            class="px-3 py-2 text-red-600 hover:text-red-800"
                                        >
                                            <x-admin.icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                @endforeach
                                
                                <!-- Empty field for new entries -->
                                <div class="flex items-center gap-2">
                                    <input
                                        type="text"
                                        name="technologies[]"
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="e.g., Laravel, React, MySQL"
                                    >
                                    <button
                                        type="button"
                                        onclick="removeTechnology(this)"
                                        class="px-3 py-2 text-red-600 hover:text-red-800"
                                    >
                                        <x-admin.icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                            <button
                                type="button"
                                onclick="addTechnology()"
                                class="mt-2 px-4 py-2 text-sm text-blue-600 hover:text-blue-800 flex items-center"
                            >
                                <x-admin.icon name="plus" class="w-4 h-4 mr-1" />
                                Add Technology
                            </button>
                            <p class="mt-1 text-sm text-gray-500">List technologies, tools, or skills used in this role</p>
                            @error('technologies.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Timeline Information -->
                <x-admin.card title="Timeline">
                    <div class="space-y-6">
                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Start Date *
                            </label>
                            <input
                                type="month"
                                id="start_date"
                                name="start_date"
                                value="{{ old('start_date', $experience->start_date?->format('Y-m')) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Position -->
                        <div>
                            <label class="flex items-center mb-3">
                                <input
                                    type="checkbox"
                                    id="is_current"
                                    name="is_current"
                                    value="1"
                                    {{ old('is_current', $experience->is_current) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    onchange="toggleEndDate()"
                                >
                                <span class="ml-2 text-sm text-gray-700">I currently work here</span>
                            </label>
                        </div>

                        <!-- End Date -->
                        <div id="end_date_container" style="{{ old('is_current', $experience->is_current) ? 'display: none;' : '' }}">
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                                End Date
                            </label>
                            <input
                                type="month"
                                id="end_date"
                                name="end_date"
                                value="{{ old('end_date', $experience->end_date?->format('Y-m')) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <p class="mt-1 text-sm text-gray-500">Leave empty for "Present"</p>
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duration Preview -->
                        <div class="pt-4 border-t border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Duration Preview
                            </label>
                            <div class="text-sm text-gray-600" id="duration_preview">
                                {{ $experience->duration ?? 'Calculating...' }}
                            </div>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Settings -->
                <x-admin.card title="Settings">
                    <div class="space-y-4">
                        <!-- Sort Order -->
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                                Sort Order
                            </label>
                            <input
                                type="number"
                                id="sort_order"
                                name="sort_order"
                                value="{{ old('sort_order', $experience->sort_order) }}"
                                min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <p class="mt-1 text-sm text-gray-500">Lower numbers appear first in lists</p>
                            @error('sort_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Preview -->
                <x-admin.card title="Preview">
                    <div class="space-y-3">
                        <div>
                            <h3 class="font-medium text-gray-900" id="preview_title">
                                {{ old('title', $experience->title) ?: 'Job Title' }}
                            </h3>
                            <p class="text-sm text-blue-600" id="preview_company">
                                {{ old('company', $experience->company) ?: 'Company Name' }}
                            </p>
                            <div class="flex items-center text-sm text-gray-600 mt-1">
                                <span id="preview_dates">
                                    {{ old('start_date', $experience->start_date?->format('M Y')) ?: 'Start Date' }} - 
                                    {{ old('is_current', $experience->is_current) ? 'Present' : (old('end_date', $experience->end_date?->format('M Y')) ?: 'End Date') }}
                                </span>
                                <span class="mx-2">•</span>
                                <span id="preview_location">
                                    {{ old('location', $experience->location) ?: 'Location' }}
                                </span>
                            </div>
                        </div>
                        @if(old('technologies', $experience->technologies_array ?? []))
                            <div class="flex flex-wrap gap-1">
                                @foreach(old('technologies', $experience->technologies_array ?? []) as $tech)
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">
                                        {{ $tech }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
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
                                {{ $experience->id ? 'Update Experience' : 'Add Experience' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        function toggleEndDate() {
            const isCurrent = document.getElementById('is_current').checked;
            const endDateContainer = document.getElementById('end_date_container');
            const endDateInput = document.getElementById('end_date');
            
            if (isCurrent) {
                endDateContainer.style.display = 'none';
                endDateInput.value = '';
            } else {
                endDateContainer.style.display = 'block';
            }
            updatePreview();
        }

        function addTechnology() {
            const container = document.getElementById('technologiesContainer');
            const template = document.getElementById('technologyTemplate');
            const clone = template.content.cloneNode(true);
            container.appendChild(clone);
        }

        function removeTechnology(button) {
            const container = document.getElementById('technologiesContainer');
            const inputs = container.querySelectorAll('input[name="technologies[]"]');
            
            // Don't remove if it's the last one
            if (inputs.length > 1) {
                button.parentElement.remove();
            } else {
                // Clear the last input instead
                button.previousElementSibling.value = '';
            }
        }

        function calculateDuration(startDate, endDate, isCurrent) {
            if (!startDate) return 'Calculating...';
            
            const start = new Date(startDate + '-01');
            const end = isCurrent ? new Date() : (endDate ? new Date(endDate + '-01') : new Date());
            
            let years = end.getFullYear() - start.getFullYear();
            let months = end.getMonth() - start.getMonth();
            
            if (months < 0) {
                years--;
                months += 12;
            }
            
            if (years < 0) return 'Invalid date';
            
            const duration = [];
            if (years > 0) {
                duration.push(`${years} ${years === 1 ? 'year' : 'years'}`);
            }
            if (months > 0 || years === 0) {
                duration.push(`${months} ${months === 1 ? 'month' : 'months'}`);
            }
            
            return duration.join(' ') || 'Less than a month';
        }

        function updatePreview() {
            // Update title and company
            document.getElementById('preview_title').textContent = 
                document.getElementById('title').value || 'Job Title';
            document.getElementById('preview_company').textContent = 
                document.getElementById('company').value || 'Company Name';
            document.getElementById('preview_location').textContent = 
                document.getElementById('location').value || 'Location';
            
            // Update dates
            const startDate = document.getElementById('start_date').value;
            const isCurrent = document.getElementById('is_current').checked;
            const endDate = isCurrent ? null : document.getElementById('end_date').value;
            
            const startFormatted = startDate ? new Date(startDate + '-01').toLocaleDateString('en-US', { 
                month: 'short', 
                year: 'numeric' 
            }) : 'Start Date';
            
            const endFormatted = isCurrent ? 'Present' : 
                (endDate ? new Date(endDate + '-01').toLocaleDateString('en-US', { 
                    month: 'short', 
                    year: 'numeric' 
                }) : 'Present');
            
            document.getElementById('preview_dates').textContent = 
                `${startFormatted} - ${endFormatted}`;
            
            // Update duration
            document.getElementById('duration_preview').textContent = 
                calculateDuration(startDate, endDate, isCurrent);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Set up event listeners for preview
            ['title', 'company', 'location', 'start_date', 'end_date'].forEach(id => {
                document.getElementById(id)?.addEventListener('input', updatePreview);
            });
            
            document.getElementById('is_current')?.addEventListener('change', updatePreview);
            
            // Initial preview update
            updatePreview();
        });
    </script>
    @endpush
</x-admin.layout>