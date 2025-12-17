<x-admin.layout title="Dashboard">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600">Welcome back, {{ auth()->user()->name }}!</p>
            </div>
            <div class="flex items-center space-x-4">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <x-admin.icon name="plus" class="w-5 h-5 inline mr-2" />
                    Quick Add
                </button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <x-admin.icon name="refresh" class="w-5 h-5 inline mr-2" />
                    Refresh
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-admin.stat-card 
            title="Total Projects" 
            :value="$stats['total_projects']"
            icon="folder"
            color="blue"
            :change="[
                'value' => $monthlyStats['projects']['growth'],
                'label' => 'from last month'
            ]"
        />
        
        <x-admin.stat-card 
            title="Published Posts" 
            :value="$stats['published_posts']"
            icon="file-text"
            color="green"
            :change="[
                'value' => $monthlyStats['posts']['growth'],
                'label' => 'from last month'
            ]"
        />
        
        <x-admin.stat-card 
            title="Unread Messages" 
            :value="$stats['unread_messages']"
            icon="mail"
            color="red"
            :change="[
                'value' => $monthlyStats['messages']['growth'],
                'label' => 'from last month'
            ]"
        />
        
        <x-admin.stat-card 
            title="Total Users" 
            :value="$stats['total_users']"
            icon="users"
            color="purple"
            change-label="Active"
        />
    </div>

    <!-- Charts & Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Project Views Chart -->
        <x-admin.card title="Project Views (Last 30 Days)" class="h-96">
            <canvas id="projectViewsChart" class="w-full h-80"></canvas>
        </x-admin.card>
        
        <!-- Recent Activity -->
        <x-admin.card title="Recent Activity">
            <div class="space-y-4">
                <x-admin.activity-item 
                    type="project" 
                    title="New project created" 
                    time="2 hours ago"
                    user="John Doe"
                />
                <x-admin.activity-item 
                    type="post" 
                    title="Blog post published" 
                    time="4 hours ago"
                    user="Jane Smith"
                />
                <x-admin.activity-item 
                    type="message" 
                    title="New message received" 
                    time="1 day ago"
                    user="Support Team"
                />
            </div>
        </x-admin.card>
    </div>

    <!-- Recent Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Projects -->
        <x-admin.card title="Recent Projects" :action="['href' => route('admin.projects.index'), 'label' => 'View All']">
            <div class="divide-y divide-gray-200">
                @foreach($recentProjects as $project)
                    <div class="py-4 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gray-200 rounded-lg overflow-hidden">
                                @if($project->image)
                                    <img 
                                        src="{{ Storage::url($project->image) }}" 
                                        alt="{{ $project->title }}"
                                        class="w-full h-full object-cover"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gray-300">
                                        <x-admin.icon name="folder" class="w-6 h-6 text-gray-600" />
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $project->title }}</h4>
                                <p class="text-sm text-gray-500">{{ $project->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <x-admin.badge :color="$project->status === 'published' ? 'green' : 'yellow'">
                            {{ ucfirst($project->status) }}
                        </x-admin.badge>
                    </div>
                @endforeach
            </div>
        </x-admin.card>
        
        <!-- Recent Messages -->
        <x-admin.card title="Recent Messages" :action="['href' => route('admin.messages.index'), 'label' => 'View All']">
            <div class="divide-y divide-gray-200">
                @foreach($recentMessages as $message)
                    <div class="py-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-900">{{ $message->name }}</h4>
                            <x-admin.badge :color="$message->status === 'unread' ? 'red' : 'gray'">
                                {{ ucfirst($message->status) }}
                            </x-admin.badge>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($message->subject, 50) }}</p>
                        <p class="text-xs text-gray-500">{{ $message->created_at->diffForHumans() }}</p>
                    </div>
                @endforeach
            </div>
        </x-admin.card>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('projectViewsChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($projectViews->pluck('date')),
                datasets: [{
                    label: 'Total Views',
                    data: @json($projectViews->pluck('total_views')),
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-admin.layout>