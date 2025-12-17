<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
    @forelse($files as $file)
        <div class="media-item cursor-pointer border border-gray-200 rounded-lg overflow-hidden hover:border-blue-500 hover:shadow-md transition-all"
             data-path="{{ $file['path'] }}"
             data-url="{{ $file['url'] }}"
             title="{{ $file['name'] }}">
            <div class="aspect-w-1 aspect-h-1 bg-gray-100">
                <img src="{{ $file['url'] }}" 
                     alt="{{ $file['name'] }}"
                     class="object-cover w-full h-32">
            </div>
            <div class="p-2">
                <p class="text-xs text-gray-600 truncate">{{ $file['name'] }}</p>
                <p class="text-xs text-gray-400">{{ $file['size'] }}</p>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No images found</h3>
            <p class="mt-1 text-sm text-gray-500">Upload some images first.</p>
        </div>
    @endforelse
</div>