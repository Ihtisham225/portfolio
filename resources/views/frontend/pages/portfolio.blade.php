<x-frontend.layout :metaTags="$metaTags ?? []">
    <!-- Filters -->
    <section class="py-8 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <!-- Category Filter -->
                <div class="flex flex-wrap gap-2">
                    <a 
                        href="{{ route('portfolio') }}"
                        class="px-4 py-2 rounded-lg font-medium transition
                            {{ !request('category') && !request('tag') ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}"
                    >
                        All Projects
                    </a>
                    @foreach($categories as $category)
                        <a 
                            href="{{ route('portfolio', ['category' => $category->slug]) }}"
                            class="px-4 py-2 rounded-lg font-medium transition
                                {{ request('category') == $category->slug ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}"
                        >
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>

                <!-- Search -->
                <form method="GET" class="w-full md:w-auto">
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search projects..."
                            class="w-full md:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        <x-frontend.icon name="search" class="w-5 h-5 text-gray-400 absolute left-3 top-3" />
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Projects Grid -->
    <section class="py-16">
        <div class="container mx-auto px-6">
            @if($projects->isEmpty())
                <div class="text-center py-20">
                    <x-frontend.icon name="folder" class="w-16 h-16 text-gray-400 mx-auto mb-4" />
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No Projects Found</h3>
                    <p class="text-gray-600 max-w-md mx-auto">
                        @if(request('search'))
                            No projects match your search criteria. Try a different search term.
                        @else
                            No projects are available in this category. Check back soon!
                        @endif
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($projects as $project)
                        <x-frontend.project-card :project="$project" />
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($projects->hasPages())
                    <div class="mt-12">
                        {{ $projects->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>
</x-frontend.layout>