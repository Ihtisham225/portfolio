@props(['project'])

<article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
    <div class="relative h-48 overflow-hidden">
        @if($project->optimized_image ?? $project->image)
            <img 
                src="{{ $project->optimized_image ?? Storage::url($project->image) }}" 
                alt="{{ $project->title }}" 
                class="w-full h-full object-cover hover:scale-110 transition duration-500"
            >
        @else
            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                <x-frontend.icon name="folder" class="w-12 h-12 text-gray-400" />
            </div>
        @endif
        
        <div class="absolute top-4 right-4">
            @foreach($project->technologies_array->take(3) as $tech)
                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded mr-1 mb-1 inline-block">
                    {{ $tech }}
                </span>
            @endforeach
        </div>
    </div>
    
    <div class="p-6">
        <div class="flex items-center mb-3">
            @if($project->client)
                <span class="text-sm text-gray-500">{{ $project->client }}</span>
            @endif
            @if($project->project_date)
                <span class="mx-2 text-gray-300">•</span>
                <span class="text-sm text-gray-500">{{ $project->formatted_date }}</span>
            @endif
        </div>
        
        <h3 class="text-xl font-bold mb-3">{{ $project->title }}</h3>
        <p class="text-gray-600 mb-4">{{ Str::limit($project->excerpt, 100) }}</p>
        
        <div class="flex justify-between items-center">
            <a 
                href="{{ route('portfolio.detail', $project->slug) }}" 
                class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800"
            >
                View Project
                <x-frontend.icon name="arrow-right" class="w-4 h-4 ml-2" />
            </a>
            
            @if($project->project_url)
                <a 
                    href="{{ $project->project_url }}" 
                    target="_blank"
                    class="text-gray-400 hover:text-gray-600"
                    title="Live Demo"
                >
                    <x-frontend.icon name="external-link" class="w-5 h-5" />
                </a>
            @endif
        </div>
    </div>
</article>