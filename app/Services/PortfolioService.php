<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Post;
use App\Models\Message;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\Education;
use App\Models\Certification;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PortfolioService
{
    public function getPortfolioStats(): array
    {
        return Cache::remember('portfolio_stats', 3600, function () {
            return [
                'total_projects' => Project::count(),
                'published_projects' => Project::published()->count(),
                'featured_projects' => Project::where('is_featured', true)->count(),
                'total_posts' => Post::count(),
                'published_posts' => Post::published()->count(),
                'featured_posts' => Post::where('is_featured', true)->count(),
                'total_messages' => Message::count(),
                'unread_messages' => Message::unread()->count(),
                'total_skills' => Skill::count(),
                'featured_skills' => Skill::featured()->count(),
                'total_experiences' => Experience::count(),
                'total_education' => Education::count(),
                'total_certifications' => Certification::count(),
            ];
        });
    }

    public function getRecentActivity(int $limit = 10): array
    {
        return Cache::remember('recent_activity', 300, function () use ($limit) {
            return [
                'projects' => Project::latest()->take($limit)->get(),
                'posts' => Post::latest()->take($limit)->get(),
                'messages' => Message::latest()->take($limit)->get(),
            ];
        });
    }

    public function generateSitemap(): string
    {
        $projects = Project::published()->get();
        $posts = Post::published()->get();
        $categories = Category::all();
        $tags = Tag::all();
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
                        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" 
                        xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';
        
        // Add static pages
        $staticPages = [
            ['url' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => route('portfolio'), 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['url' => route('blog'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('contact'), 'priority' => '0.7', 'changefreq' => 'monthly'],
        ];
        
        foreach ($staticPages as $page) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($page['url']) . '</loc>';
            $xml .= '<priority>' . $page['priority'] . '</priority>';
            $xml .= '<changefreq>' . $page['changefreq'] . '</changefreq>';
            $xml .= '</url>';
        }
        
        // Add projects
        foreach ($projects as $project) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars(route('portfolio.detail', $project->slug)) . '</loc>';
            $xml .= '<lastmod>' . $project->updated_at->toIso8601String() . '</lastmod>';
            $xml .= '<priority>0.7</priority>';
            $xml .= '<changefreq>monthly</changefreq>';
            
            // Add project image if exists
            if ($project->image) {
                $xml .= '<image:image>';
                $xml .= '<image:loc>' . htmlspecialchars(Storage::url($project->image)) . '</image:loc>';
                $xml .= '<image:title>' . htmlspecialchars($project->title) . '</image:title>';
                $xml .= '<image:caption>' . htmlspecialchars($project->excerpt) . '</image:caption>';
                $xml .= '</image:image>';
            }
            
            $xml .= '</url>';
        }
        
        // Add blog posts
        foreach ($posts as $post) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars(route('blog.detail', $post->slug)) . '</loc>';
            $xml .= '<lastmod>' . $post->updated_at->toIso8601String() . '</lastmod>';
            $xml .= '<priority>0.6</priority>';
            $xml .= '<changefreq>monthly</changefreq>';
            
            // Add post image if exists
            if ($post->featured_image) {
                $xml .= '<image:image>';
                $xml .= '<image:loc>' . htmlspecialchars(Storage::url($post->featured_image)) . '</image:loc>';
                $xml .= '<image:title>' . htmlspecialchars($post->title) . '</image:title>';
                $xml .= '<image:caption>' . htmlspecialchars($post->excerpt) . '</image:caption>';
                $xml .= '</image:image>';
            }
            
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }

    public function getPerformanceMetrics(): array
    {
        return Cache::remember('performance_metrics', 1800, function () {
            // Calculate various performance metrics
            $totalViews = Post::sum('views') + Project::sum('views');
            $totalContent = Post::count() + Project::count();
            
            return [
                'avg_views_per_content' => $totalContent > 0 ? round($totalViews / $totalContent, 1) : 0,
                'avg_message_response_time' => $this->calculateAvgResponseTime(),
                'content_engagement_rate' => $this->calculateEngagementRate(),
                'popular_content' => $this->getPopularContent(5),
                'recent_activity_timeline' => $this->getActivityTimeline(),
            ];
        });
    }

    public function getContentAnalytics(): array
    {
        return Cache::remember('content_analytics', 3600, function () {
            $currentYear = Carbon::now()->year;
            $currentMonth = Carbon::now()->month;
            
            return [
                'posts_by_year' => $this->getPostsByYear($currentYear),
                'projects_by_year' => $this->getProjectsByYear($currentYear),
                'monthly_totals' => [
                    'posts' => $this->getMonthlyTotals(Post::class, $currentYear),
                    'projects' => $this->getMonthlyTotals(Project::class, $currentYear),
                    'messages' => $this->getMonthlyTotals(Message::class, $currentYear),
                ],
                'top_categories' => Category::withCount(['posts', 'projects'])
                    ->orderByRaw('(posts_count + projects_count) DESC')
                    ->take(10)
                    ->get(),
                'top_tags' => Tag::withCount(['posts', 'projects'])
                    ->orderByRaw('(posts_count + projects_count) DESC')
                    ->take(15)
                    ->get(),
            ];
        });
    }

    public function generateRobotsTxt(): string
    {
        $isProduction = config('app.env') === 'production';
        $sitemapUrl = route('sitemap');
        
        $content = "User-agent: *\n";
        
        if ($isProduction) {
            $content .= "Allow: /\n";
            $content .= "Disallow: /admin/\n";
            $content .= "Disallow: /api/\n";
            $content .= "Disallow: /storage/\n";
            $content .= "Disallow: /vendor/\n";
        } else {
            $content .= "Disallow: /\n";
        }
        
        $content .= "\nSitemap: {$sitemapUrl}\n";
        
        return $content;
    }

    /**
     * Helper Methods
     */
    
    private function calculateAvgResponseTime(): ?string
    {
        $repliedMessages = Message::whereNotNull('responded_at')
            ->whereNotNull('created_at')
            ->get();
        
        if ($repliedMessages->isEmpty()) {
            return null;
        }
        
        $totalSeconds = 0;
        foreach ($repliedMessages as $message) {
            $totalSeconds += $message->created_at->diffInSeconds($message->responded_at);
        }
        
        $avgSeconds = $totalSeconds / $repliedMessages->count();
        
        if ($avgSeconds < 60) {
            return round($avgSeconds) . ' seconds';
        } elseif ($avgSeconds < 3600) {
            return round($avgSeconds / 60) . ' minutes';
        } elseif ($avgSeconds < 86400) {
            return round($avgSeconds / 3600) . ' hours';
        } else {
            return round($avgSeconds / 86400) . ' days';
        }
    }

    private function calculateEngagementRate(): float
    {
        // This is a simplified calculation
        $totalViews = Post::sum('views') + Project::sum('views');
        $totalInteractions = Message::count(); // Could include comments, likes, etc.
        
        if ($totalViews == 0) {
            return 0;
        }
        
        return round(($totalInteractions / $totalViews) * 100, 2);
    }

    private function getPopularContent(int $limit = 5): array
    {
        $popularPosts = Post::published()
            ->orderBy('views', 'desc')
            ->take($limit)
            ->get(['id', 'title', 'slug', 'views', 'created_at']);
        
        $popularProjects = Project::published()
            ->orderBy('views', 'desc')
            ->take($limit)
            ->get(['id', 'title', 'slug', 'views', 'created_at']);
        
        return [
            'posts' => $popularPosts,
            'projects' => $popularProjects,
        ];
    }

    private function getActivityTimeline(): array
    {
        $timeline = [];
        
        // Get last 30 days of activity
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays($i);
            
            $timeline[$date->format('Y-m-d')] = [
                'posts' => Post::whereDate('created_at', $date)->count(),
                'projects' => Project::whereDate('created_at', $date)->count(),
                'messages' => Message::whereDate('created_at', $date)->count(),
            ];
        }
        
        return $timeline;
    }

    private function getPostsByYear(int $year): array
    {
        return Post::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
    }

    private function getProjectsByYear(int $year): array
    {
        return Project::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
    }

    private function getMonthlyTotals(string $model, int $year): array
    {
        $months = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $months[$month] = $model::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
        }
        
        return $months;
    }

    public function clearCache(): void
    {
        Cache::forget('portfolio_stats');
        Cache::forget('recent_activity');
        Cache::forget('performance_metrics');
        Cache::forget('content_analytics');
    }
}