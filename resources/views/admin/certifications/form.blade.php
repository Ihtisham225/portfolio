<x-admin.layout :title="$title ?? ($certification->id ? 'Edit Certification' : 'Add Certification')">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $certification->id ? 'Edit Certification' : 'Add New Certification' }}
                </h1>
                <p class="text-gray-600">
                    {{ $certification->id ? 'Update certification details' : 'Add a new professional certification' }}
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('admin.certifications.index') }}" 
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                    Cancel
                </a>
            </div>
        </div>
    </x-slot>

    <form 
        action="{{ $certification->id ? route('admin.certifications.update', $certification) : route('admin.certifications.store') }}" 
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @if($certification->id)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <x-admin.card title="Certification Details">
                    <div class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Certification Name *
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $certification->name) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                                placeholder="e.g., AWS Certified Solutions Architect"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Issuer -->
                        <div>
                            <label for="issuer" class="block text-sm font-medium text-gray-700 mb-1">
                                Issuing Organization *
                            </label>
                            <input
                                type="text"
                                id="issuer"
                                name="issuer"
                                value="{{ old('issuer', $certification->issuer) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                                placeholder="e.g., Amazon Web Services"
                            >
                            @error('issuer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Credential ID -->
                        <div>
                            <label for="credential_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Credential ID
                            </label>
                            <input
                                type="text"
                                id="credential_id"
                                name="credential_id"
                                value="{{ old('credential_id', $certification->credential_id) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="e.g., AWS-12345-ABC"
                            >
                            <p class="mt-1 text-sm text-gray-500">Your unique certification/credential ID</p>
                            @error('credential_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Credential URL -->
                        <div>
                            <label for="credential_url" class="block text-sm font-medium text-gray-700 mb-1">
                                Verification URL
                            </label>
                            <input
                                type="url"
                                id="credential_url"
                                name="credential_url"
                                value="{{ old('credential_url', $certification->credential_url) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="https://verify.certification.com/your-id"
                            >
                            <p class="mt-1 text-sm text-gray-500">Link to verify this certification online</p>
                            @error('credential_url')
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
                        <!-- Issue Date -->
                        <div>
                            <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Issue Date *
                            </label>
                            <input
                                type="month"
                                id="issue_date"
                                name="issue_date"
                                value="{{ old('issue_date', $certification->issue_date?->format('Y-m')) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                            @error('issue_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Expiration Date -->
                        <div>
                            <label for="expiration_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Expiration Date
                            </label>
                            <input
                                type="month"
                                id="expiration_date"
                                name="expiration_date"
                                value="{{ old('expiration_date', $certification->expiration_date?->format('Y-m')) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <p class="mt-1 text-sm text-gray-500">Leave empty for certifications that don't expire</p>
                            @error('expiration_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Validity Status -->
                        <div class="pt-4 border-t border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Validity Status
                            </label>
                            <div class="text-sm" id="validity_status">
                                @if($certification->id)
                                    @if($certification->is_valid)
                                        <span class="text-green-600">
                                            {{ $certification->expiration_date ? 'Valid until ' . $certification->formatted_expiration_date : 'No Expiration' }}
                                        </span>
                                    @else
                                        <span class="text-red-600">
                                            Expired on {{ $certification->formatted_expiration_date }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-gray-500">Will calculate after dates are entered</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Image Upload -->
                <x-admin.card title="Certificate Image">
                    <div class="space-y-4">
                        <!-- Current Image -->
                        @if($certification->image_url)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                                <img 
                                    src="{{ $certification->image_url }}" 
                                    alt="{{ $certification->name }}"
                                    class="w-full h-48 object-contain rounded-lg border border-gray-200 bg-gray-50"
                                >
                            </div>
                        @endif

                        <!-- Image Upload -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ $certification->image_url ? 'Replace Image' : 'Upload Image' }}
                            </label>
                            <div class="mt-1 flex items-center">
                                <input
                                    type="file"
                                    id="image"
                                    name="image"
                                    accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                    onchange="previewImage(event)"
                                >
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Recommended: Square image, max 5MB</p>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image Preview -->
                        <div id="imagePreviewContainer" class="{{ !$certification->image_url ? 'hidden' : '' }}">
                            <p class="text-sm text-gray-600 mb-2">New Image Preview:</p>
                            <img 
                                id="imagePreview" 
                                class="w-full h-48 object-contain rounded-lg border border-gray-200 bg-gray-50"
                                alt="Image preview"
                            >
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
                                value="{{ old('sort_order', $certification->sort_order) }}"
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
                        <div class="flex items-start space-x-3">
                            @if($certification->image_url)
                                <img 
                                    src="{{ $certification->image_url }}" 
                                    alt="Preview"
                                    class="w-12 h-12 rounded-lg object-cover border border-gray-200"
                                    id="preview_image"
                                >
                            @else
                                <div class="w-12 h-12 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center">
                                    <x-admin.icon name="badge-check" class="w-6 h-6 text-gray-400" id="preview_icon" />
                                </div>
                            @endif
                            <div>
                                <h3 class="font-medium text-gray-900" id="preview_name">
                                    {{ old('name', $certification->name) ?: 'Certification Name' }}
                                </h3>
                                <p class="text-sm text-gray-600" id="preview_issuer">
                                    {{ old('issuer', $certification->issuer) ?: 'Issuing Organization' }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Issued:</span>
                                <span id="preview_issue_date" class="text-gray-900 ml-1">
                                    {{ old('issue_date', $certification->issue_date?->format('M Y')) ?: 'Date' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500">Expires:</span>
                                <span id="preview_expiration_date" class="text-gray-900 ml-1">
                                    {{ old('expiration_date', $certification->expiration_date?->format('M Y')) ?: 'No expiration' }}
                                </span>
                            </div>
                        </div>
                        
                        @if(old('credential_id', $certification->credential_id))
                            <div class="pt-2 border-t border-gray-100">
                                <div class="text-sm">
                                    <span class="text-gray-500">Credential ID:</span>
                                    <span id="preview_credential_id" class="text-gray-900 ml-1">
                                        {{ old('credential_id', $certification->credential_id) }}
                                    </span>
                                </div>
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
                                {{ $certification->id ? 'Update Certification' : 'Add Certification' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('imagePreview');
            const container = document.getElementById('imagePreviewContainer');
            const previewImage = document.getElementById('preview_image');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.classList.remove('hidden');
                    
                    // Update preview in sidebar
                    if (previewImage) {
                        previewImage.src = e.target.result;
                    }
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        function calculateValidityStatus() {
            const issueDate = document.getElementById('issue_date').value;
            const expirationDate = document.getElementById('expiration_date').value;
            const statusElement = document.getElementById('validity_status');
            
            if (!issueDate) {
                statusElement.innerHTML = '<span class="text-gray-500">Enter issue date to calculate validity</span>';
                return;
            }
            
            const issue = new Date(issueDate + '-01');
            const now = new Date();
            
            if (!expirationDate) {
                statusElement.innerHTML = '<span class="text-green-600">No Expiration</span>';
                return;
            }
            
            const expiration = new Date(expirationDate + '-01');
            const issueFormatted = issue.toLocaleDateString('en-US', { 
                month: 'short', 
                year: 'numeric' 
            });
            const expirationFormatted = expiration.toLocaleDateString('en-US', { 
                month: 'short', 
                year: 'numeric' 
            });
            
            if (expiration >= now) {
                // Calculate time until expiration
                const monthsUntilExpiration = Math.ceil((expiration - now) / (1000 * 60 * 60 * 24 * 30));
                const years = Math.floor(monthsUntilExpiration / 12);
                const months = monthsUntilExpiration % 12;
                
                let timeLeft = '';
                if (years > 0) {
                    timeLeft += `${years} ${years === 1 ? 'year' : 'years'}`;
                }
                if (months > 0) {
                    if (years > 0) timeLeft += ' ';
                    timeLeft += `${months} ${months === 1 ? 'month' : 'months'}`;
                }
                
                statusElement.innerHTML = `<span class="text-green-600">Valid until ${expirationFormatted} (${timeLeft} left)</span>`;
            } else {
                statusElement.innerHTML = `<span class="text-red-600">Expired on ${expirationFormatted}</span>`;
            }
        }

        function formatMonthYear(dateString) {
            if (!dateString) return 'No expiration';
            
            const date = new Date(dateString + '-01');
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                year: 'numeric' 
            });
        }

        function updatePreview() {
            // Update name and issuer
            document.getElementById('preview_name').textContent = 
                document.getElementById('name').value || 'Certification Name';
            document.getElementById('preview_issuer').textContent = 
                document.getElementById('issuer').value || 'Issuing Organization';
            
            // Update dates
            document.getElementById('preview_issue_date').textContent = 
                formatMonthYear(document.getElementById('issue_date').value) || 'Date';
            document.getElementById('preview_expiration_date').textContent = 
                formatMonthYear(document.getElementById('expiration_date').value) || 'No expiration';
            
            // Update credential ID
            const credentialId = document.getElementById('credential_id').value;
            const previewCredentialId = document.getElementById('preview_credential_id');
            if (previewCredentialId) {
                previewCredentialId.textContent = credentialId || '';
            }
            
            // Update validity status
            calculateValidityStatus();
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Set up event listeners for preview
            ['name', 'issuer', 'credential_id', 'issue_date', 'expiration_date'].forEach(id => {
                document.getElementById(id)?.addEventListener('input', updatePreview);
            });
            
            // Initial preview update
            updatePreview();
        });
    </script>
    @endpush
</x-admin.layout>