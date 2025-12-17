<x-admin.layout :title="$title ?? ($education->id ? 'Edit Education' : 'Add Education')">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $education->id ? 'Edit Education' : 'Add New Education' }}
                </h1>
                <p class="text-gray-600">
                    {{ $education->id ? 'Update education details' : 'Add a new education record' }}
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('admin.education.index') }}" 
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                    Cancel
                </a>
            </div>
        </div>
    </x-slot>

    <form 
        action="{{ $education->id ? route('admin.education.update', $education) : route('admin.education.store') }}" 
        method="POST"
    >
        @csrf
        @if($education->id)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <x-admin.card title="Education Details">
                    <div class="space-y-6">
                        <!-- Degree -->
                        <div>
                            <label for="degree" class="block text-sm font-medium text-gray-700 mb-1">
                                Degree/Certificate *
                            </label>
                            <input
                                type="text"
                                id="degree"
                                name="degree"
                                value="{{ old('degree', $education->degree) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                                placeholder="e.g., Bachelor of Science in Computer Science"
                            >
                            @error('degree')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Institution -->
                        <div>
                            <label for="institution" class="block text-sm font-medium text-gray-700 mb-1">
                                Institution *
                            </label>
                            <input
                                type="text"
                                id="institution"
                                name="institution"
                                value="{{ old('institution', $education->institution) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                                placeholder="e.g., Stanford University"
                            >
                            @error('institution')
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
                                value="{{ old('location', $education->location) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="e.g., Stanford, CA"
                            >
                            @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Describe your coursework, achievements, or relevant details..."
                            >{{ old('description', $education->description) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Optional: Include relevant coursework, honors, or activities</p>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Score -->
                        <div>
                            <label for="score" class="block text-sm font-medium text-gray-700 mb-1">
                                GPA/Score (Optional)
                            </label>
                            <div class="flex items-center space-x-4">
                                <div class="relative flex-1">
                                    <input
                                        type="range"
                                        id="score_slider"
                                        min="0"
                                        max="100"
                                        step="0.1"
                                        value="{{ old('score', $education->score ?? 0) }}"
                                        class="w-full"
                                        oninput="document.getElementById('score').value = this.value; updateScorePreview()"
                                    >
                                </div>
                                <div class="flex items-center space-x-2 w-32">
                                    <input
                                        type="number"
                                        id="score"
                                        name="score"
                                        value="{{ old('score', $education->score) }}"
                                        min="0"
                                        max="100"
                                        step="0.1"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="e.g., 3.8"
                                        oninput="document.getElementById('score_slider').value = this.value; updateScorePreview()"
                                    >
                                    <span class="text-sm font-medium text-gray-700">%</span>
                                </div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>0%</span>
                                <span>50%</span>
                                <span>100%</span>
                            </div>
                            @error('score')
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
                                value="{{ old('start_date', $education->start_date?->format('Y-m')) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Study -->
                        <div>
                            <label class="flex items-center mb-3">
                                <input
                                    type="checkbox"
                                    id="is_current"
                                    name="is_current"
                                    value="1"
                                    {{ old('is_current', $education->is_current) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    onchange="toggleEndDate()"
                                >
                                <span class="ml-2 text-sm text-gray-700">I am currently studying here</span>
                            </label>
                        </div>

                        <!-- End Date -->
                        <div id="end_date_container" style="{{ old('is_current', $education->is_current) ? 'display: none;' : '' }}">
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                                End Date / Expected Graduation
                            </label>
                            <input
                                type="month"
                                id="end_date"
                                name="end_date"
                                value="{{ old('end_date', $education->end_date?->format('Y-m')) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <p class="mt-1 text-sm text-gray-500">Leave empty if still studying</p>
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
                                {{ $education->duration ?? 'Calculating...' }}
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
                                value="{{ old('sort_order', $education->sort_order) }}"
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
                            <h3 class="font-medium text-gray-900" id="preview_degree">
                                {{ old('degree', $education->degree) ?: 'Degree/Certificate' }}
                            </h3>
                            <p class="text-sm text-blue-600" id="preview_institution">
                                {{ old('institution', $education->institution) ?: 'Institution Name' }}
                            </p>
                            <div class="flex items-center text-sm text-gray-600 mt-1">
                                <span id="preview_dates">
                                    {{ old('start_date', $education->start_date?->format('M Y')) ?: 'Start Date' }} - 
                                    {{ old('is_current', $education->is_current) ? 'Present' : (old('end_date', $education->end_date?->format('M Y')) ?: 'Graduation') }}
                                </span>
                                @if(old('location', $education->location))
                                    <span class="mx-2">•</span>
                                    <span id="preview_location">{{ old('location', $education->location) }}</span>
                                @endif
                            </div>
                        </div>
                        
                        @if(old('score', $education->score))
                        <div class="pt-2 border-t border-gray-100">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Score:</span>
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div 
                                            id="score_preview_bar"
                                            class="h-2 rounded-full bg-green-500"
                                            style="width: {{ min(old('score', $education->score) ?? 0, 100) }}%"
                                        ></div>
                                    </div>
                                    <span id="score_preview" class="font-medium text-gray-900">
                                        {{ number_format(old('score', $education->score) ?? 0, 2) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if(old('description', $education->description))
                        <div class="pt-2 border-t border-gray-100">
                            <p class="text-sm text-gray-600 line-clamp-2" id="preview_description">
                                {{ Str::limit(old('description', $education->description), 100) }}
                            </p>
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
                                {{ $education->id ? 'Update Education' : 'Add Education' }}
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

        function updateScorePreview() {
            const score = parseFloat(document.getElementById('score').value) || 0;
            const scorePreview = document.getElementById('score_preview');
            const scorePreviewBar = document.getElementById('score_preview_bar');
            
            if (scorePreview) {
                scorePreview.textContent = score.toFixed(2) + '%';
            }
            if (scorePreviewBar) {
                scorePreviewBar.style.width = Math.min(score, 100) + '%';
                
                // Change color based on score
                if (score >= 80) {
                    scorePreviewBar.className = 'h-2 rounded-full bg-green-500';
                } else if (score >= 60) {
                    scorePreviewBar.className = 'h-2 rounded-full bg-yellow-500';
                } else {
                    scorePreviewBar.className = 'h-2 rounded-full bg-red-500';
                }
            }
        }

        function calculateDuration(startDate, endDate, isCurrent) {
            if (!startDate) return 'Calculating...';
            
            const start = new Date(startDate + '-01');
            const end = isCurrent ? new Date() : (endDate ? new Date(endDate + '-01') : null);
            
            if (!end) return 'Ongoing';
            
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
            // Update degree and institution
            document.getElementById('preview_degree').textContent = 
                document.getElementById('degree').value || 'Degree/Certificate';
            document.getElementById('preview_institution').textContent = 
                document.getElementById('institution').value || 'Institution Name';
            document.getElementById('preview_location').textContent = 
                document.getElementById('location').value || '';
            document.getElementById('preview_description').textContent = 
                document.getElementById('description').value ? 
                Str.limit(document.getElementById('description').value, 100) : '';
            
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
                }) : 'Graduation');
            
            document.getElementById('preview_dates').textContent = 
                `${startFormatted} - ${endFormatted}`;
            
            // Update duration
            document.getElementById('duration_preview').textContent = 
                calculateDuration(startDate, endDate, isCurrent);
            
            // Update score preview
            updateScorePreview();
        }

        // Helper function for string limiting (since we don't have Laravel's Str in JS)
        const Str = {
            limit: function(text, limit) {
                if (text.length <= limit) return text;
                return text.substring(0, limit) + '...';
            }
        };

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Set up event listeners for preview
            ['degree', 'institution', 'location', 'description', 'start_date', 'end_date', 'score'].forEach(id => {
                document.getElementById(id)?.addEventListener('input', updatePreview);
            });
            
            document.getElementById('is_current')?.addEventListener('change', updatePreview);
            
            // Initial preview update
            updatePreview();
        });
    </script>
    @endpush
</x-admin.layout>