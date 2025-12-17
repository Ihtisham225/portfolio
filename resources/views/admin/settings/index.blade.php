<x-admin.layout title="Settings">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
                <p class="text-gray-600">Manage your application settings</p>
            </div>
            <div class="flex items-center space-x-4">
                <button
                    type="submit"
                    form="settingsForm"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center"
                >
                    <x-admin.icon name="save" class="w-5 h-5 mr-2" />
                    Save Changes
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Settings Form -->
    <form 
        id="settingsForm"
        action="{{ route('admin.settings.update') }}" 
        method="POST"
    >
        @csrf
        @method('PUT')

        <div class="space-y-8">
            <!-- General Settings -->
            <x-admin.card title="General Settings">
                <div class="space-y-6">
                    @php
                        // Access the passed variables correctly
                        $generalSettings = $generalSettings ?? [];
                    @endphp
                    
                    <!-- Site Name -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Site Name *
                            </label>
                            <input
                                type="text"
                                id="site_name"
                                name="settings[site_name]"
                                value="{{ old('settings.site_name', $generalSettings['site_name'] ?? config('app.name')) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                        </div>

                        <!-- Site Title -->
                        <div>
                            <label for="site_title" class="block text-sm font-medium text-gray-700 mb-1">
                                Site Title
                            </label>
                            <input
                                type="text"
                                id="site_title"
                                name="settings[site_title]"
                                value="{{ old('settings.site_title', $generalSettings['site_title'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        <!-- Site Tagline -->
                        <div>
                            <label for="site_tagline" class="block text-sm font-medium text-gray-700 mb-1">
                                Site Tagline
                            </label>
                            <input
                                type="text"
                                id="site_tagline"
                                name="settings[site_tagline]"
                                value="{{ old('settings.site_tagline', $generalSettings['site_tagline'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        <!-- Site Email -->
                        <div>
                            <label for="site_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Site Email *
                            </label>
                            <input
                                type="email"
                                id="site_email"
                                name="settings[site_email]"
                                value="{{ old('settings.site_email', $generalSettings['site_email'] ?? config('mail.from.address')) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                        </div>

                        <!-- Timezone -->
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 mb-1">
                                Timezone *
                            </label>
                            <select
                                id="timezone"
                                name="settings[timezone]"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                                @foreach(timezone_identifiers_list() as $timezone)
                                    <option 
                                        value="{{ $timezone }}"
                                        {{ old('settings.timezone', $generalSettings['timezone'] ?? config('app.timezone')) === $timezone ? 'selected' : '' }}
                                    >
                                        {{ $timezone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Format -->
                        <div>
                            <label for="date_format" class="block text-sm font-medium text-gray-700 mb-1">
                                Date Format *
                            </label>
                            <select
                                id="date_format"
                                name="settings[date_format]"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                                <option value="Y-m-d" {{ old('settings.date_format', $generalSettings['date_format'] ?? 'Y-m-d') === 'Y-m-d' ? 'selected' : '' }}>
                                    YYYY-MM-DD (2024-01-15)
                                </option>
                                <option value="d/m/Y" {{ old('settings.date_format', $generalSettings['date_format'] ?? 'Y-m-d') === 'd/m/Y' ? 'selected' : '' }}>
                                    DD/MM/YYYY (15/01/2024)
                                </option>
                                <option value="m/d/Y" {{ old('settings.date_format', $generalSettings['date_format'] ?? 'Y-m-d') === 'm/d/Y' ? 'selected' : '' }}>
                                    MM/DD/YYYY (01/15/2024)
                                </option>
                                <option value="F j, Y" {{ old('settings.date_format', $generalSettings['date_format'] ?? 'Y-m-d') === 'F j, Y' ? 'selected' : '' }}>
                                    Month Day, Year (January 15, 2024)
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Site Description -->
                    <div>
                        <label for="site_description" class="block text-sm font-medium text-gray-700 mb-1">
                            Site Description
                        </label>
                        <textarea
                            id="site_description"
                            name="settings[site_description]"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >{{ old('settings.site_description', $generalSettings['site_description'] ?? '') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">A brief description of your website</p>
                    </div>

                    <!-- Maintenance Mode -->
                    <div class="pt-4 border-t border-gray-200">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                id="maintenance_mode"
                                name="settings[maintenance_mode]"
                                value="1"
                                {{ old('settings.maintenance_mode', $generalSettings['maintenance_mode'] ?? false) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">Enable Maintenance Mode</span>
                        </label>
                        <p class="mt-1 text-sm text-gray-500">When enabled, only administrators can access the site</p>
                    </div>

                    <!-- Allow User Registration -->
                    <div>
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                id="allow_registration"
                                name="settings[allow_registration]"
                                value="1"
                                {{ old('settings.allow_registration', $generalSettings['allow_registration'] ?? false) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">Allow User Registration</span>
                        </label>
                    </div>
                </div>
            </x-admin.card>

            <!-- SEO Settings -->
            <x-admin.card title="SEO Settings">
                <div class="space-y-6">
                    @php
                        $seoSettings = $seoSettings ?? [];
                    @endphp
                    
                    <!-- Default Meta Title -->
                    <div>
                        <label for="seo_default_title" class="block text-sm font-medium text-gray-700 mb-1">
                            Default Meta Title
                        </label>
                        <input
                            type="text"
                            id="seo_default_title"
                            name="seo_settings[default_title]"
                            value="{{ old('seo_settings.default_title', $seoSettings['default_title'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="{{ $generalSettings['site_name'] ?? config('app.name') }}"
                        >
                    </div>

                    <!-- Default Meta Description -->
                    <div>
                        <label for="seo_default_description" class="block text-sm font-medium text-gray-700 mb-1">
                            Default Meta Description
                        </label>
                        <textarea
                            id="seo_default_description"
                            name="seo_settings[default_description]"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="{{ $generalSettings['site_description'] ?? '' }}"
                        >{{ old('seo_settings.default_description', $seoSettings['default_description'] ?? '') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Maximum 160 characters recommended</p>
                    </div>

                    <!-- Default Meta Keywords -->
                    <div>
                        <label for="seo_default_keywords" class="block text-sm font-medium text-gray-700 mb-1">
                            Default Meta Keywords
                        </label>
                        <input
                            type="text"
                            id="seo_default_keywords"
                            name="seo_settings[default_keywords]"
                            value="{{ old('seo_settings.default_keywords', $seoSettings['default_keywords'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="keyword1, keyword2, keyword3"
                        >
                        <p class="mt-1 text-sm text-gray-500">Separate keywords with commas</p>
                    </div>

                    <!-- Robots.txt Content -->
                    <div>
                        <label for="seo_robots_txt" class="block text-sm font-medium text-gray-700 mb-1">
                            Robots.txt Content
                        </label>
                        <textarea
                            id="seo_robots_txt"
                            name="seo_settings[robots_txt]"
                            rows="4"
                            class="w-full px-4 py-2 font-mono text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="User-agent: *&#10;Allow: /&#10;Disallow: /admin/"
                        >{{ old('seo_settings.robots_txt', $seoSettings['robots_txt'] ?? '') }}</textarea>
                    </div>

                    <!-- Google Analytics -->
                    <div class="pt-4 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Google Analytics</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="seo_google_analytics_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Google Analytics ID
                                </label>
                                <input
                                    type="text"
                                    id="seo_google_analytics_id"
                                    name="seo_settings[google_analytics_id]"
                                    value="{{ old('seo_settings.google_analytics_id', $seoSettings['google_analytics_id'] ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="UA-XXXXX-Y or G-XXXXXXX"
                                >
                            </div>
                            <div>
                                <label for="seo_google_site_verification" class="block text-sm font-medium text-gray-700 mb-1">
                                    Google Site Verification
                                </label>
                                <input
                                    type="text"
                                    id="seo_google_site_verification"
                                    name="seo_settings[google_site_verification]"
                                    value="{{ old('seo_settings.google_site_verification', $seoSettings['google_site_verification'] ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="google-site-verification=XXXXXXXXXXXXXXXX"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <!-- Social Media Settings -->
            <x-admin.card title="Social Media">
                <div class="space-y-6">
                    @php
                        $socialSettings = $socialSettings ?? [];
                    @endphp
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Facebook -->
                        <div>
                            <label for="social_facebook" class="block text-sm font-medium text-gray-700 mb-1">
                                Facebook Page URL
                            </label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                    facebook.com/
                                </span>
                                <input
                                    type="text"
                                    id="social_facebook"
                                    name="social_settings[facebook]"
                                    value="{{ old('social_settings.facebook', $socialSettings['facebook'] ?? '') }}"
                                    class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="username"
                                >
                            </div>
                        </div>

                        <!-- Twitter -->
                        <div>
                            <label for="social_twitter" class="block text-sm font-medium text-gray-700 mb-1">
                                Twitter Username
                            </label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                    twitter.com/
                                </span>
                                <input
                                    type="text"
                                    id="social_twitter"
                                    name="social_settings[twitter]"
                                    value="{{ old('social_settings.twitter', $socialSettings['twitter'] ?? '') }}"
                                    class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="username"
                                >
                            </div>
                        </div>

                        <!-- Instagram -->
                        <div>
                            <label for="social_instagram" class="block text-sm font-medium text-gray-700 mb-1">
                                Instagram Username
                            </label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                    instagram.com/
                                </span>
                                <input
                                    type="text"
                                    id="social_instagram"
                                    name="social_settings[instagram]"
                                    value="{{ old('social_settings.instagram', $socialSettings['instagram'] ?? '') }}"
                                    class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="username"
                                >
                            </div>
                        </div>

                        <!-- LinkedIn -->
                        <div>
                            <label for="social_linkedin" class="block text-sm font-medium text-gray-700 mb-1">
                                LinkedIn Profile/Page
                            </label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                    linkedin.com/
                                </span>
                                <input
                                    type="text"
                                    id="social_linkedin"
                                    name="social_settings[linkedin]"
                                    value="{{ old('social_settings.linkedin', $socialSettings['linkedin'] ?? '') }}"
                                    class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="in/username or company/company-name"
                                >
                            </div>
                        </div>

                        <!-- GitHub -->
                        <div>
                            <label for="social_github" class="block text-sm font-medium text-gray-700 mb-1">
                                GitHub Username
                            </label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                    github.com/
                                </span>
                                <input
                                    type="text"
                                    id="social_github"
                                    name="social_settings[github]"
                                    value="{{ old('social_settings.github', $socialSettings['github'] ?? '') }}"
                                    class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="username"
                                >
                            </div>
                        </div>

                        <!-- YouTube -->
                        <div>
                            <label for="social_youtube" class="block text-sm font-medium text-gray-700 mb-1">
                                YouTube Channel
                            </label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                    youtube.com/
                                </span>
                                <input
                                    type="text"
                                    id="social_youtube"
                                    name="social_settings[youtube]"
                                    value="{{ old('social_settings.youtube', $socialSettings['youtube'] ?? '') }}"
                                    class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="c/ChannelName or @username"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <!-- Contact Information -->
            <x-admin.card title="Contact Information">
                <div class="space-y-6">
                    @php
                        $contactSettings = $contactSettings ?? [];
                    @endphp
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Contact Email -->
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Contact Email
                            </label>
                            <input
                                type="email"
                                id="contact_email"
                                name="contact_settings[email]"
                                value="{{ old('contact_settings.email', $contactSettings['email'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        <!-- Contact Phone -->
                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Contact Phone
                            </label>
                            <input
                                type="tel"
                                id="contact_phone"
                                name="contact_settings[phone]"
                                value="{{ old('contact_settings.phone', $contactSettings['phone'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="+1 (555) 123-4567"
                            >
                        </div>

                        <!-- Contact Address -->
                        <div class="md:col-span-2">
                            <label for="contact_address" class="block text-sm font-medium text-gray-700 mb-1">
                                Address
                            </label>
                            <textarea
                                id="contact_address"
                                name="contact_settings[address]"
                                rows="2"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >{{ old('contact_settings.address', $contactSettings['address'] ?? '') }}</textarea>
                        </div>

                        <!-- Contact City -->
                        <div>
                            <label for="contact_city" class="block text-sm font-medium text-gray-700 mb-1">
                                City
                            </label>
                            <input
                                type="text"
                                id="contact_city"
                                name="contact_settings[city]"
                                value="{{ old('contact_settings.city', $contactSettings['city'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        <!-- Contact State/Region -->
                        <div>
                            <label for="contact_state" class="block text-sm font-medium text-gray-700 mb-1">
                                State/Region
                            </label>
                            <input
                                type="text"
                                id="contact_state"
                                name="contact_settings[state]"
                                value="{{ old('contact_settings.state', $contactSettings['state'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        <!-- Contact Zip/Postal Code -->
                        <div>
                            <label for="contact_zip" class="block text-sm font-medium text-gray-700 mb-1">
                                Zip/Postal Code
                            </label>
                            <input
                                type="text"
                                id="contact_zip"
                                name="contact_settings[zip]"
                                value="{{ old('contact_settings.zip', $contactSettings['zip'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        <!-- Contact Country -->
                        <div>
                            <label for="contact_country" class="block text-sm font-medium text-gray-700 mb-1">
                                Country
                            </label>
                            <input
                                type="text"
                                id="contact_country"
                                name="contact_settings[country]"
                                value="{{ old('contact_settings.country', $contactSettings['country'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        <!-- Google Maps Embed URL -->
                        <div class="md:col-span-2">
                            <label for="contact_google_maps" class="block text-sm font-medium text-gray-700 mb-1">
                                Google Maps Embed URL
                            </label>
                            <input
                                type="url"
                                id="contact_google_maps"
                                name="contact_settings[google_maps]"
                                value="{{ old('contact_settings.google_maps', $contactSettings['google_maps'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="https://www.google.com/maps/embed?pb=..."
                            >
                            <p class="mt-1 text-sm text-gray-500">
                                Get embed URL from Google Maps → Share → Embed a map
                            </p>
                        </div>

                        <!-- Business Hours -->
                        <div class="md:col-span-2">
                            <label for="contact_business_hours" class="block text-sm font-medium text-gray-700 mb-1">
                                Business Hours
                            </label>
                            <textarea
                                id="contact_business_hours"
                                name="contact_settings[business_hours]"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Monday-Friday: 9:00 AM - 5:00 PM&#10;Saturday: 10:00 AM - 2:00 PM&#10;Sunday: Closed"
                            >{{ old('contact_settings.business_hours', $contactSettings['business_hours'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <!-- Danger Zone -->
            <x-admin.card title="Danger Zone" class="border-red-200 bg-red-50">
                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-red-300">
                        <div>
                            <h3 class="font-medium text-red-900">Clear Cache</h3>
                            <p class="text-sm text-red-700 mt-1">
                                Clear all cached data including settings, portfolio stats, and recent activity.
                            </p>
                        </div>
                        <button
                            type="button"
                            onclick="clearCache()"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                        >
                            Clear Cache
                        </button>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-red-300">
                        <div>
                            <h3 class="font-medium text-red-900">Reset to Default Settings</h3>
                            <p class="text-sm text-red-700 mt-1">
                                Reset all settings to their default values. This action cannot be undone.
                            </p>
                        </div>
                        <button
                            type="button"
                            onclick="confirmReset()"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                        >
                            Reset Settings
                        </button>
                    </div>
                </div>
            </x-admin.card>

            <!-- Save Button -->
            <div class="sticky bottom-6">
                <div class="bg-white p-4 rounded-lg shadow-lg border border-gray-200">
                    <div class="flex items-center justify-end space-x-4">
                        <button
                            type="button"
                            onclick="window.location.reload()"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            Discard Changes
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium flex items-center"
                        >
                            <x-admin.icon name="save" class="w-5 h-5 mr-2" />
                            Save All Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        // Auto-save counter
        let autoSaveTimer;
        let lastSaveTime = null;
        
        // Start auto-save timer
        function startAutoSaveTimer() {
            if (autoSaveTimer) clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                document.getElementById('settingsForm').submit();
            }, 30000); // 30 seconds
        }
        
        // Initialize autosave
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('settingsForm');
            const inputs = form.querySelectorAll('input, textarea, select');
            
            inputs.forEach(input => {
                input.addEventListener('change', startAutoSaveTimer);
                input.addEventListener('input', startAutoSaveTimer);
            });
            
            // Update last save time if there was a previous save
            @if(session('success'))
                lastSaveTime = new Date();
                updateSaveStatus();
            @endif
        });
        
        // Update save status indicator
        function updateSaveStatus() {
            const statusEl = document.getElementById('saveStatus');
            if (!statusEl) return;
            
            if (lastSaveTime) {
                const now = new Date();
                const diff = Math.floor((now - lastSaveTime) / 1000);
                
                if (diff < 60) {
                    statusEl.textContent = `Saved ${diff} seconds ago`;
                    statusEl.className = 'text-green-600 text-sm';
                } else if (diff < 3600) {
                    statusEl.textContent = `Saved ${Math.floor(diff / 60)} minutes ago`;
                    statusEl.className = 'text-green-600 text-sm';
                } else {
                    statusEl.textContent = `Saved ${Math.floor(diff / 3600)} hours ago`;
                    statusEl.className = 'text-yellow-600 text-sm';
                }
            } else {
                statusEl.textContent = 'Unsaved changes';
                statusEl.className = 'text-red-600 text-sm';
            }
        }
        
        // Clear cache function
        function clearCache() {
            if (confirm('Are you sure you want to clear all cache? This may temporarily slow down the site.')) {
                fetch('{{ route("admin.settings.clear-cache") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cache cleared successfully!');
                    } else {
                        alert('Error clearing cache.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error clearing cache.');
                });
            }
        }
        
        // Confirm reset function
        function confirmReset() {
            if (confirm('WARNING: This will reset ALL settings to their default values. This action cannot be undone. Are you sure?')) {
                fetch('{{ route("admin.settings.reset") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Settings reset successfully! The page will reload.');
                        window.location.reload();
                    } else {
                        alert('Error resetting settings.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error resetting settings.');
                });
            }
        }
        
        // Character counters
        function updateCharacterCount(textareaId, maxLength = 160) {
            const textarea = document.getElementById(textareaId);
            const counter = document.getElementById(textareaId + '_counter');
            if (!textarea || !counter) return;
            
            const length = textarea.value.length;
            counter.textContent = `${length}/${maxLength}`;
            
            if (length > maxLength) {
                counter.className = 'text-red-600 text-sm';
            } else if (length > maxLength * 0.9) {
                counter.className = 'text-yellow-600 text-sm';
            } else {
                counter.className = 'text-gray-500 text-sm';
            }
        }
        
        // Initialize character counters
        document.addEventListener('DOMContentLoaded', function() {
            // SEO description counter
            const seoDesc = document.getElementById('seo_default_description');
            if (seoDesc) {
                const counter = document.createElement('div');
                counter.id = 'seo_default_description_counter';
                counter.className = 'text-gray-500 text-sm mt-1 text-right';
                seoDesc.parentNode.insertBefore(counter, seoDesc.nextSibling);
                
                seoDesc.addEventListener('input', () => updateCharacterCount('seo_default_description', 160));
                updateCharacterCount('seo_default_description', 160);
            }
            
            // Site description counter
            const siteDesc = document.getElementById('site_description');
            if (siteDesc) {
                const counter = document.createElement('div');
                counter.id = 'site_description_counter';
                counter.className = 'text-gray-500 text-sm mt-1 text-right';
                siteDesc.parentNode.insertBefore(counter, siteDesc.nextSibling);
                
                siteDesc.addEventListener('input', () => updateCharacterCount('site_description', 500));
                updateCharacterCount('site_description', 500);
            }
        });
    </script>
    @endpush
</x-admin.layout>