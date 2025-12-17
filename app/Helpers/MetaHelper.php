<?php

use App\Models\Setting;

if (!function_exists('generate_meta_tags')) {
    /**
     * Generate meta tags for a page
     *
     * @param array $overrides
     * @return array
     */
    function generate_meta_tags(array $overrides = [])
    {
        $siteName = setting('site_name', config('app.name'));
        $defaultTitle = setting('default_title', '');
        $defaultDescription = setting('default_description', '');
        $defaultKeywords = setting('default_keywords', '');
        
        $defaults = [
            'title' => $defaultTitle ?: $siteName,
            'description' => $defaultDescription,
            'keywords' => $defaultKeywords,
            'og_title' => null,
            'og_description' => null,
            'og_image' => null,
            'og_url' => url()->current(),
            'og_type' => 'website',
            'og_site_name' => $siteName,
            'twitter_card' => 'summary_large_image',
            'twitter_site' => setting('twitter') ? '@' . setting('twitter') : null,
            'twitter_creator' => null,
            'twitter_title' => null,
            'twitter_description' => null,
            'twitter_image' => null,
            'canonical' => url()->current(),
            'robots' => 'index, follow',
            'theme_color' => '#3B82F6',
        ];
        
        // Merge defaults with overrides
        $meta = array_merge($defaults, $overrides);
        
        // Auto-fill Twitter from OG if not set
        if (empty($meta['twitter_title']) && !empty($meta['og_title'])) {
            $meta['twitter_title'] = $meta['og_title'];
        }
        
        if (empty($meta['twitter_description']) && !empty($meta['og_description'])) {
            $meta['twitter_description'] = $meta['og_description'];
        }
        
        if (empty($meta['twitter_image']) && !empty($meta['og_image'])) {
            $meta['twitter_image'] = $meta['og_image'];
        }
        
        // Auto-fill OG from basic if not set
        if (empty($meta['og_title']) && !empty($meta['title'])) {
            $meta['og_title'] = $meta['title'];
        }
        
        if (empty($meta['og_description']) && !empty($meta['description'])) {
            $meta['og_description'] = $meta['description'];
        }
        
        return $meta;
    }
}

if (!function_exists('generate_structured_data')) {
    /**
     * Generate structured data for a page
     *
     * @param string $type
     * @param array $data
     * @return array
     */
    function generate_structured_data($type = 'Website', array $data = [])
    {
        $siteName = setting('site_name', config('app.name'));
        $siteUrl = config('app.url');
        $siteDescription = setting('site_description', '');
        
        $defaults = [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'name' => $siteName,
            'url' => $siteUrl,
            'description' => $siteDescription,
        ];
        
        // Add contact information
        $contactEmail = setting('contact_email');
        $contactPhone = setting('contact_phone');
        $contactAddress = setting('address');
        
        if ($contactEmail || $contactPhone || $contactAddress) {
            $defaults['contactPoint'] = [
                '@type' => 'ContactPoint',
            ];
            
            if ($contactEmail) {
                $defaults['contactPoint']['email'] = $contactEmail;
            }
            
            if ($contactPhone) {
                $defaults['contactPoint']['telephone'] = $contactPhone;
            }
        }
        
        return array_merge($defaults, $data);
    }
}