<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name') }} Admin</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <x-admin.sidebar />
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <x-admin.header />
            
            <!-- Page Content -->
            <main class="flex-1 p-6">
                <!-- Page Header -->
                @if(isset($header))
                    <div class="mb-6">
                        {{ $header }}
                    </div>
                @endif
                
                <!-- Page Content -->
                {{ $slot }}
            </main>
            
            <!-- Footer -->
            <x-admin.footer />
        </div>
    </div>
    
    <!-- Scripts -->
    @stack('scripts')
    
    <!-- Toast notifications -->
    <x-admin.toast />
</body>
</html>