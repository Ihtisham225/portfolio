<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Post;
use App\Models\Message;
use App\Models\Setting;
use App\Models\User;
use App\Models\Skill;
use App\Services\PortfolioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    protected $portfolioService;

    public function __construct(PortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }

    public function index()
    {
        // Use PortfolioService for stats
        $portfolioStats = $this->portfolioService->getPortfolioStats();
        
        // Enhanced stats with additional metrics
        $stats = array_merge($portfolioStats, [
            'total_users' => User::count(),
            'total_skills' => Skill::count(),
            'active_projects' => Project::where('status', 'published')->count(),
            'pending_messages' => Message::whereIn('status', ['unread', 'read'])->count(),
        ]);

        // Get recent activity from service
        $recentActivity = $this->portfolioService->getRecentActivity(5);
        $recentMessages = $recentActivity['messages'];
        $recentProjects = $recentActivity['projects'];
        $recentPosts = $recentActivity['posts'];

        // Enhanced project views chart data with daily breakdown
        $projectViews = Project::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total_projects'),
            DB::raw('SUM(views) as total_views'),
            DB::raw('AVG(views) as avg_views')
        )
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Monthly stats with comparison
        $currentMonth = now()->month;
        $previousMonth = now()->subMonth()->month;
        
        $monthlyStats = [
            'projects' => [
                'current' => Project::whereMonth('created_at', $currentMonth)->count(),
                'previous' => Project::whereMonth('created_at', $previousMonth)->count(),
                'growth' => $this->calculateGrowth(
                    Project::whereMonth('created_at', $currentMonth)->count(),
                    Project::whereMonth('created_at', $previousMonth)->count()
                ),
            ],
            'posts' => [
                'current' => Post::whereMonth('created_at', $currentMonth)->count(),
                'previous' => Post::whereMonth('created_at', $previousMonth)->count(),
                'growth' => $this->calculateGrowth(
                    Post::whereMonth('created_at', $currentMonth)->count(),
                    Post::whereMonth('created_at', $previousMonth)->count()
                ),
            ],
            'messages' => [
                'current' => Message::whereMonth('created_at', $currentMonth)->count(),
                'previous' => Message::whereMonth('created_at', $previousMonth)->count(),
                'growth' => $this->calculateGrowth(
                    Message::whereMonth('created_at', $currentMonth)->count(),
                    Message::whereMonth('created_at', $previousMonth)->count()
                ),
            ],
        ];

        // Popular content
        $popularContent = [
            'top_projects' => Project::published()
                ->orderBy('views', 'desc')
                ->take(5)
                ->get(),
            'top_posts' => Post::published()
                ->orderBy('views', 'desc')
                ->take(5)
                ->get(),
        ];

        // Performance metrics
        $performanceMetrics = [
            'avg_project_views' => round(Project::avg('views'), 1),
            'avg_post_views' => round(Post::avg('views'), 1),
            'conversion_rate' => $this->calculateConversionRate(),
            'engagement_rate' => $this->calculateEngagementRate(),
        ];

        return view('admin.dashboard.index', compact(
            'stats',
            'recentMessages',
            'recentProjects',
            'recentPosts',
            'projectViews',
            'monthlyStats',
            'popularContent',
            'performanceMetrics'
        ));
    }

    public function analytics()
    {
        // Visitor analytics - you can integrate with Google Analytics API here
        $visitorData = $this->getVisitorAnalytics();
        
        // Content analytics
        $contentAnalytics = [
            'posts' => [
                'total' => Post::count(),
                'published' => Post::published()->count(),
                'drafts' => Post::where('status', 'draft')->count(),
                'featured' => Post::where('is_featured', true)->count(),
                'avg_views' => round(Post::avg('views'), 1),
            ],
            'projects' => [
                'total' => Project::count(),
                'published' => Project::published()->count(),
                'featured' => Project::where('is_featured', true)->count(),
                'avg_views' => round(Project::avg('views'), 1),
            ],
        ];

        // Popular content with detailed metrics
        $popularPosts = Post::published()
            ->with(['user', 'categories'])
            ->withCount('comments')
            ->orderBy('views', 'desc')
            ->take(10)
            ->get()
            ->map(function ($post) {
                return [
                    'post' => $post,
                    'engagement_rate' => $this->calculatePostEngagement($post),
                    'read_time' => $post->read_time,
                ];
            });

        $popularProjects = Project::published()
            ->with(['user', 'tags'])
            ->orderBy('views', 'desc')
            ->take(10)
            ->get()
            ->map(function ($project) {
                return [
                    'project' => $project,
                    'technologies_count' => count($project->technologies ?? []),
                    'gallery_count' => count($project->gallery ?? []),
                ];
            });

        // Time-based analytics
        $timeAnalytics = [
            'posts_by_month' => $this->getPostsByMonth(),
            'projects_by_month' => $this->getProjectsByMonth(),
            'messages_by_month' => $this->getMessagesByMonth(),
        ];

        // Performance metrics
        $performanceMetrics = [
            'page_load_time' => '1.2s', // This would come from monitoring service
            'uptime' => '99.9%',
            'cache_hit_rate' => '92%',
            'api_response_time' => '0.8s',
        ];

        return view('admin.dashboard.analytics', compact(
            'visitorData',
            'contentAnalytics',
            'popularPosts',
            'popularProjects',
            'timeAnalytics',
            'performanceMetrics'
        ));
    }

    public function sitemap()
    {
        $portfolioService = app(PortfolioService::class);
        $sitemapContent = $portfolioService->generateSitemap();
        
        return response($sitemapContent)
            ->header('Content-Type', 'text/xml');
    }

    public function exportData(Request $request)
    {
        $request->validate([
            'type' => 'required|in:projects,posts,messages',
            'format' => 'required|in:csv,json',
        ]);

        $data = [];
        $filename = '';

        switch ($request->type) {
            case 'projects':
                $data = Project::with(['user', 'categories', 'tags'])->get();
                $filename = 'projects_export_' . date('Y-m-d') . '.' . $request->format;
                break;
            case 'posts':
                $data = Post::with(['user', 'categories', 'tags'])->get();
                $filename = 'posts_export_' . date('Y-m-d') . '.' . $request->format;
                break;
            case 'messages':
                $data = Message::all();
                $filename = 'messages_export_' . date('Y-m-d') . '.' . $request->format;
                break;
        }

        if ($request->format === 'csv') {
            return $this->exportToCsv($data, $filename);
        }

        return response()->json($data)->header(
            'Content-Disposition',
            'attachment; filename="' . $filename . '"'
        );
    }

    /**
     * Helper Methods
     */
    
    private function calculateGrowth($current, $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 2);
    }

    private function calculateConversionRate(): float
    {
        $totalVisitors = 10000; // This would come from analytics
        $totalMessages = Message::count();
        
        if ($totalVisitors == 0) return 0;
        
        return round(($totalMessages / $totalVisitors) * 100, 2);
    }

    private function calculateEngagementRate(): float
    {
        $totalViews = Post::sum('views') + Project::sum('views');
        $totalComments = 0; // You would need a comments model
        
        if ($totalViews == 0) return 0;
        
        return round(($totalComments / $totalViews) * 100, 2);
    }

    private function calculatePostEngagement(Post $post): float
    {
        $commentsCount = $post->comments_count ?? 0;
        $views = $post->views;
        
        if ($views == 0) return 0;
        
        return round(($commentsCount / $views) * 100, 2);
    }

    private function getVisitorAnalytics(): array
    {
        // This is mock data - integrate with Google Analytics or similar service
        return [
            'today' => [
                'visitors' => 150,
                'pageviews' => 450,
                'avg_session' => '3m 45s',
                'bounce_rate' => '42%',
            ],
            'yesterday' => [
                'visitors' => 120,
                'pageviews' => 380,
                'avg_session' => '3m 20s',
                'bounce_rate' => '45%',
            ],
            'this_week' => [
                'visitors' => 850,
                'pageviews' => 2500,
                'avg_session' => '3m 30s',
                'bounce_rate' => '43%',
            ],
            'last_week' => [
                'visitors' => 720,
                'pageviews' => 2100,
                'avg_session' => '3m 15s',
                'bounce_rate' => '46%',
            ],
            'this_month' => [
                'visitors' => 3200,
                'pageviews' => 9800,
                'avg_session' => '3m 40s',
                'bounce_rate' => '41%',
            ],
            'last_month' => [
                'visitors' => 2800,
                'pageviews' => 8500,
                'avg_session' => '3m 25s',
                'bounce_rate' => '44%',
            ],
        ];
    }

    private function getPostsByMonth(): array
    {
        return Post::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subYear())
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get()
        ->map(function ($item) {
            return [
                'month' => Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                'count' => $item->count,
            ];
        })
        ->toArray();
    }

    private function getProjectsByMonth(): array
    {
        return Project::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subYear())
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get()
        ->map(function ($item) {
            return [
                'month' => Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                'count' => $item->count,
            ];
        })
        ->toArray();
    }

    private function getMessagesByMonth(): array
    {
        return Message::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subYear())
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get()
        ->map(function ($item) {
            return [
                'month' => Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                'count' => $item->count,
            ];
        })
        ->toArray();
    }

    private function exportToCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            if (count($data) > 0) {
                $firstRow = (array) $data[0];
                fputcsv($file, array_keys($firstRow));
            }
            
            // Add data
            foreach ($data as $row) {
                fputcsv($file, (array) $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}