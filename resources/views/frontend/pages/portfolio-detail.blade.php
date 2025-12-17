<x-frontend.layout :metaTags="$metaTags ?? []">
    <!-- Breadcrumb -->
    <x-frontend.breadcrumbs :items="[
        ['label' => 'projects', 'url' => route('portfolio')],
        ['label' => $project->title]
    ]" />

    <!-- Project Hero -->
    <section class="py-12">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">
                <div class="mb-8">
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($project->categories as $category)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                                {{ $category->name }}
                            </span>
                        @endforeach
                    </div>
                    <h1 class="text-4xl font-bold mb-4">{{ $project->title }}</h1>
                    <p class="text-xl text-gray-600 mb-6">{{ $project->excerpt }}</p>
                    
                    <div class="flex flex-wrap items-center gap-6 text-gray-500">
                        @if($project->client)
                            <div class="flex items-center">
                                <x-frontend.icon name="user" class="w-5 h-5 mr-2" />
                                <span>Client: {{ $project->client }}</span>
                            </div>
                        @endif
                        @if($project->project_date)
                            <div class="flex items-center">
                                <x-frontend.icon name="calendar" class="w-5 h-5 mr-2" />
                                <span>{{ $project->formatted_date }}</span>
                            </div>
                        @endif
                        @if($project->read_time)
                            <div class="flex items-center">
                                <x-frontend.icon name="clock" class="w-5 h-5 mr-2" />
                                <span>{{ $project->read_time }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Project Image -->
                <div class="mb-8 rounded-2xl overflow-hidden shadow-xl">
                    <img 
                        src="{{ $project->image_url }}" 
                        alt="{{ $project->title }}"
                        class="w-full h-auto object-cover"
                    >
                </div>
            </div>
        </div>
    </section>

    <!-- Project Details -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                    <!-- Main Content -->
                    <div class="lg:col-span-2">
                        <div class="prose prose-lg max-w-none">
                            <h2 class="text-2xl font-bold mb-6">Project Overview</h2>
                            <div class="text-gray-600 space-y-4">
                                {!! $project->description !!}
                            </div>
                        </div>

                        <!-- Gallery -->
                        @if($project->gallery_urls && count($project->gallery_urls) > 0)
                            <div class="mt-12">
                                <h3 class="text-2xl font-bold mb-6">Project Gallery</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($project->gallery_urls as $image)
                                        <div class="rounded-lg overflow-hidden">
                                            <img 
                                                src="{{ $image }}" 
                                                alt="Gallery Image {{ $loop->iteration }}"
                                                class="w-full h-64 object-cover hover:scale-105 transition duration-300"
                                            >
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-8">
                        <!-- Technologies -->
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                            <h3 class="text-xl font-bold mb-4">Technologies Used</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($project->technologies_array as $tech)
                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full">
                                        {{ $tech }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Project Links -->
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                            <h3 class="text-xl font-bold mb-4">Project Links</h3>
                            <div class="space-y-3">
                                @if($project->project_url)
                                    <a 
                                        href="{{ $project->project_url }}" 
                                        target="_blank"
                                        class="flex items-center justify-between p-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition"
                                    >
                                        <div class="flex items-center">
                                            <x-frontend.icon name="external-link" class="w-5 h-5 mr-3" />
                                            <span>Live Demo</span>
                                        </div>
                                        <x-frontend.icon name="arrow-right" class="w-5 h-5" />
                                    </a>
                                @endif
                                @if($project->github_url)
                                    <a 
                                        href="{{ $project->github_url }}" 
                                        target="_blank"
                                        class="flex items-center justify-between p-3 bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition"
                                    >
                                        <div class="flex items-center">
                                            <x-frontend.icon name="github" class="w-5 h-5 mr-3" />
                                            <span>Source Code</span>
                                        </div>
                                        <x-frontend.icon name="arrow-right" class="w-5 h-5" />
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Tags -->
                        @if($project->tags->isNotEmpty())
                            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                                <h3 class="text-xl font-bold mb-4">Tags</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($project->tags as $tag)
                                        <a 
                                            href="{{ route('portfolio', ['tag' => $tag->slug]) }}"
                                            class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full hover:bg-gray-200 transition"
                                        >
                                            {{ $tag->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Share -->
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                            <h3 class="text-xl font-bold mb-4">Share Project</h3>
                            <div class="flex space-x-4">
                                <a 
                                    href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $project->title }}"
                                    target="_blank"
                                    class="flex items-center justify-center w-10 h-10 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition"
                                >
                                    <x-frontend.icon name="twitter" class="w-5 h-5" />
                                </a>
                                <a 
                                    href="https://www.linkedin.com/sharing/share-offsite/?url={{ url()->current() }}"
                                    target="_blank"
                                    class="flex items-center justify-center w-10 h-10 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition"
                                >
                                    <x-frontend.icon name="linkedin" class="w-5 h-5" />
                                </a>
                                <a 
                                    href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}"
                                    target="_blank"
                                    class="flex items-center justify-center w-10 h-10 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition"
                                >
                                    <x-frontend.icon name="facebook" class="w-5 h-5" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Projects -->
    @if($relatedProjects->isNotEmpty())
        <section class="py-16">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl font-bold text-center mb-12">Related Projects</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($relatedProjects as $relatedProject)
                        <x-frontend.project-card :project="$relatedProject" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-frontend.layout>