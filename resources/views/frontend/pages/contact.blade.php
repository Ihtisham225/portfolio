<x-frontend.layout :metaTags="$metaTags ?? []">
    <!-- Contact Section -->
    <section class="py-16">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div>
                    <h2 class="text-3xl font-bold mb-8">Send a Message</h2>
                    <form method="POST" action="{{ route('contact.submit') }}" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-frontend.input 
                                name="name" 
                                label="Your Name"
                                value="{{ old('name') }}"
                                placeholder="John Doe"
                                required
                                :error="$errors->first('name')"
                            />
                            <x-frontend.input 
                                name="email" 
                                type="email"
                                label="Email Address"
                                value="{{ old('email') }}"
                                placeholder="john@example.com"
                                required
                                :error="$errors->first('email')"
                            />
                        </div>
                        <x-frontend.input 
                            name="subject" 
                            label="Subject"
                            value="{{ old('subject') }}"
                            placeholder="Project Inquiry"
                            required
                            :error="$errors->first('subject')"
                        />
                        <x-frontend.textarea 
                            name="message" 
                            label="Message"
                            rows="6"
                            placeholder="Tell me about your project..."
                            required
                            :error="$errors->first('message')"
                        >{{ old('message') }}</x-frontend.textarea>
                        
                        <x-frontend.button type="submit" class="w-full">
                            Send Message
                            <x-frontend.icon name="send" class="w-5 h-5 ml-2" />
                        </x-frontend.button>
                    </form>
                </div>

                <!-- Contact Information -->
                <div>
                    <h2 class="text-3xl font-bold mb-8">Contact Information</h2>
                    <div class="space-y-8">
                        <!-- Contact Details -->
                        <div class="space-y-6">
                            <div class="flex items-start group">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl flex items-center justify-center mr-4 group-hover:from-blue-200 group-hover:to-blue-100 transition-all duration-300">
                                    <x-frontend.icon name="mail" class="w-6 h-6 text-blue-600" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold mb-1 text-gray-900">Email</h3>
                                    <a href="mailto:{{ $contactInfo['email'] }}" 
                                    class="text-gray-600 hover:text-blue-600 transition-colors inline-flex items-center group">
                                        <span>{{ $contactInfo['email'] }}</span>
                                        <x-frontend.icon name="arrow-right" class="w-4 h-4 ml-2 opacity-0 group-hover:opacity-100 transform group-hover:translate-x-1 transition-all" />
                                    </a>
                                </div>
                            </div>
                            
                            @if($contactInfo['phone'])
                                <div class="flex items-start group">
                                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-green-100 to-green-50 rounded-xl flex items-center justify-center mr-4 group-hover:from-green-200 group-hover:to-green-100 transition-all duration-300">
                                        <x-frontend.icon name="phone" class="w-6 h-6 text-green-600" />
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold mb-1 text-gray-900">Phone</h3>
                                        <a href="tel:{{ $contactInfo['phone'] }}" 
                                        class="text-gray-600 hover:text-green-600 transition-colors inline-flex items-center group">
                                            <span>{{ $contactInfo['phone'] }}</span>
                                            <x-frontend.icon name="arrow-right" class="w-4 h-4 ml-2 opacity-0 group-hover:opacity-100 transform group-hover:translate-x-1 transition-all" />
                                        </a>
                                    </div>
                                </div>
                            @endif
                            
                            @if($contactInfo['address'])
                                <div class="flex items-start group">
                                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-50 rounded-xl flex items-center justify-center mr-4 group-hover:from-purple-200 group-hover:to-purple-100 transition-all duration-300">
                                        <x-frontend.icon name="map-pin" class="w-6 h-6 text-purple-600" />
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold mb-1 text-gray-900">Location</h3>
                                        <p class="text-gray-600 flex items-center">
                                            <span>{{ $contactInfo['address'] }}</span>
                                            @if($contactInfo['timezone'])
                                                <span class="ml-2 text-sm bg-gray-100 text-gray-600 px-2 py-1 rounded">
                                                    {{ $contactInfo['timezone'] }}
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Social Links -->
                        @if(collect($socialLinks)->filter(fn($link) => $link !== '#')->count() > 0)
                            <div>
                                <h3 class="text-lg font-bold mb-4 text-gray-900">Connect With Me</h3>
                                <div class="grid grid-cols-5 gap-3">
                                    @if($socialLinks['github'] !== '#')
                                        <a href="{{ $socialLinks['github'] }}" target="_blank" 
                                        class="flex flex-col items-center justify-center p-3 bg-gray-50 rounded-xl hover:bg-gray-100 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 group"
                                        title="GitHub">
                                            <div class="w-10 h-10 bg-gray-900 rounded-full flex items-center justify-center mb-2 group-hover:bg-gray-800">
                                                <x-frontend.icon name="github" class="w-5 h-5 text-white" />
                                            </div>
                                            <span class="text-xs text-gray-600 font-medium">GitHub</span>
                                        </a>
                                    @endif
                                    
                                    @if($socialLinks['linkedin'] !== '#')
                                        <a href="{{ $socialLinks['linkedin'] }}" target="_blank" 
                                        class="flex flex-col items-center justify-center p-3 bg-blue-50 rounded-xl hover:bg-blue-100 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 group"
                                        title="LinkedIn">
                                            <div class="w-10 h-10 bg-blue-700 rounded-full flex items-center justify-center mb-2 group-hover:bg-blue-600">
                                                <x-frontend.icon name="linkedin" class="w-5 h-5 text-white" />
                                            </div>
                                            <span class="text-xs text-gray-600 font-medium">LinkedIn</span>
                                        </a>
                                    @endif
                                    
                                    @if($socialLinks['twitter'] !== '#')
                                        <a href="{{ $socialLinks['twitter'] }}" target="_blank" 
                                        class="flex flex-col items-center justify-center p-3 bg-blue-50 rounded-xl hover:bg-blue-100 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 group"
                                        title="Twitter">
                                            <div class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center mb-2 group-hover:bg-blue-300">
                                                <x-frontend.icon name="twitter" class="w-5 h-5 text-white" />
                                            </div>
                                            <span class="text-xs text-gray-600 font-medium">Twitter</span>
                                        </a>
                                    @endif
                                    
                                    @if($socialLinks['instagram'] !== '#')
                                        <a href="{{ $socialLinks['instagram'] }}" target="_blank" 
                                        class="flex flex-col items-center justify-center p-3 bg-pink-50 rounded-xl hover:bg-pink-100 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 group"
                                        title="Instagram">
                                            <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center mb-2">
                                                <x-frontend.icon name="instagram" class="w-5 h-5 text-white" />
                                            </div>
                                            <span class="text-xs text-gray-600 font-medium">Instagram</span>
                                        </a>
                                    @endif
                                    
                                    @if($socialLinks['facebook'] !== '#')
                                        <a href="{{ $socialLinks['facebook'] }}" target="_blank" 
                                        class="flex flex-col items-center justify-center p-3 bg-blue-50 rounded-xl hover:bg-blue-100 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 group"
                                        title="Facebook">
                                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center mb-2 group-hover:bg-blue-500">
                                                <x-frontend.icon name="facebook" class="w-5 h-5 text-white" />
                                            </div>
                                            <span class="text-xs text-gray-600 font-medium">Facebook</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Working Hours -->
                        <div>
                            <h3 class="text-lg font-bold mb-4 text-gray-900">Business Hours</h3>
                            <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 p-6 shadow-sm">
                                <div class="flex items-center mb-4">
                                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-100 to-yellow-50 rounded-lg flex items-center justify-center mr-3">
                                        <x-frontend.icon name="clock" class="w-5 h-5 text-yellow-600" />
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">Availability</h4>
                                        <p class="text-sm text-gray-500">Current timezone: {{ $contactInfo['timezone'] ?? 'EST' }}</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-3">
                                    @foreach($contactInfo['business_hours_parsed'] as $schedule)
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                                            <span class="text-gray-700 font-medium">{{ $schedule['days'] }}</span>
                                            <span class="text-gray-600 {{ $schedule['hours'] === 'Closed' ? 'text-red-500' : '' }}">
                                                {{ $schedule['hours'] }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                
                                @if($contactInfo['response_time'])
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <x-frontend.icon name="information-circle" class="w-4 h-4 mr-2 text-blue-500" />
                                            <span>Typical response time: <span class="font-medium">{{ $contactInfo['response_time'] }}</span></span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="rounded-2xl overflow-hidden shadow-xl h-96">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.576700856079!2d-122.41941618468171!3d37.774929779759134!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8085808c5e72d7f9%3A0x5b7c52e4e6a6b9a!2sSan%20Francisco%2C%20CA!5e0!3m2!1sen!2sus!4v1623456789012!5m2!1sen!2sus" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy"
                ></iframe>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-16">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Frequently Asked Questions</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Find answers to common questions about working with me.
                </p>
            </div>
            
            <div class="max-w-3xl mx-auto">
                <div class="space-y-4">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                        <h3 class="text-lg font-bold mb-2">What services do you offer?</h3>
                        <p class="text-gray-600">
                            I offer full-stack web development, including custom web applications, API development, 
                            front-end development with modern frameworks, and website maintenance.
                        </p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                        <h3 class="text-lg font-bold mb-2">What is your typical project timeline?</h3>
                        <p class="text-gray-600">
                            Project timelines vary based on scope and complexity. Small projects typically take 2-4 weeks, 
                            medium projects 4-8 weeks, and large projects 8+ weeks. We'll establish a timeline during our initial consultation.
                        </p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                        <h3 class="text-lg font-bold mb-2">Do you work with remote clients?</h3>
                        <p class="text-gray-600">
                            Yes, I work with clients worldwide. All communication and project management are handled 
                            through video calls, email, and project management tools.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-frontend.layout>