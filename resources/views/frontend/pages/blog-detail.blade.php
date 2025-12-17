<x-frontend.layout :metaTags="$metaTags ?? []">
    <!-- Breadcrumb -->
    <x-frontend.breadcrumbs :items="[
        ['label' => 'Blog', 'url' => route('blog')],
        ['label' => $post->title]
    ]" />

    <!-- Article -->
    <section class="py-12">
        <div class="container mx-auto px-6">
            <article class="max-w-4xl mx-auto">
                <!-- Header -->
                <header class="mb-8">
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($post->categories as $category)
                            <a 
                                href="{{ route('blog', ['category' => $category->slug]) }}"
                                class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full hover:bg-blue-200 transition"
                            >
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                    <h1 class="text-4xl font-bold mb-4">{{ $post->title }}</h1>
                    <div class="flex flex-wrap items-center gap-6 text-gray-500">
                        <div class="flex items-center">
                            <img 
                                src="{{ $user->avatar_url }}" 
                                alt="{{ $user->name ?? 'Profile Image' }}"
                                class="w-8 h-8 rounded-full mr-3"
                            >
                            <span>{{ $user->name ?? 'John Doe' }}</span>
                        </div>
                        <div class="flex items-center">
                            <x-frontend.icon name="calendar" class="w-5 h-5 mr-2" />
                            <span>{{ $post->published_at->format('F j, Y') }}</span>
                        </div>
                        <div class="flex items-center">
                            <x-frontend.icon name="clock" class="w-5 h-5 mr-2" />
                            <span>{{ $post->read_time }}</span>
                        </div>
                        <div class="flex items-center">
                            <x-frontend.icon name="eye" class="w-5 h-5 mr-2" />
                            <span>{{ $post->views }} views</span>
                        </div>
                    </div>
                </header>

                <!-- Featured Image -->
                @if($post->featured_image_url)
                    <div class="mb-8 rounded-2xl overflow-hidden shadow-xl">
                        <img 
                            src="{{ $post->featured_image_url }}" 
                            alt="{{ $post->title }}"
                            class="w-full h-auto object-cover"
                        >
                    </div>
                @endif

                <!-- Content -->
                <div class="prose prose-lg max-w-none mb-12">
                    {!! $post->content !!}
                </div>

                <!-- Tags -->
                @if($post->tags->isNotEmpty())
                    <div class="mb-12">
                        <h3 class="text-lg font-bold mb-4">Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($post->tags as $tag)
                                <a 
                                    href="{{ route('blog', ['tag' => $tag->slug]) }}"
                                    class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full hover:bg-gray-200 transition"
                                >
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Share -->
                <div class="mb-12 p-6 bg-gray-50 rounded-2xl">
                    <h3 class="text-lg font-bold mb-4">Share this article</h3>
                    <div class="flex space-x-4">
                        <a 
                            href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $post->title }}"
                            target="_blank"
                            class="flex items-center px-4 py-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition"
                        >
                            <x-frontend.icon name="twitter" class="w-5 h-5 mr-2" />
                            Twitter
                        </a>
                        <a 
                            href="https://www.linkedin.com/sharing/share-offsite/?url={{ url()->current() }}"
                            target="_blank"
                            class="flex items-center px-4 py-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition"
                        >
                            <x-frontend.icon name="linkedin" class="w-5 h-5 mr-2" />
                            LinkedIn
                        </a>
                        <a 
                            href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}"
                            target="_blank"
                            class="flex items-center px-4 py-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition"
                        >
                            <x-frontend.icon name="facebook" class="w-5 h-5 mr-2" />
                            Facebook
                        </a>
                    </div>
                </div>

                <!-- Author Bio -->
                <div class="mb-12 p-6 bg-white rounded-2xl shadow-sm border border-gray-200">
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                        <img 
                            src="{{ $user->avatar_url }}" 
                            alt="{{ $user->name ?? 'Profile Image' }}"
                            class="w-20 h-20 rounded-full"
                        >
                        <div>
                            <h3 class="text-xl font-bold mb-2">{{ $user->name ?? 'John Doe' }}</h3>
                            <p class="text-gray-600 mb-4">{{ $user->title ?? 'Full Stack Developer' }}</p>
                            <p class="text-gray-600">{{ $user->bio_short ?? 'Passionate about web development, sharing knowledge through articles and tutorials.' }}</p>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <!-- Related Posts -->
    @if($relatedPosts->isNotEmpty())
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl font-bold text-center mb-12">Related Articles</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($relatedPosts as $relatedPost)
                        <x-frontend.post-card :post="$relatedPost" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-frontend.layout>