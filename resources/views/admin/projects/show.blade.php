<x-admin.layout title="Project: {{ $project->title }}">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $project->title }}</h1>
                <div class="flex items-center space-x-4 mt-2">
                    <x-admin.badge :color="$project->status === 'published' ? 'green' : ($project->status === 'draft' ? 'yellow' : 'gray')">
                        {{ ucfirst($project->status) }}
                    </x-admin.badge>
                    <span class="text-sm text-gray-600">
                        Created {{ $project->created_at->diffForHumans() }}
                    </span>
                    <span class="text-sm text-gray-600">
                        {{ $project->views }} views
                    </span>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('portfolio.detail', $project->slug) }}" 
                    target="_blank"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center"
                >
                    <x-admin.icon name="eye" class="w-5 h-5 mr-2" />
                    Preview
                </a>
                <a 
                    href="{{ route('admin.projects.edit', $project) }}" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center"
                >
                    <x-admin.icon name="edit" class="w-5 h-5 mr-2" />
                    Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Featured Image -->
            @if($project->image)
                <x-admin.card>
                    <img 
                        src="{{ Storage::url($project->image) }}" 
                        alt="{{ $project->title }}"
                        class="w-full h-auto rounded-lg"
                    >
                </x-admin.card>
            @endif

            <!-- Description -->
            <x-admin.card title="Description">
                <div class="prose max-w-none">
                    {!! $project->description !!}
                </div>
            </x-admin.card>

            <!-- Project Details -->
            <x-admin.card title="Project Details">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Client</h3>
                            <p class="mt-1 text-gray-900">{{ $project->client ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Project Date</h3>
                            <p class="mt-1 text-gray-900">{{ $project->formatted_date ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Sort Order</h3>
                            <p class="mt-1 text-gray-900">{{ $project->sort_order }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        @if($project->project_url)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Live URL</h3>
                                <a 
                                    href="{{ $project->project_url }}" 
                                    target="_blank"
                                    class="mt-1 text-blue-600 hover:text-blue-800 flex items-center"
                                >
                                    {{ $project->project_url }}
                                    <x-admin.icon name="external-link" class="w-4 h-4 ml-1" />
                                </a>
                            </div>
                        @endif
                        @if($project->github_url)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">GitHub</h3>
                                <a 
                                    href="{{ $project->github_url }}" 
                                    target="_blank"
                                    class="mt-1 text-blue-600 hover:text-blue-800 flex items-center"
                                >
                                    View on GitHub
                                    <x-admin.icon name="external-link" class="w-4 h-4 ml-1" />
                                </a>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Last Updated</h3>
                            <p class="mt-1 text-gray-900">{{ $project->updated_at->format('F d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <!-- Technologies -->
            @if($project->technologies && count($project->technologies) > 0)
                <x-admin.card title="Technologies Used">
                    <div class="flex flex-wrap gap-2">
                        @foreach($project->technologies as $technology)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                                {{ $technology }}
                            </span>
                        @endforeach
                    </div>
                </x-admin.card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Categories & Tags -->
            <x-admin.card title="Categories">
                <div class="space-y-2">
                    @foreach($project->categories as $category)
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full inline-block mr-2 mb-2">
                            {{ $category->name }}
                        </span>
                    @endforeach
                </div>
            </x-admin.card>

            <x-admin.card title="Tags">
                <div class="space-y-2">
                    @foreach($project->tags as $tag)
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full inline-block mr-2 mb-2">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            </x-admin.card>

            <!-- Gallery -->
            @if($project->gallery && count($project->gallery) > 0)
                <x-admin.card title="Gallery Images">
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($project->gallery as $image)
                            <div class="relative">
                                <img 
                                    src="{{ Storage::url($image) }}" 
                                    alt="Gallery Image"
                                    class="w-full h-20 object-cover rounded-lg"
                                >
                            </div>
                        @endforeach
                    </div>
                </x-admin.card>
            @endif

            <!-- Quick Actions -->
            <x-admin.card title="Quick Actions">
                <div class="space-y-3">
                    @if($project->status === 'draft')
                        <form action="{{ route('admin.projects.update', $project) }}" method="POST" class="inline w-full">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="published">
                            <button 
                                type="submit" 
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center justify-center"
                            >
                                <x-admin.icon name="check" class="w-5 h-5 mr-2" />
                                Publish Now
                            </button>
                        </form>
                    @endif
                    
                    <a 
                        href="{{ route('admin.projects.edit', $project) }}" 
                        class="block w-full px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 text-center"
                    >
                        Edit Project
                    </a>
                    
                    <form 
                        action="{{ route('admin.projects.destroy', $project) }}" 
                        method="POST" 
                        class="inline w-full"
                        onsubmit="return confirm('Are you sure you want to delete this project?')"
                    >
                        @csrf
                        @method('DELETE')
                        <button 
                            type="submit" 
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center justify-center"
                        >
                            <x-admin.icon name="trash" class="w-5 h-5 mr-2" />
                            Delete Project
                        </button>
                    </form>
                </div>
            </x-admin.card>
        </div>
    </div>
</x-admin.layout>