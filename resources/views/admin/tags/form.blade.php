<x-admin.layout :title="$title ?? ($tag->id ? 'Edit Tag' : 'Create Tag')">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $tag->id ? 'Edit Tag' : 'Create New Tag' }}
                </h1>
                <p class="text-gray-600">
                    {{ $tag->id ? 'Update tag details' : 'Add a new tag' }}
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('admin.tags.index') }}" 
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                    Cancel
                </a>
            </div>
        </div>
    </x-slot>

    <form 
        action="{{ $tag->id ? route('admin.tags.update', $tag) : route('admin.tags.store') }}" 
        method="POST"
    >
        @csrf
        @if($tag->id)
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
                                Tag Name *
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $tag->name) }}"
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
                                value="{{ old('slug', $tag->slug) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                Tag Type *
                            </label>
                            <select
                                id="type"
                                name="type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                                <option value="">Select Type</option>
                                <option value="post" {{ old('type', $tag->type) === 'post' ? 'selected' : '' }}>Post Tag</option>
                                <option value="project" {{ old('type', $tag->type) === 'project' ? 'selected' : '' }}>Project Tag</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Usage Statistics -->
                @if($tag->id)
                    <x-admin.card title="Usage Statistics">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Total Usage</h3>
                                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $tag->total_count }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Posts</h3>
                                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $tag->post_count }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Projects</h3>
                                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $tag->project_count }}</p>
                            </div>
                        </div>
                    </x-admin.card>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Warning if tag has content -->
                @if($tag->id && ($tag->posts()->count() > 0 || $tag->projects()->count() > 0))
                    <x-admin.card title="Warning" class="border-yellow-200 bg-yellow-50">
                        <div class="flex">
                            <x-admin.icon name="exclamation" class="w-5 h-5 text-yellow-400 mr-2 flex-shrink-0" />
                            <div>
                                <p class="text-sm text-yellow-700 font-medium mb-1">
                                    This tag is currently in use
                                </p>
                                <ul class="text-sm text-yellow-600 space-y-1">
                                    @if($tag->posts()->count() > 0)
                                        <li>• Used in {{ $tag->posts()->count() }} post(s)</li>
                                    @endif
                                    @if($tag->projects()->count() > 0)
                                        <li>• Used in {{ $tag->projects()->count() }} project(s)</li>
                                    @endif
                                </ul>
                                <p class="text-sm text-yellow-600 mt-2">
                                    Changing the type may remove it from some content.
                                </p>
                            </div>
                        </div>
                    </x-admin.card>
                @endif

                <!-- Submit Button -->
                <div class="sticky bottom-6">
                    <div class="bg-white p-4 rounded-lg shadow-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            @if($tag->id)
                                <form 
                                    action="{{ route('admin.tags.destroy', $tag) }}" 
                                    method="POST" 
                                    class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this tag?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit" 
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center"
                                    >
                                        <x-admin.icon name="trash" class="w-5 h-5 mr-2" />
                                        Delete
                                    </button>
                                </form>
                            @else
                                <button
                                    type="button"
                                    onclick="window.history.back()"
                                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                                >
                                    Cancel
                                </button>
                            @endif
                            
                            <button
                                type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium"
                            >
                                {{ $tag->id ? 'Update Tag' : 'Create Tag' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quick Info -->
                <x-admin.card title="About Tags">
                    <div class="space-y-3">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-1">Post Tags</h4>
                            <p class="text-sm text-gray-600">
                                Used to categorize and organize blog posts. Multiple tags can be assigned to each post.
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-1">Project Tags</h4>
                            <p class="text-sm text-gray-600">
                                Used to categorize and organize projects. Helps users find related projects.
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-1">Best Practices</h4>
                            <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                <li>Use lowercase for tag names</li>
                                <li>Keep tags short and descriptive</li>
                                <li>Avoid duplicate or similar tags</li>
                                <li>Use hyphens for multi-word tags</li>
                            </ul>
                        </div>
                    </div>
                </x-admin.card>
            </div>
        </div>
    </form>

    @if($tag->id)
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const typeSelect = document.getElementById('type');
                const warningDiv = document.querySelector('[x-data="warning"]');
                
                typeSelect.addEventListener('change', function() {
                    const currentType = '{{ $tag->type }}';
                    const newType = this.value;
                    
                    if (currentType !== newType) {
                        const postsCount = {{ $tag->posts()->count() }};
                        const projectsCount = {{ $tag->projects()->count() }};
                        
                        let affectedContent = [];
                        
                        if (newType === 'project' && postsCount > 0) {
                            affectedContent.push(`${postsCount} post(s) will lose this tag`);
                        }
                        
                        if (newType === 'post' && projectsCount > 0) {
                            affectedContent.push(`${projectsCount} project(s) will lose this tag`);
                        }
                        
                        if (affectedContent.length > 0) {
                            if (!confirm(`Warning: Changing tag type will affect existing content:\n\n• ${affectedContent.join('\n• ')}\n\nAre you sure you want to continue?`)) {
                                this.value = currentType;
                            }
                        }
                    }
                });
            });
        </script>
        @endpush
    @endif
</x-admin.layout>