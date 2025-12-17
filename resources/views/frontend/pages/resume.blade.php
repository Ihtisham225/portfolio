<x-frontend.layout :metaTags="$metaTags ?? []">
    <!-- Resume Content -->
    <section class="py-16">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">
                <!-- Summary -->
                <div class="mb-12">
                    <h2 class="text-3xl font-bold mb-6">Professional Summary</h2>
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                        <p class="text-gray-600 text-lg leading-relaxed">
                            {{ $user->bio ?? 'Experienced Full Stack Developer with 5+ years of expertise in building scalable web applications. 
                            Proficient in modern technologies including Laravel, Vue.js, React, and cloud platforms. 
                            Passionate about clean code, best practices, and delivering exceptional user experiences.' }}
                        </p>
                    </div>
                </div>

                <!-- Experience -->
                <div class="mb-12">
                    <h2 class="text-3xl font-bold mb-8">Work Experience</h2>
                    <div class="space-y-8">
                        @foreach($experiences as $experience)
                            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                                <div class="flex flex-col md:flex-row md:items-center justify-between mb-4">
                                    <h3 class="text-xl font-bold">{{ $experience->title }}</h3>
                                    <span class="text-blue-600 font-semibold">{{ $experience->company }}</span>
                                </div>
                                <div class="flex items-center text-gray-500 mb-4">
                                    <x-frontend.icon name="calendar" class="w-4 h-4 mr-2" />
                                    <span>{{ $experience->formatted_start_date }} - {{ $experience->formatted_end_date }}</span>
                                    <span class="mx-2">•</span>
                                    <span class="text-blue-600 font-medium">{{ $experience->duration }}</span>
                                </div>
                                <div class="flex items-center text-gray-500 mb-4">
                                    <x-frontend.icon name="map-pin" class="w-4 h-4 mr-2" />
                                    <span>{{ $experience->location }}</span>
                                </div>
                                <p class="text-gray-600 mb-4">{{ $experience->description }}</p>
                                @if($experience->technologies_array->isNotEmpty())
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($experience->technologies_array as $tech)
                                            <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full">
                                                {{ $tech }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Education -->
                <div class="mb-12">
                    <h2 class="text-3xl font-bold mb-8">Education</h2>
                    <div class="space-y-8">
                        @foreach($educations as $education)
                            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                                <h3 class="text-xl font-bold mb-2">{{ $education->degree }}</h3>
                                <div class="flex items-center text-gray-500 mb-2">
                                    <x-frontend.icon name="map-pin" class="w-4 h-4 mr-2" />
                                    <span>{{ $education->institution }}, {{ $education->location }}</span>
                                </div>
                                <div class="flex items-center text-gray-500 mb-4">
                                    <x-frontend.icon name="calendar" class="w-4 h-4 mr-2" />
                                    <span>{{ $education->formatted_start_date }} - {{ $education->formatted_end_date }}</span>
                                    <span class="mx-2">•</span>
                                    <span class="text-blue-600 font-medium">{{ $education->duration }}</span>
                                </div>
                                @if($education->score)
                                    <div class="text-blue-600 font-semibold">
                                        GPA: {{ $education->score_formatted }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Skills -->
                <div class="mb-12">
                    <h2 class="text-3xl font-bold mb-8">Technical Skills</h2>
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @foreach($skillsFlat->chunk(ceil($skillsFlat->count() / 2)) as $skillChunk)
                                <div>
                                    @foreach($skillChunk as $skill)
                                        <div class="mb-6">
                                            <div class="flex justify-between mb-2">
                                                <span class="font-semibold">{{ $skill->name }}</span>
                                                <span class="font-semibold">{{ $skill->percentage }}%</span>
                                            </div>
                                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                                <div
                                                    class="h-full rounded-full transition-all duration-1000"
                                                    style="width: {{ $skill->percentage }}%; background-color: {{ $skill->color }}"
                                                ></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

                <!-- Certifications -->
                @if($certifications->isNotEmpty())
                    <div class="mb-12">
                        <h2 class="text-3xl font-bold mb-8">Certifications</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($certifications as $certification)
                                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                                    <div class="flex items-start">
                                        @if($certification->image_url)
                                            <img 
                                                src="{{ $certification->image_url }}" 
                                                alt="{{ $certification->name }}"
                                                class="w-12 h-12 rounded-lg mr-4 object-cover"
                                            >
                                        @endif
                                        <div class="flex-1">
                                            <h3 class="text-lg font-bold mb-1">{{ $certification->name }}</h3>
                                            <div class="text-gray-600 mb-2">{{ $certification->issuer }}</div>
                                            <div class="flex items-center text-gray-500 mb-3">
                                                <x-frontend.icon name="calendar" class="w-4 h-4 mr-2" />
                                                <span>{{ $certification->formatted_issue_date }}</span>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                                {{ $certification->is_valid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $certification->validity_status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-frontend.layout>