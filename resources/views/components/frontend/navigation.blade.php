<nav class="bg-white shadow-lg sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
    <div class="container mx-auto px-6">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <!-- In your layout or header -->
                <div class="relative w-12 h-12">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-2xl transform rotate-3"></div>
                    <div class="absolute inset-1 bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none">
                            <path d="M10 20L14 4M18 8L22 12L18 16M6 16L2 12L6 8" 
                                stroke="url(#gradient)" 
                                stroke-width="2" 
                                stroke-linecap="round" 
                                stroke-linejoin="round"/>
                            <defs>
                                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#3B82F6" />
                                    <stop offset="50%" stop-color="#8B5CF6" />
                                    <stop offset="100%" stop-color="#EC4899" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <x-frontend.nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                    Home
                </x-frontend.nav-link>
                <x-frontend.nav-link href="{{ route('blog') }}" :active="request()->routeIs('blog')">
                    Blog
                </x-frontend.nav-link>
                <x-frontend.nav-link href="{{ route('contact') }}" :active="request()->routeIs('contact')">
                    Contact
                </x-frontend.nav-link>
            </div>
            
            <!-- Mobile menu button -->
            <button 
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="md:hidden text-gray-700 hover:text-blue-600"
            >
                <x-frontend.icon name="menu" class="w-6 h-6" />
            </button>
        </div>
    </div>
    
    <!-- Mobile menu -->
    <div 
        x-show="mobileMenuOpen"
        @click.away="mobileMenuOpen = false"
        class="md:hidden bg-white border-t border-gray-200"
        x-cloak
    >
        <div class="px-2 pt-2 pb-3 space-y-1">
            <x-frontend.nav-link href="{{ route('home') }}" :active="request()->routeIs('home')" mobile>
                Home
            </x-frontend.nav-link>
            <x-frontend.nav-link href="{{ route('blog') }}" :active="request()->routeIs('blog')" mobile>
                Blog
            </x-frontend.nav-link>
            <x-frontend.nav-link href="{{ route('contact') }}" :active="request()->routeIs('contact')" mobile>
                Contact
            </x-frontend.nav-link>
        </div>
    </div>
</nav>