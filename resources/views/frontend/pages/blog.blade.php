<x-frontend.layout :metaTags="$metaTags ?? []">
    <!-- Content -->
    <section class="py-16">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <!-- Filters -->
                    <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-12">
                        <div class="flex flex-wrap gap-2">
                            <a 
                                href="{{ route('blog') }}"
                                class="px-4 py-2 rounded-lg font-medium transition
                                    {{ !request('category') && !request('tag') ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}"
                            >
                                All Posts
                            </a>
                            @foreach($categories as $category)
                                <a 
                                    href="{{ route('blog', ['category' => $category->slug]) }}"
                                    class="px-4 py-2 rounded-lg font-medium transition
                                        {{ request('category') == $category->slug ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}"
                                >
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>

                        <form method="GET" class="w-full md:w-auto">
                            <div class="relative">
                                <input
                                    type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Search articles..."
                                    class="w-full md:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                <x-frontend.icon name="search" class="w-5 h-5 text-gray-400 absolute left-3 top-3" />
                            </div>
                        </form>
                    </div>

                    <!-- Posts Grid -->
                    @if($posts->isEmpty())
                        <div class="text-center py-20">
                            <x-frontend.icon name="file-text" class="w-16 h-16 text-gray-400 mx-auto mb-4" />
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">No Articles Found</h3>
                            <p class="text-gray-600 max-w-md mx-auto">
                                @if(request('search'))
                                    No articles match your search criteria. Try a different search term.
                                @else
                                    No articles are available in this category. Check back soon!
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @foreach($posts as $post)
                                <x-frontend.post-card :post="$post" />
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($posts->hasPages())
                            <div class="mt-12">
                                {{ $posts->links() }}
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- About Author -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                        <div class="flex items-center mb-4">
                            <img 
                                src="{{ $user->avatar_url }}" 
                                alt="{{ $user->name ?? 'Profile Image' }}"
                                class="w-12 h-12 rounded-full mr-4"
                            >
                            <div>
                                <h3 class="font-bold">{{ $user->name ?? 'John Doe' }}</h3>
                                <p class="text-sm text-gray-600">{{ $user->title ?? 'Full Stack Developer' }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm">
                            {{ $user->bio_short ?? 'Passionate about web development, sharing knowledge through articles and tutorials.' }}
                        </p>
                    </div>

                    <!-- Popular Posts -->
                    @if($popularPosts->isNotEmpty())
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                            <h3 class="text-xl font-bold mb-4">Popular Posts</h3>
                            <div class="space-y-4">
                                @foreach($popularPosts as $popularPost)
                                    <a 
                                        href="{{ route('blog.detail', $popularPost->slug) }}"
                                        class="flex items-center group"
                                    >
                                        @if($popularPost->featured_image)
                                            <img 
                                                src="{{ Storage::url($popularPost->featured_image) }}" 
                                                alt="{{ $popularPost->title }}"
                                                class="w-16 h-16 rounded-lg object-cover mr-4"
                                            >
                                        @endif
                                        <div>
                                            <h4 class="font-medium group-hover:text-blue-600 transition">
                                                {{ Str::limit($popularPost->title, 50) }}
                                            </h4>
                                            <div class="text-sm text-gray-500">
                                                {{ $popularPost->published_at->format('M d, Y') }}
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Categories -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                        <h3 class="text-xl font-bold mb-4">Categories</h3>
                        <div class="space-y-2">
                            @foreach($allCategories as $category)
                                <a 
                                    href="{{ route('blog', ['category' => $category->slug]) }}"
                                    class="flex justify-between items-center p-2 hover:bg-gray-50 rounded-lg transition"
                                >
                                    <span>{{ $category->name }}</span>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">
                                        {{ $category->posts_count }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Tags -->
                    @if($tags->isNotEmpty())
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                            <h3 class="text-xl font-bold mb-4">Popular Tags</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($tags as $tag)
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
                </div>
            </div>
        </div>
    </section>
</x-frontend.layout>