@php
    use App\Models\Setting;
    use App\Models\Category;
    
    // Fetch settings from database
    $siteName = Setting::getValue('site_name', config('app.name'));
    $siteDescription = Setting::getValue('site_tagline', 'Professional portfolio showcasing innovative projects and technical expertise in web development.');
    
    // Social media links
    $socialLinks = [
        'github' => Setting::getValue('github', '#'),
        'linkedin' => Setting::getValue('linkedin', '#'),
        'twitter' => Setting::getValue('twitter', '#'),
        'facebook' => Setting::getValue('facebook', '#'),
        'instagram' => Setting::getValue('instagram', '#'),
        'youtube' => Setting::getValue('youtube', '#'),
    ];
    
    // Contact info
    $contactEmail = Setting::getValue('email', 'hello@example.com');
    $contactPhone = Setting::getValue('phone', '+1 (555) 123-4567');
    $contactAddress = Setting::getValue('address', 'Your City, Country');
    
    // Newsletter settings
    $newsletterTitle = Setting::getValue('newsletter_title', 'Stay Updated');
    $newsletterDescription = Setting::getValue('newsletter_description', 'Get exclusive insights, project updates, and development tips delivered to your inbox.');
@endphp

<footer class="bg-white border-t border-gray-200">
    <div class="container mx-auto px-6 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- About -->
            <div>
                <h3 class="text-lg font-bold mb-4 text-gray-900">{{ $siteName }}</h3>
                <p class="text-gray-600 mb-4">
                    {{ $siteDescription }}
                </p>
                
                <!-- Contact Info -->
                <div class="space-y-2 mb-6">
                    @if($contactEmail)
                        <div class="flex items-center text-gray-600">
                            <x-frontend.icon name="mail" class="w-4 h-4 mr-2 text-gray-400" />
                            <a href="mailto:{{ $contactEmail }}" class="hover:text-blue-600 transition-colors">
                                {{ $contactEmail }}
                            </a>
                        </div>
                    @endif
                    
                    @if($contactPhone)
                        <div class="flex items-center text-gray-600">
                            <x-frontend.icon name="phone" class="w-4 h-4 mr-2 text-gray-400" />
                            <a href="tel:{{ $contactPhone }}" class="hover:text-blue-600 transition-colors">
                                {{ $contactPhone }}
                            </a>
                        </div>
                    @endif
                </div>
                
                <!-- Social Links -->
                <div class="flex space-x-4">
                    @if($socialLinks['github'] !== '#')
                        <a href="{{ $socialLinks['github'] }}" target="_blank" 
                           class="text-gray-400 hover:text-gray-900 transition-colors transform hover:-translate-y-1"
                           title="GitHub">
                            <x-frontend.icon name="github" class="w-5 h-5" />
                        </a>
                    @endif
                    
                    @if($socialLinks['linkedin'] !== '#')
                        <a href="{{ $socialLinks['linkedin'] }}" target="_blank" 
                           class="text-gray-400 hover:text-blue-700 transition-colors transform hover:-translate-y-1"
                           title="LinkedIn">
                            <x-frontend.icon name="linkedin" class="w-5 h-5" />
                        </a>
                    @endif
                    
                    @if($socialLinks['twitter'] !== '#')
                        <a href="{{ $socialLinks['twitter'] }}" target="_blank" 
                           class="text-gray-400 hover:text-blue-400 transition-colors transform hover:-translate-y-1"
                           title="Twitter">
                            <x-frontend.icon name="twitter" class="w-5 h-5" />
                        </a>
                    @endif
                    
                    @if($socialLinks['facebook'] !== '#')
                        <a href="{{ $socialLinks['facebook'] }}" target="_blank" 
                           class="text-gray-400 hover:text-blue-600 transition-colors transform hover:-translate-y-1"
                           title="Facebook">
                            <x-frontend.icon name="facebook" class="w-5 h-5" />
                        </a>
                    @endif
                    
                    @if($socialLinks['instagram'] !== '#')
                        <a href="{{ $socialLinks['instagram'] }}" target="_blank" 
                           class="text-gray-400 hover:text-pink-600 transition-colors transform hover:-translate-y-1"
                           title="Instagram">
                            <x-frontend.icon name="instagram" class="w-5 h-5" />
                        </a>
                    @endif
                </div>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-bold mb-4 text-gray-900">Quick Links</h3>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('home') }}" 
                           class="text-gray-600 hover:text-blue-600 transition-colors flex items-center group">
                            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-100 transition-colors">
                                <x-frontend.icon name="home" class="w-4 h-4 text-blue-600" />
                            </div>
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('portfolio') }}" 
                           class="text-gray-600 hover:text-blue-600 transition-colors flex items-center group">
                            <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center mr-3 group-hover:bg-purple-100 transition-colors">
                                <x-frontend.icon name="briefcase" class="w-4 h-4 text-purple-600" />
                            </div>
                            Portfolio
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('blog') }}" 
                           class="text-gray-600 hover:text-blue-600 transition-colors flex items-center group">
                            <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-100 transition-colors">
                                <x-frontend.icon name="newspaper" class="w-4 h-4 text-green-600" />
                            </div>
                            Blog
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('resume') }}" 
                           class="text-gray-600 hover:text-blue-600 transition-colors flex items-center group">
                            <div class="w-8 h-8 bg-yellow-50 rounded-lg flex items-center justify-center mr-3 group-hover:bg-yellow-100 transition-colors">
                                <x-frontend.icon name="document-text" class="w-4 h-4 text-yellow-600" />
                            </div>
                            Resume
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Categories -->
            <div>
                <h3 class="text-lg font-bold mb-4 text-gray-900">Project Categories</h3>
                <ul class="space-y-3">
                    @php
                        $categories = Category::projectType()
                            ->withCount(['projects' => function($query) {
                                $query->published();
                            }])
                            ->having('projects_count', '>', 0)
                            ->orderBy('projects_count', 'desc')
                            ->limit(6)
                            ->get();
                    @endphp
                    
                    @forelse($categories as $category)
                        <li>
                            <a 
                                href="{{ route('portfolio', ['category' => $category->slug]) }}" 
                                class="text-gray-600 hover:text-blue-600 transition-colors flex items-center justify-between group"
                            >
                                <span class="flex items-center">
                                    <x-frontend.icon name="folder" class="w-4 h-4 mr-2 text-gray-400 group-hover:text-blue-500 transition-colors" />
                                    {{ $category->name }}
                                </span>
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">
                                    {{ $category->projects_count }}
                                </span>
                            </a>
                        </li>
                    @empty
                        <li class="text-gray-500 text-sm">
                            No categories available
                        </li>
                    @endforelse
                    
                    @if($categories->count() > 0)
                        <li>
                            <a href="{{ route('portfolio') }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center group mt-4">
                                View all projects
                                <x-frontend.icon name="arrow-right" class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" />
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            
            <!-- Newsletter -->
            <div>
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                            <x-frontend.icon name="mail" class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $newsletterTitle }}</h3>
                            <p class="text-sm text-gray-600">No spam, unsubscribe anytime</p>
                        </div>
                    </div>
                    
                    <p class="text-gray-600 mb-6 text-sm">
                        {{ $newsletterDescription }}
                    </p>
                    
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-3" id="newsletterForm">
                        @csrf
                        <div class="space-y-2">
                            <input
                                type="email"
                                name="email"
                                id="newsletterEmail"
                                placeholder="you@example.com"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors placeholder-gray-400"
                                required
                                autocomplete="email"
                            >
                            <div class="text-xs text-gray-500">
                                We'll send you a verification email to confirm your subscription
                            </div>
                        </div>
                        
                        <button
                            type="submit"
                            id="newsletterSubmit"
                            class="w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-md flex items-center justify-center gap-2"
                        >
                            <span id="submitText">Subscribe Now</span>
                            <span id="loadingSpinner" class="hidden">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                        
                        <div id="newsletterMessage" class="hidden mt-4 p-4 rounded-lg text-sm"></div>
                    </form>
                </div>
                
                <!-- Stats -->
                @php
                    $subscriberCount = \App\Models\NewsletterSubscription::verified()->active()->count();
                @endphp
                <div class="mt-4 text-center">
                    <div class="inline-flex items-center gap-2 text-sm text-gray-500">
                        <x-frontend.icon name="users" class="w-4 h-4" />
                        <span>{{ $subscriberCount }}+ developers already subscribed</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom Bar -->
        <div class="border-t border-gray-200 mt-8 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <!-- Copyright -->
                <div class="text-gray-600 mb-4 md:mb-0">
                    <p class="flex items-center gap-2">
                        <x-frontend.icon name="shield-check" class="w-4 h-4 text-green-500" />
                        <span>
                            &copy; {{ date('Y') }} {{ $siteName }}. 
                            @php
                                $copyrightText = Setting::getValue('copyright_text', 'All rights reserved.');
                            @endphp
                            {{ $copyrightText }}
                        </span>
                    </p>
                </div>
                
                <!-- Additional Links -->
                <div class="flex flex-wrap gap-6 text-sm">
                    @php
                        $privacyPolicy = Setting::getValue('privacy_policy_url');
                        $termsOfService = Setting::getValue('terms_of_service_url');
                        $cookiePolicy = Setting::getValue('cookie_policy_url');
                    @endphp
                    
                    @if($privacyPolicy)
                        <a href="{{ $privacyPolicy }}" 
                           class="text-gray-600 hover:text-blue-600 transition-colors flex items-center gap-1">
                            <x-frontend.icon name="shield-exclamation" class="w-4 h-4" />
                            Privacy Policy
                        </a>
                    @endif
                    
                    @if($termsOfService)
                        <a href="{{ $termsOfService }}" 
                           class="text-gray-600 hover:text-blue-600 transition-colors flex items-center gap-1">
                            <x-frontend.icon name="document-text" class="w-4 h-4" />
                            Terms
                        </a>
                    @endif
                    
                    @if($cookiePolicy)
                        <a href="{{ $cookiePolicy }}" 
                           class="text-gray-600 hover:text-blue-600 transition-colors flex items-center gap-1">
                            <x-frontend.icon name="cookie" class="w-4 h-4" />
                            Cookies
                        </a>
                    @endif
                    
                    <a href="{{ route('sitemap') }}" 
                       class="text-gray-600 hover:text-blue-600 transition-colors flex items-center gap-1">
                        <x-frontend.icon name="sitemap" class="w-4 h-4" />
                        Sitemap
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const newsletterForm = document.getElementById('newsletterForm');
        const newsletterEmail = document.getElementById('newsletterEmail');
        const newsletterSubmit = document.getElementById('newsletterSubmit');
        const submitText = document.getElementById('submitText');
        const loadingSpinner = document.getElementById('newsletterSubmit').querySelector('#loadingSpinner');
        const newsletterMessage = document.getElementById('newsletterMessage');
        
        // Add cookie icon to icon component first
        const cookieIcon = {
            'cookie': 'M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm-1-6a1 1 0 100-2 1 1 0 000 2zm5-1a1 1 0 11-2 0 1 1 0 012 0zm-7 4a1 1 0 100-2 1 1 0 000 2zm8-4a1 1 0 100-2 1 1 0 000 2zm-4 4a1 1 0 100-2 1 1 0 000 2z',
            'users': 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13 0a4 4 0 11-8 0 4 4 0 018 0z',
            'sitemap': 'M3 3h18v18H3V3zm2 2v14h14V5H5zm2 2h10v2H7V7zm0 4h10v2H7v-2zm0 4h10v2H7v-2z',
            'shield-check': 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            'shield-exclamation': 'M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01'
        };
        
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const email = newsletterEmail.value.trim();
                
                // Validate email
                if (!email || !isValidEmail(email)) {
                    showMessage('Please enter a valid email address.', 'error');
                    return;
                }
                
                // Show loading state
                newsletterSubmit.disabled = true;
                submitText.textContent = 'Subscribing...';
                loadingSpinner.classList.remove('hidden');
                newsletterSubmit.classList.add('opacity-75');
                
                // Remove any existing message
                newsletterMessage.classList.add('hidden');
                
                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showMessage(data.message, 'success');
                        newsletterForm.reset();
                    } else {
                        showMessage(data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showMessage('An error occurred. Please try again.', 'error');
                } finally {
                    // Reset button state
                    newsletterSubmit.disabled = false;
                    submitText.textContent = 'Subscribe Now';
                    loadingSpinner.classList.add('hidden');
                    newsletterSubmit.classList.remove('opacity-75');
                }
            });
            
            // Email validation function
            function isValidEmail(email) {
                const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(String(email).toLowerCase());
            }
            
            // Show message function
            function showMessage(message, type) {
                newsletterMessage.textContent = message;
                newsletterMessage.className = `mt-4 p-4 rounded-lg text-sm ${
                    type === 'success' 
                        ? 'bg-green-50 text-green-800 border border-green-200' 
                        : 'bg-red-50 text-red-800 border border-red-200'
                }`;
                newsletterMessage.classList.remove('hidden');
                
                // Auto-hide success messages after 5 seconds
                if (type === 'success') {
                    setTimeout(() => {
                        newsletterMessage.classList.add('hidden');
                    }, 5000);
                }
            }
            
            // Add email validation on blur
            newsletterEmail.addEventListener('blur', function() {
                const email = this.value.trim();
                if (email && !isValidEmail(email)) {
                    this.classList.add('border-red-300', 'bg-red-50');
                    this.classList.remove('border-gray-300');
                } else {
                    this.classList.remove('border-red-300', 'bg-red-50');
                    this.classList.add('border-gray-300');
                }
            });
            
            // Clear validation on focus
            newsletterEmail.addEventListener('focus', function() {
                this.classList.remove('border-red-300', 'bg-red-50');
                this.classList.add('border-blue-500');
            });
        }
    });
</script>
@endpush