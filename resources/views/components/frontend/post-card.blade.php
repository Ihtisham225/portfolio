@props(['post'])

<article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
    <div class="h-48 overflow-hidden">
        @if($post->optimized_image ?? $post->featured_image)
            <img 
                src="{{ $post->optimized_image ?? Storage::url($post->featured_image) }}" 
                alt="{{ $post->title }}" 
                class="w-full h-full object-cover hover:scale-110 transition duration-500"
            >
        @else
            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                <x-frontend.icon name="file-text" class="w-12 h-12 text-gray-400" />
            </div>
        @endif
    </div>
    
    <div class="p-6">
        <div class="flex items-center text-sm text-gray-500 mb-3">
            <x-frontend.icon name="calendar" class="w-4 h-4 mr-2" />
            <span>{{ $post->published_at->format('M d, Y') }}</span>
            <span class="mx-2">•</span>
            <x-frontend.icon name="clock" class="w-4 h-4 mr-2" />
            <span>{{ $post->read_time }}</span>
        </div>
        
        <h3 class="text-xl font-bold mb-3">{{ $post->title }}</h3>
        <p class="text-gray-600 mb-4">{{ Str::limit($post->excerpt, 100) }}</p>
        
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach($post->categories->take(2) as $category)
                <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full">
                    {{ $category->name }}
                </span>
            @endforeach
        </div>
        
        <a 
            href="{{ route('blog.detail', $post->slug) }}" 
            class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800"
        >
            Read More
            <x-frontend.icon name="arrow-right" class="w-4 h-4 ml-2" />
        </a>
    </div>
</article>