<x-frontend.layout :metaTags="$metaTags ?? []">
    @php
        // Get settings
        $heroTitle = \App\Models\Setting::getValue('site_title', 'Building Digital Experiences');
        $heroSubtitle = \App\Models\Setting::getValue('site_tagline', 'Crafting innovative solutions with cutting-edge technology');
        $services = ['Web Development', 'VPS Deployment', 'AWS Deployment', 'API Development'];
        $servicesDescription = \App\Models\Setting::getValue('services_description', 'Comprehensive digital solutions tailored to your needs');
        $contactEmail = \App\Models\Setting::getValue('email', 'hello@example.com');
        $contactPhone = \App\Models\Setting::getValue('phone', '+1 (555) 123-4567');
        $location = \App\Models\Setting::getValue('address', 'San Francisco, CA');
        $availableForHire = \App\Models\Setting::getValue('available_for_hire', true);
        $socialLinks = \App\Models\Setting::getGroup('social');
        
        // Experience stats
        $totalProjects = \App\Models\Project::published()->count();
        $totalExperience = \App\Models\Experience::count() > 0 ? 
            now()->diffInYears(\App\Models\Experience::oldest('start_date')->first()->start_date) . '+' : '5+';
        $happyClients = \App\Models\Project::published()
            ->whereNotNull('client')
            ->distinct('client')
            ->count('client');
    @endphp

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-gray-50 via-white to-blue-50">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
            <div class="absolute top-1/3 left-1/4 w-80 h-80 bg-pink-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-4000"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left Column - Content -->
                <div class="space-y-8">
                    <!-- Animated Badge -->
                    <div class="inline-flex items-center space-x-2 bg-gradient-to-r from-blue-100 to-purple-100 text-blue-700 px-4 py-2 rounded-full text-sm font-semibold mb-6 transform hover:scale-105 transition-transform"
                         x-data="{ show: false }"
                         x-init="setTimeout(() => show = true, 300)" x-show="show" x-transition>
                        <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                        <span>Available for Freelance Projects</span>
                    </div>

                    <!-- Name with Typewriter Effect -->
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight">
                        <span class="block" x-data="{ text: '{{ $user->name ?? 'Your Name' }}', display: '', typing: true }"
                              x-init="setTimeout(() => {
                                  let i = 0;
                                  const timer = setInterval(() => {
                                      if(i < text.length) {
                                          display += text.charAt(i);
                                          i++;
                                      } else {
                                          clearInterval(timer);
                                          typing = false;
                                      }
                                  }, 100);
                              }, 500)">
                            <span x-text="display" class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600"></span>
                            <span x-show="typing" class="inline-block w-1 h-12 bg-blue-500 ml-1 animate-pulse"></span>
                        </span>
                        <span class="block text-3xl md:text-4xl mt-4 text-gray-700" x-data="{ show: false }"
                              x-init="setTimeout(() => show = true, 2000)" x-show="show" x-transition>
                            {{ $user->title ?? 'Senior Full Stack Developer' }}
                        </span>
                    </h1>

                    <!-- Bio -->
                    <p class="text-xl text-gray-600 leading-relaxed max-w-2xl" x-data="{ show: false }"
                       x-init="setTimeout(() => show = true, 2500)" x-show="show" x-transition>
                        {{ $heroSubtitle }}
                    </p>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-6 pt-8 border-t border-gray-200" x-data="{ show: false }"
                         x-init="setTimeout(() => show = true, 3000)" x-show="show" x-transition>
                        <div class="text-center group">
                            <div class="text-4xl font-bold text-blue-600 mb-2 transform group-hover:scale-110 transition-transform">
                                <span class="counter" data-target="{{ $totalExperience }}">0</span>
                            </div>
                            <div class="text-gray-600 text-sm uppercase tracking-wider">Years Experience</div>
                        </div>
                        <div class="text-center group">
                            <div class="text-4xl font-bold text-purple-600 mb-2 transform group-hover:scale-110 transition-transform">
                                <span class="counter" data-target="{{ $totalProjects }}">0</span>
                            </div>
                            <div class="text-gray-600 text-sm uppercase tracking-wider">Projects Delivered</div>
                        </div>
                        <div class="text-center group">
                            <div class="text-4xl font-bold text-pink-600 mb-2 transform group-hover:scale-110 transition-transform">
                                <span class="counter" data-target="{{ $happyClients }}">0</span>
                            </div>
                            <div class="text-gray-600 text-sm uppercase tracking-wider">Happy Clients</div>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="flex flex-wrap gap-4 pt-8" x-data="{ show: false }"
                         x-init="setTimeout(() => show = true, 3500)" x-show="show" x-transition>
                        <a href="#portfolio"
                           class="px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-semibold hover:shadow-2xl hover:shadow-blue-500/30 transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-3 group">
                            <x-frontend.icon name="eye" class="w-5 h-5" />
                            View Projects
                            <x-frontend.icon name="arrow-right" class="w-5 h-5 group-hover:translate-x-2 transition-transform" />
                        </a>
                        <a href="#contact"
                           class="px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-semibold hover:shadow-2xl hover:shadow-purple-500/30 transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-3 group">
                            <x-frontend.icon name="chat" class="w-5 h-5" />
                            Hire Me Now
                            <x-frontend.icon name="arrow-right" class="w-5 h-5 group-hover:translate-x-2 transition-transform" />
                        </a>
                        <a href="{{ route('resume') }}"
                           class="px-8 py-4 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:border-blue-500 hover:text-blue-600 transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-3 group">
                            <x-frontend.icon name="document-text" class="w-5 h-5" />
                            Download CV
                        </a>
                    </div>

                    <!-- Quick Contact Info -->
                    <div class="pt-8 border-t border-gray-200" x-data="{ show: false }"
                         x-init="setTimeout(() => show = true, 4000)" x-show="show" x-transition>
                        <div class="flex flex-wrap gap-6">
                            <a href="mailto:{{ $contactEmail }}" 
                               class="flex items-center gap-3 text-gray-600 hover:text-blue-600 transition-colors group">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-500 transition-colors">
                                    <x-frontend.icon name="mail" class="w-5 h-5 text-blue-600 group-hover:text-white" />
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Email Me</div>
                                    <div class="font-medium">{{ $contactEmail }}</div>
                                </div>
                            </a>
                            <a href="https://wa.me/{{ preg_replace('/\D+/', '', $contactPhone) }}"
                                target="_blank"
                                rel="noopener"
                                class="flex items-center gap-3 text-gray-600 hover:text-green-600 transition-colors group">

                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-500 transition-colors">
                                    <x-frontend.icon name="whatsapp" class="w-5 h-5 text-green-600 group-hover:text-white" />
                                </div>

                                <div>
                                    <div class="text-sm text-gray-500">WhatsApp Me</div>
                                    <div class="font-medium">{{ $contactPhone }}</div>
                                </div>
                            </a>

                        </div>
                    </div>
                </div>

                <!-- Right Column - Profile Image -->
                <div class="relative" x-data="{ show: false }"
                     x-init="setTimeout(() => show = true, 1000)" x-show="show" x-transition>
                    <div class="relative max-w-md mx-auto">
                        <!-- Floating elements -->
                        <div class="absolute -top-4 -right-4 w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full blur-xl opacity-30 animate-float"></div>
                        <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-gradient-to-tr from-purple-500 to-pink-500 rounded-full blur-xl opacity-30 animate-float animation-delay-2000"></div>
                        
                        <!-- Main Image Container -->
                        <div class="relative bg-gradient-to-br from-white to-gray-50 rounded-3xl p-3 shadow-2xl overflow-hidden group">
                            <!-- Glowing border -->
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 opacity-0 group-hover:opacity-10 transition-opacity duration-500 rounded-3xl"></div>
                            
                            <!-- Image -->
                            <div class="relative rounded-2xl overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200">
                                @if($user->avatar_url)
                                    <img 
                                        src="{{ $user->avatar_url }}" 
                                        alt="{{ $user->name ?? 'Profile Image' }}"
                                        class="w-full h-auto object-cover transform group-hover:scale-105 transition-transform duration-700"
                                    />
                                @else
                                    <div class="w-full h-96 flex items-center justify-center">
                                        <x-frontend.icon name="user" class="w-32 h-32 text-gray-400" />
                                    </div>
                                @endif
                                
                                <!-- Gradient Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/20 via-transparent to-transparent"></div>
                            </div>
                            
                            <!-- Status Badge -->
                            @if($availableForHire)
                                <div class="absolute top-6 right-6 bg-gradient-to-r from-green-500 to-emerald-600 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg animate-pulse">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 bg-white rounded-full"></span>
                                        Available for Hire
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Location Badge -->
                            <div class="absolute bottom-6 left-6 bg-white/90 backdrop-blur-sm text-gray-800 px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                                <div class="flex items-center gap-2">
                                    <x-frontend.icon name="location-marker" class="w-4 h-4 text-blue-600" />
                                    {{ $location }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2">
            <a href="#services" class="text-gray-400 hover:text-blue-600 transition-colors animate-bounce">
                <x-frontend.icon name="chevron-down" class="w-8 h-8" />
            </a>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <h2 class="inline-block text-4xl md:text-5xl font-bold text-gray-900 mb-6 relative"
                    x-data="{ show: false }" x-init="setTimeout(() => show = true, 500)" x-show="show" x-transition>
                    <span class="relative">
                        My Services
                        <span class="absolute -bottom-2 left-0 w-24 h-1 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full"></span>
                    </span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto"
                   x-data="{ show: false }" x-init="setTimeout(() => show = true, 800)" x-show="show" x-transition>
                    {{ $servicesDescription }}
                </p>
            </div>
            
            <!-- Services Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($services as $index => $service)
                    @php
                        $serviceData = is_array($service) ? $service : ['name' => $service, 'description' => ''];
                        $serviceName = $serviceData['name'] ?? $service;
                        $serviceDesc = $serviceData['description'] ?? "Professional {$serviceName} services with modern technologies and best practices.";
                        $icons = ['code', 'chart-bar', 'light-bulb', 'server', 'chart-bar', 'light-bulb'];
                        $colors = ['blue', 'purple', 'blue', 'green', 'yellow', 'indigo'];
                        $icon = $icons[$index % count($icons)] ?? 'code';
                        $color = $colors[$index % count($colors)] ?? 'blue';
                    @endphp
                    
                    <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl p-8 border border-gray-200 hover:border-transparent hover:shadow-2xl hover:shadow-{{$color}}-500/10 transition-all duration-500 transform hover:-translate-y-2 group"
                         x-data="{ show: false }" 
                         x-init="setTimeout(() => show = true, {{ $index * 200 + 1000 }})" 
                         x-show="show" x-transition>
                        <!-- Service Icon -->
                        <div class="w-16 h-16 bg-gradient-to-br from-{{$color}}-500 to-{{$color}}-600 rounded-2xl flex items-center justify-center mb-6 transform group-hover:rotate-12 transition-transform duration-500 shadow-lg shadow-{{$color}}-500/30">
                            <x-frontend.icon name="{{ $icon }}" class="w-8 h-8 text-white" />
                        </div>
                        
                        <!-- Service Content -->
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $serviceName }}</h3>
                        <p class="text-gray-600 leading-relaxed mb-6">
                            {{ $serviceDesc }}
                        </p>
                        
                        <!-- Technologies -->
                        <div class="flex flex-wrap gap-2 mb-6">
                            @php
                                $techs = ['Laravel', 'Next.js', 'React', 'Node.js', 'AWS', 'Docker'];
                            @endphp
                            @foreach(array_slice($techs, 0, 3) as $tech)
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">
                                    {{ $tech }}
                                </span>
                            @endforeach
                        </div>
                        
                        <!-- View Projects -->
                        <a href="{{ route('portfolio') }}?service={{ strtolower(str_replace(' ', '-', $serviceName)) }}" 
                           class="inline-flex items-center text-{{$color}}-600 font-semibold group">
                            <span>View Projects</span>
                            <x-frontend.icon name="arrow-right" class="w-5 h-5 ml-2 transform group-hover:translate-x-2 transition-transform" />
                        </a>
                    </div>
                @endforeach
            </div>
            
            <!-- View All Services -->
            <div class="text-center mt-12" x-data="{ show: false }" 
                 x-init="setTimeout(() => show = true, 2500)" x-show="show" x-transition>
                <a href="{{ route('contact') }}" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-gray-800 to-gray-900 text-white rounded-xl font-semibold hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 group">
                    <x-frontend.icon name="chat" class="w-5 h-5 mr-3" />
                    Discuss Your Project Requirements
                    <x-frontend.icon name="arrow-right" class="w-5 h-5 ml-3 group-hover:translate-x-2 transition-transform" />
                </a>
            </div>
        </div>
    </section>

    <!-- Skills Section -->
    <section class="py-20 bg-gradient-to-b from-white to-gray-50">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <!-- Left Column - Skills -->
                <div>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-8"
                        x-data="{ show: false }" x-init="setTimeout(() => show = true, 500)" x-show="show" x-transition>
                        Technical Expertise
                    </h2>
                    <p class="text-xl text-gray-600 mb-12 leading-relaxed"
                       x-data="{ show: false }" x-init="setTimeout(() => show = true, 800)" x-show="show" x-transition>
                        Mastery of modern technologies and frameworks to deliver scalable, efficient solutions.
                    </p>
                    
                    <!-- Skills Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach($skills as $index => $skill)
                            <div class="space-y-3" x-data="{ show: false }" 
                                 x-init="setTimeout(() => show = true, {{ $index * 200 + 1000 }})" 
                                 x-show="show" x-transition>
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-gray-900">{{ $skill->name }}</span>
                                    <span class="font-bold text-blue-600">{{ $skill->percentage }}%</span>
                                </div>
                                <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-1000 ease-out"
                                         x-data="{ width: 0 }"
                                         x-init="setTimeout(() => width = {{ $skill->percentage }}, {{ $index * 100 }})"
                                         :style="`width: ${width}%; background: linear-gradient(90deg, {{ $skill->color }}, {{ $skill->color }}80)`">
                                    </div>
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <x-frontend.icon name="clock" class="w-4 h-4 mr-2" />
                                    <span>Used in {{ rand(10, 50) }}+ projects</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Right Column - Technologies -->
                <div>
                    <div class="bg-white rounded-3xl p-8 shadow-2xl shadow-blue-500/5 border border-gray-200"
                         x-data="{ show: false }" x-init="setTimeout(() => show = true, 1500)" x-show="show" x-transition>
                        <h3 class="text-2xl font-bold text-gray-900 mb-8">Technologies I Work With</h3>
                        
                        <!-- Technology Grid -->
                        <div class="grid grid-cols-3 gap-6">
                            @php
                                $technologies = [
                                    ['name' => 'Laravel', 'icon' => 'server', 'color' => 'red'],
                                    ['name' => 'Vue.js', 'icon' => 'code', 'color' => 'green'],
                                    ['name' => 'React', 'icon' => 'lightning-bolt', 'color' => 'blue'],
                                    ['name' => 'Node.js', 'icon' => 'server', 'color' => 'green'],
                                    ['name' => 'AWS', 'icon' => 'cloud', 'color' => 'orange'],
                                    ['name' => 'Docker', 'icon' => 'cube', 'color' => 'blue'],
                                    ['name' => 'MySQL', 'icon' => 'database', 'color' => 'blue'],
                                    ['name' => 'Redis', 'icon' => 'database', 'color' => 'red'],
                                    ['name' => 'Git', 'icon' => 'code-branch', 'color' => 'orange'],
                                ];
                            @endphp
                            
                            @foreach($technologies as $tech)
                                <div class="text-center group">
                                    <div class="w-16 h-16 mx-auto bg-gradient-to-br from-{{ $tech['color'] }}-100 to-{{ $tech['color'] }}-200 rounded-2xl flex items-center justify-center mb-3 transform group-hover:scale-110 transition-transform">
                                        <x-frontend.icon name="{{ $tech['icon'] }}" class="w-8 h-8 text-{{ $tech['color'] }}-600" />
                                    </div>
                                    <div class="font-medium text-gray-900">{{ $tech['name'] }}</div>
                                    <div class="text-sm text-gray-500">Expert</div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- View All Skills -->
                        <div class="mt-12 pt-8 border-t border-gray-200">
                            <a href="{{ route('contact') }}" 
                               class="inline-flex items-center justify-center w-full px-6 py-3 bg-gradient-to-r from-blue-50 to-purple-50 text-blue-600 rounded-xl font-semibold hover:from-blue-100 hover:to-purple-100 transition-colors group">
                                Contact Me to Discuss Your Tech Needs
                                <x-frontend.icon name="arrow-right" class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Preview -->
    <section id="portfolio" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6"
                    x-data="{ show: false }" x-init="setTimeout(() => show = true, 500)" x-show="show" x-transition>
                    Featured Projects
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto"
                   x-data="{ show: false }" x-init="setTimeout(() => show = true, 800)" x-show="show" x-transition>
                    A selection of my recent work that showcases technical expertise and problem-solving abilities.
                </p>
            </div>
            
            <!-- Projects Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredProjects as $index => $project)
                    <div x-data="{ show: false }" 
                         x-init="setTimeout(() => show = true, {{ $index * 200 + 1000 }})" 
                         x-show="show" x-transition
                         class="transform hover:-translate-y-2 transition-transform duration-500">
                        <x-frontend.project-card :project="$project" />
                    </div>
                @endforeach
            </div>
            
            <!-- View All Projects -->
            <div class="text-center mt-16" x-data="{ show: false }" 
                 x-init="setTimeout(() => show = true, 2500)" x-show="show" x-transition>
                <a href="{{ route('portfolio') }}" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-semibold hover:shadow-2xl hover:shadow-blue-500/30 transition-all duration-300 transform hover:-translate-y-1 group">
                    <x-frontend.icon name="folder-open" class="w-5 h-5 mr-3" />
                    View All Projects
                    <x-frontend.icon name="arrow-right" class="w-5 h-5 ml-3 group-hover:translate-x-2 transition-transform" />
                </a>
            </div>
        </div>
    </section>

    <!-- Add CSS for animations -->
    <style>
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }
            33% {
                transform: translate(30px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }
        
        .animate-blob {
            animation: blob 7s infinite;
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        
        .counter {
            display: inline-block;
        }
    </style>

    <!-- Add JavaScript for counters and animations -->
    @push('scripts')
    <script>
        // Counter animation
        function animateCounter(element) {
            const target = parseInt(element.getAttribute('data-target'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    element.textContent = target + '+';
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current) + '+';
                }
            }, 16);
        }
        
        // Intersection Observer for animations
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize counters
            document.querySelectorAll('.counter').forEach(counter => {
                animateCounter(counter);
            });
            
            // Add scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, observerOptions);
            
            // Observe elements
            document.querySelectorAll('[x-data]').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
    @endpush
</x-frontend.layout>