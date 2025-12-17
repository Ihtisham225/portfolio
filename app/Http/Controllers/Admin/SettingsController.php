<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        // Get all settings grouped
        $settings = [
            'general' => Setting::getGroup('general'),
            'seo' => Setting::getGroup('seo'),
            'social' => Setting::getGroup('social'),
            'contact' => Setting::getGroup('contact'),
        ];
        
        // Pass as a single array to match blade template
        return view('admin.settings.index', [
            'settings' => $settings,
            'generalSettings' => $settings['general'] ?? [],
            'seoSettings' => $settings['seo'] ?? [],
            'socialSettings' => $settings['social'] ?? [],
            'contactSettings' => $settings['contact'] ?? [],
        ]);
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'settings.site_name' => 'required|string|max:255',
            'settings.site_title' => 'nullable|string|max:255',
            'settings.site_tagline' => 'nullable|string|max:500',
            'settings.site_email' => 'required|email|max:255',
            'settings.timezone' => 'required|string|timezone',
            'settings.date_format' => 'required|string|in:Y-m-d,d/m/Y,m/d/Y,F j, Y',
            'settings.site_description' => 'nullable|string|max:500',
            'settings.maintenance_mode' => 'nullable|boolean',
            'settings.allow_registration' => 'nullable|boolean',
            
            'seo_settings.default_title' => 'nullable|string|max:255',
            'seo_settings.default_description' => 'nullable|string|max:500',
            'seo_settings.default_keywords' => 'nullable|string|max:500',
            'seo_settings.robots_txt' => 'nullable|string',
            'seo_settings.google_analytics_id' => 'nullable|string|max:50',
            'seo_settings.google_site_verification' => 'nullable|string|max:100',
            
            'social_settings.facebook' => 'nullable|string|max:100',
            'social_settings.twitter' => 'nullable|string|max:100',
            'social_settings.instagram' => 'nullable|string|max:100',
            'social_settings.linkedin' => 'nullable|string|max:100',
            'social_settings.github' => 'nullable|string|max:100',
            'social_settings.youtube' => 'nullable|string|max:100',
            
            'contact_settings.email' => 'nullable|email|max:255',
            'contact_settings.phone' => 'nullable|string|max:50',
            'contact_settings.address' => 'nullable|string|max:500',
            'contact_settings.city' => 'nullable|string|max:100',
            'contact_settings.state' => 'nullable|string|max:100',
            'contact_settings.zip' => 'nullable|string|max:20',
            'contact_settings.country' => 'nullable|string|max:100',
            'contact_settings.google_maps' => 'nullable|url|max:500',
            'contact_settings.business_hours' => 'nullable|string|max:500',
        ]);

        // Process general settings
        if (isset($validated['settings'])) {
            foreach ($validated['settings'] as $key => $value) {
                $type = $this->determineType($key, $value);
                Setting::setValue($key, $value, $type, 'general');
            }
        }

        // Process SEO settings
        if (isset($validated['seo_settings'])) {
            foreach ($validated['seo_settings'] as $key => $value) {
                $type = $this->determineType($key, $value);
                Setting::setValue($key, $value, $type, 'seo');
            }
        }

        // Process social settings
        if (isset($validated['social_settings'])) {
            foreach ($validated['social_settings'] as $key => $value) {
                $type = $this->determineType($key, $value);
                Setting::setValue($key, $value, $type, 'social');
            }
        }

        // Process contact settings
        if (isset($validated['contact_settings'])) {
            foreach ($validated['contact_settings'] as $key => $value) {
                $type = $this->determineType($key, $value);
                Setting::setValue($key, $value, $type, 'contact');
            }
        }

        // Clear cache
        Setting::clearCache();

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Clear application cache
     */
    public function clearCache()
    {
        try {
            // Clear all caches
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Setting::clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cache: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset settings to default values
     */
    public function reset()
    {
        try {
            // Get default settings
            $defaultSettings = $this->getDefaultSettings();
            
            // Delete all existing settings
            Setting::query()->delete();
            
            // Insert default settings
            foreach ($defaultSettings as $group => $settings) {
                foreach ($settings as $key => $value) {
                    $type = $this->determineType($key, $value);
                    Setting::create([
                        'key' => $key,
                        'value' => $value,
                        'type' => $type,
                        'group' => $group,
                    ]);
                }
            }
            
            // Clear cache
            Setting::clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Settings reset to default values.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error resetting settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Determine the type of setting value
     */
    private function determineType($key, $value)
    {
        // Check for boolean fields
        $booleanFields = ['maintenance_mode', 'allow_registration'];
        if (in_array($key, $booleanFields)) {
            return 'boolean';
        }
        
        // Check for array fields
        $arrayFields = ['default_keywords'];
        if (in_array($key, $arrayFields)) {
            return 'array';
        }
        
        // Check for JSON fields
        $jsonFields = ['meta']; // Add more if needed
        if (in_array($key, $jsonFields)) {
            return 'json';
        }
        
        // Check for number fields
        $numberFields = []; // Add numeric fields here if any
        if (in_array($key, $numberFields)) {
            return 'number';
        }
        
        // Default to text
        return 'text';
    }

    /**
     * Get default settings
     */
    private function getDefaultSettings()
    {
        return [
            'general' => [
                'site_name' => config('app.name'),
                'site_title' => '',
                'site_tagline' => '',
                'site_email' => config('mail.from.address'),
                'timezone' => config('app.timezone'),
                'date_format' => 'Y-m-d',
                'site_description' => '',
                'maintenance_mode' => false,
                'allow_registration' => true,
            ],
            'seo' => [
                'default_title' => '',
                'default_description' => '',
                'default_keywords' => '',
                'robots_txt' => "User-agent: *\nAllow: /\nDisallow: /admin/",
                'google_analytics_id' => '',
                'google_site_verification' => '',
            ],
            'social' => [
                'facebook' => '',
                'twitter' => '',
                'instagram' => '',
                'linkedin' => '',
                'github' => '',
                'youtube' => '',
            ],
            'contact' => [
                'email' => '',
                'phone' => '',
                'address' => '',
                'city' => '',
                'state' => '',
                'zip' => '',
                'country' => '',
                'google_maps' => '',
                'business_hours' => '',
            ],
        ];
    }

    /**
     * Seed initial settings (run once)
     */
    public function seed()
    {
        if (Setting::count() > 0) {
            return redirect()->back()->with('info', 'Settings already exist.');
        }

        $defaultSettings = $this->getDefaultSettings();
        
        foreach ($defaultSettings as $group => $settings) {
            foreach ($settings as $key => $value) {
                $type = $this->determineType($key, $value);
                Setting::create([
                    'key' => $key,
                    'value' => $value,
                    'type' => $type,
                    'group' => $group,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Default settings seeded successfully.');
    }
}