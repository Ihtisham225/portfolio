<header class="bg-white border-b border-gray-200">
    <div class="px-6 py-4">
        <div class="flex items-center justify-between">
            <!-- Left Side: Search & Breadcrumbs -->
            <div class="flex items-center space-x-4">
                <!-- Mobile Menu Button -->
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600">
                    <x-admin.icon name="menu" class="w-6 h-6" />
                </button>
                
                <!-- Breadcrumbs -->
                <nav class="hidden md:flex items-center space-x-2 text-sm">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900">
                        Dashboard
                    </a>
                    @if(isset($breadcrumbs))
                        @foreach($breadcrumbs as $breadcrumb)
                            <x-admin.icon name="chevron-right" class="w-4 h-4 text-gray-400" />
                            @if($breadcrumb['url'])
                                <a href="{{ $breadcrumb['url'] }}" class="text-gray-600 hover:text-gray-900">
                                    {{ $breadcrumb['label'] }}
                                </a>
                            @else
                                <span class="text-gray-900">{{ $breadcrumb['label'] }}</span>
                            @endif
                        @endforeach
                    @endif
                </nav>
            </div>
            
            <!-- Right Side: Actions & Notifications -->
            <div class="flex items-center space-x-4">
                <!-- Search -->
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search..."
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <x-admin.icon name="search" class="w-5 h-5" />
                    </div>
                </div>
                
                <!-- Notifications -->
                <button class="relative p-2 text-gray-600 hover:text-gray-900">
                    <x-admin.icon name="bell" class="w-6 h-6" />
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                
                <!-- Quick Actions -->
                <div class="relative" x-data="{ open: false }">
                    <button 
                        @click="open = !open"
                        class="flex items-center space-x-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        <x-admin.icon name="plus" class="w-5 h-5" />
                        <span>Quick Add</span>
                    </button>
                    
                    <!-- Dropdown -->
                    <div 
                        x-show="open"
                        @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                        x-cloak
                    >
                        <div class="py-1">
                            <a 
                                href="{{ route('admin.projects.create') }}" 
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                                <x-admin.icon name="folder-plus" class="w-4 h-4 mr-2" />
                                New Project
                            </a>
                            <a 
                                href="{{ route('admin.posts.create') }}" 
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                                <x-admin.icon name="file-plus" class="w-4 h-4 mr-2" />
                                New Post
                            </a>
                            <a 
                                href="{{ route('admin.media.index') }}" 
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                                <x-admin.icon name="upload" class="w-4 h-4 mr-2" />
                                Upload Media
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>