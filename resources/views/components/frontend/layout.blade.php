<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        // Get settings
        $siteName = setting('site_name', config('app.name'));
        $siteTitle = setting('site_title', '');
        $siteDescription = setting('site_description', '');
        $defaultSeoTitle = setting('default_title', '');
        $defaultSeoDescription = setting('default_description', '');
        $defaultSeoKeywords = setting('default_keywords', '');
        $googleAnalyticsId = setting('google_analytics_id', '');
        $googleSiteVerification = setting('google_site_verification', '');
        
        // Determine page title
        $pageTitle = $metaTags['title'] ?? ($siteTitle ?: $siteName);
        $fullTitle = $pageTitle === $siteName ? $siteName : $pageTitle . ' - ' . $siteName;
        
        // Determine meta description
        $metaDescription = $metaTags['description'] ?? $defaultSeoDescription ?: $siteDescription;
        
        // Determine meta keywords
        $metaKeywords = $metaTags['keywords'] ?? $defaultSeoKeywords;
        
        // Get social settings
        $socialSettings = function_exists('settings_group') ? settings_group('social') : [];
        $twitterUsername = $socialSettings['twitter'] ?? '';
    @endphp
    
    <title>{{ $fullTitle }}</title>
    
    <!-- Basic Meta Tags -->
    <meta name="description" content="{{ $metaDescription }}">
    @if($metaKeywords)
        <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    
    <!-- Open Graph / Facebook -->
    <meta property="og:title" content="{{ $metaTags['og_title'] ?? $pageTitle }}">
    <meta property="og:description" content="{{ $metaTags['og_description'] ?? $metaDescription }}">
    @if(isset($metaTags['og_image']))
        <meta property="og:image" content="{{ $metaTags['og_image'] }}">
    @endif
    <meta property="og:url" content="{{ $metaTags['og_url'] ?? url()->current() }}">
    <meta property="og:type" content="{{ $metaTags['og_type'] ?? 'website' }}">
    <meta property="og:site_name" content="{{ $metaTags['og_site_name'] ?? $siteName }}">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="{{ $metaTags['twitter_card'] ?? 'summary_large_image' }}">
    @if($twitterUsername)
        <meta name="twitter:site" content="@{{ $twitterUsername }}">
    @endif
    @if(isset($metaTags['twitter_creator']))
        <meta name="twitter:creator" content="{{ $metaTags['twitter_creator'] }}">
    @endif
    <meta name="twitter:title" content="{{ $metaTags['twitter_title'] ?? $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaTags['twitter_description'] ?? $metaDescription }}">
    @if(isset($metaTags['twitter_image']))
        <meta name="twitter:image" content="{{ $metaTags['twitter_image'] }}">
    @endif
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ $metaTags['canonical'] ?? url()->current() }}">
    
    <!-- Theme Color -->
    <meta name="theme-color" content="{{ $metaTags['theme_color'] ?? '#3B82F6' }}">
    
    <!-- Google Site Verification -->
    @if($googleSiteVerification)
        <meta name="google-site-verification" content="{{ $googleSiteVerification }}">
    @endif
    
    <!-- Robots -->
    @if(isset($metaTags['robots']))
        <meta name="robots" content="{{ $metaTags['robots'] }}">
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
    
    <!-- Google Analytics -->
    @if($googleAnalyticsId)
        @if(str_starts_with($googleAnalyticsId, 'UA-'))
            <!-- Google Analytics UA -->
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsId }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{{ $googleAnalyticsId }}');
            </script>
        @elseif(str_starts_with($googleAnalyticsId, 'G-'))
            <!-- Google Analytics 4 -->
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsId }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{{ $googleAnalyticsId }}');
            </script>
        @endif
    @endif
    
    <!-- Structured Data -->
    @if(isset($metaTags['structured_data']))
        @foreach($metaTags['structured_data'] as $data)
            <script type="application/ld+json">
                @json($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            </script>
        @endforeach
    @endif
    
    <!-- Additional Head -->
    @stack('head')
</head>
<body class="font-sans antialiased text-gray-900">
    <!-- Navigation -->
    <x-frontend.navigation />
    
    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>
    
    <!-- Footer -->
    <x-frontend.footer />
    
    <!-- Scripts -->
    @stack('scripts')
    
    <!-- Toast notifications -->
    @if(session('success') || session('error') || session('warning') || session('info'))
        <x-frontend.toast 
            :type="session('success') ? 'success' : (session('error') ? 'error' : (session('warning') ? 'warning' : 'info'))"
            :message="session('success') ?? session('error') ?? session('warning') ?? session('info')"
        />
    @endif
    
    <!-- Maintenance Mode Notification -->
    @if(setting('maintenance_mode') && auth()->check() && auth()->user()->is_admin)
        <div class="fixed bottom-4 right-4 bg-yellow-500 text-white px-4 py-2 rounded-lg shadow-lg z-50">
            ⚠️ Maintenance mode is active. Only admins can see this.
        </div>
    @endif
</body>
</html>