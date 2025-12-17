@php
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
    
    // Open Graph Image
    $ogImage = $metaTags['og_image'] ?? asset('images/og-default.jpg');
    $twitterImage = $metaTags['twitter_image'] ?? $ogImage;
@endphp

<title>{{ $fullTitle }}</title>

<!-- Basic Meta Tags -->
<meta name="description" content="{{ $metaDescription }}">
@if($metaKeywords)
    <meta name="keywords" content="{{ $metaKeywords }}">
@endif
<meta name="author" content="{{ $siteName }}">

<!-- Open Graph / Facebook -->
<meta property="og:title" content="{{ $metaTags['og_title'] ?? $pageTitle }}">
<meta property="og:description" content="{{ $metaTags['og_description'] ?? $metaDescription }}">
<meta property="og:image" content="{{ $ogImage }}">
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
<meta name="twitter:image" content="{{ $twitterImage }}">

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

<!-- Additional Meta Tags -->
@if(isset($metaTags['additional']))
    @foreach($metaTags['additional'] as $key => $value)
        <meta name="{{ $key }}" content="{{ $value }}">
    @endforeach
@endif