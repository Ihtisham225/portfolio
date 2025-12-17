<aside class="w-64 bg-gray-900 text-white flex flex-col">
    <!-- Logo -->
    <div class="p-6 border-b border-gray-800">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
            <x-admin.icon name="briefcase" class="w-8 h-8 text-blue-400" />
            <span class="text-xl font-bold">{{ config('app.name') }}</span>
            <span class="text-xs bg-blue-500 text-white px-2 py-1 rounded">Admin</span>
        </a>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <!-- Dashboard -->
        <x-admin.sidebar-item 
            href="{{ route('admin.dashboard') }}" 
            :active="request()->routeIs('admin.dashboard')"
            icon="dashboard"
        >
            Dashboard
        </x-admin.sidebar-item>
        
        <!-- Content Section -->
        <x-admin.sidebar-section title="Content">
            <x-admin.sidebar-item 
                href="{{ route('admin.projects.index') }}" 
                :active="request()->routeIs('admin.projects.*')"
                icon="folder"
            >
                Projects
            </x-admin.sidebar-item>
            
            <x-admin.sidebar-item 
                href="{{ route('admin.posts.index') }}" 
                :active="request()->routeIs('admin.posts.*')"
                icon="file-text"
            >
                Blog Posts
            </x-admin.sidebar-item>
            
            <x-admin.sidebar-item 
                href="{{ route('admin.categories.index') }}" 
                :active="request()->routeIs('admin.categories.*')"
                icon="tag"
            >
                Categories
            </x-admin.sidebar-item>
            
            <x-admin.sidebar-item 
                href="{{ route('admin.tags.index') }}" 
                :active="request()->routeIs('admin.tags.*')"
                icon="tags"
            >
                Tags
            </x-admin.sidebar-item>
        </x-admin.sidebar-section>
        
        <!-- Portfolio Section -->
        <x-admin.sidebar-section title="Portfolio">
            <x-admin.sidebar-item 
                href="{{ route('admin.skills.index') }}" 
                :active="request()->routeIs('admin.skills.*')"
                icon="star"
            >
                Skills
            </x-admin.sidebar-item>
            
            <x-admin.sidebar-item 
                href="{{ route('admin.experiences.index') }}" 
                :active="request()->routeIs('admin.experiences.*')"
                icon="briefcase"
            >
                Experiences
            </x-admin.sidebar-item>
            
            <x-admin.sidebar-item 
                href="{{ route('admin.education.index') }}" 
                :active="request()->routeIs('admin.education.*')"
                icon="graduation-cap"
            >
                Education
            </x-admin.sidebar-item>
            
            <x-admin.sidebar-item 
                href="{{ route('admin.certifications.index') }}" 
                :active="request()->routeIs('admin.certifications.*')"
                icon="certificate"
            >
                Certifications
            </x-admin.sidebar-item>
        </x-admin.sidebar-section>
        
        <!-- Communications -->
        <x-admin.sidebar-section title="Communications">
            <x-admin.sidebar-item 
                href="{{ route('admin.messages.index') }}" 
                :active="request()->routeIs('admin.messages.*')"
                icon="mail"
                badge="{{ App\Models\Message::unread()->count() }}"
                badge-color="red"
            >
                Messages
            </x-admin.sidebar-item>
        </x-admin.sidebar-section>
        
        <!-- Media -->
        <x-admin.sidebar-item 
            href="{{ route('admin.media.index') }}" 
            :active="request()->routeIs('admin.media.*')"
            icon="image"
        >
            Media Library
        </x-admin.sidebar-item>
        
        <!-- Settings -->
        <x-admin.sidebar-item 
            href="{{ route('admin.settings.index') }}" 
            :active="request()->routeIs('admin.settings.index')"
            icon="settings"
        >
            Settings
        </x-admin.sidebar-item>
    </nav>
    
    <!-- User Profile -->
    <div class="p-4 border-t border-gray-800">
        <div class="flex items-center space-x-3">
            <!-- Link to profile -->
            <a href="{{ route('profile.edit') }}" class="flex-1 flex items-center space-x-3 hover:bg-gray-700 p-2 rounded">
                <img 
                    src="{{ auth()->user()->avatar_url }}" 
                    alt="{{ auth()->user()->name }}" 
                    class="w-10 h-10 rounded-full"
                >
                <div>
                    <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                </div>
            </a>

            <!-- Logout button stays outside the link -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-white">
                    <x-admin.icon name="log-out" class="w-5 h-5" />
                </button>
            </form>
        </div>
    </div>

</aside>