@props(['items' => []])

<nav class="bg-gray-50 py-3">
    <div class="container mx-auto px-6">
        <ol class="flex items-center space-x-2 text-sm">
            <li>
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">
                    Home
                </a>
            </li>
            
            @foreach($items as $item)
                <li class="flex items-center">
                    <x-frontend.icon name="chevron-right" class="w-4 h-4 text-gray-400 mx-2" />
                    @if($item['url'] ?? false)
                        <a href="{{ $item['url'] }}" class="text-gray-600 hover:text-gray-900">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="text-gray-900">{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
</nav>