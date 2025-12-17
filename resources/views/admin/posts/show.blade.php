<x-admin.layout title="Post: {{ $post->title }}">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $post->title }}</h1>
                <div class="flex items-center space-x-4 mt-2">
                    <x-admin.badge :color="$post->status === 'published' ? 'green' : ($post->status === 'draft' ? 'yellow' : 'gray')">
                        {{ ucfirst($post->status) }}
                    </x-admin.badge>
                    @if($post->is_featured)
                        <x-admin.badge color="yellow">Featured</x-admin.badge>
                    @endif
                    <span class="text-sm text-gray-600">
                        Created {{ $post->created_at->diffForHumans() }}
                    </span>
                    <span class="text-sm text-gray-600">
                        {{ $post->views }} views
                    </span>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('blog.detail', $post->slug) }}" 
                    target="_blank"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center"
                >
                    <x-admin.icon name="eye" class="w-5 h-5 mr-2" />
                    Preview
                </a>
                <a 
                    href="{{ route('admin.posts.edit', $post) }}" 
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
            @if($post->featured_image)
                <x-admin.card>
                    <img 
                        src="{{ Storage::url($post->featured_image) }}" 
                        alt="{{ $post->title }}"
                        class="w-full h-auto rounded-lg"
                    >
                </x-admin.card>
            @endif

            <!-- Excerpt -->
            <x-admin.card title="Excerpt">
                <p class="text-gray-700">{{ $post->excerpt }}</p>
            </x-admin.card>

            <!-- Content -->
            <x-admin.card title="Content">
                <div class="prose max-w-none">
                    {!! $post->content !!}
                </div>
            </x-admin.card>

            <!-- Post Details -->
            <x-admin.card title="Post Details">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Author</h3>
                            <p class="mt-1 text-gray-900">{{ $post->user->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Reading Time</h3>
                            <p class="mt-1 text-gray-900">{{ $post->read_time }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        @if($post->published_at)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Published</h3>
                                <p class="mt-1 text-gray-900">{{ $post->published_at->format('F d, Y H:i') }}</p>
                                @if($post->published_at > now())
                                    <span class="text-sm text-yellow-600">(Scheduled)</span>
                                @endif
                            </div>
                        @endif
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Last Updated</h3>
                            <p class="mt-1 text-gray-900">{{ $post->updated_at->format('F d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <!-- SEO Information -->
            @if($post->meta_title || $post->meta_description || $post->meta_keywords)
                <x-admin.card title="SEO Information">
                    <div class="space-y-4">
                        @if($post->meta_title)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Meta Title</h3>
                                <p class="mt-1 text-gray-900">{{ $post->meta_title }}</p>
                            </div>
                        @endif
                        @if($post->meta_description)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Meta Description</h3>
                                <p class="mt-1 text-gray-900">{{ $post->meta_description }}</p>
                            </div>
                        @endif
                        @if($post->meta_keywords)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Meta Keywords</h3>
                                <p class="mt-1 text-gray-900">{{ $post->meta_keywords }}</p>
                            </div>
                        @endif
                    </div>
                </x-admin.card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Categories & Tags -->
            <x-admin.card title="Categories">
                <div class="space-y-2">
                    @foreach($post->categories as $category)
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full inline-block mr-2 mb-2">
                            {{ $category->name }}
                        </span>
                    @endforeach
                </div>
            </x-admin.card>

            <x-admin.card title="Tags">
                <div class="space-y-2">
                    @foreach($post->tags as $tag)
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full inline-block mr-2 mb-2">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            </x-admin.card>

            <!-- Quick Actions -->
            <x-admin.card title="Quick Actions">
                <div class="space-y-3">
                    @if($post->status === 'draft')
                        <form action="{{ route('admin.posts.update', $post) }}" method="POST" class="inline w-full">
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
                    
                    @if(!$post->is_featured)
                        <form action="{{ route('admin.posts.update', $post) }}" method="POST" class="inline w-full">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="is_featured" value="1">
                            <button 
                                type="submit" 
                                class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 flex items-center justify-center"
                            >
                                <x-admin.icon name="star" class="w-5 h-5 mr-2" />
                                Mark as Featured
                            </button>
                        </form>
                    @endif
                    
                    <a 
                        href="{{ route('admin.posts.edit', $post) }}" 
                        class="block w-full px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 text-center"
                    >
                        Edit Post
                    </a>
                    
                    <form 
                        action="{{ route('admin.posts.destroy', $post) }}" 
                        method="POST" 
                        class="inline w-full"
                        onsubmit="return confirm('Are you sure you want to delete this post?')"
                    >
                        @csrf
                        @method('DELETE')
                        <button 
                            type="submit" 
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center justify-center"
                        >
                            <x-admin.icon name="trash" class="w-5 h-5 mr-2" />
                            Delete Post
                        </button>
                    </form>
                </div>
            </x-admin.card>

            <!-- Statistics -->
            <x-admin.card title="Statistics">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Views</h3>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $post->views }}</p>
                    </div>
                    @if($post->comments_count !== null)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Comments</h3>
                            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $post->comments_count }}</p>
                        </div>
                    @endif
                </div>
            </x-admin.card>
        </div>
    </div>
</x-admin.layout>