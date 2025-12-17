<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\ContactAutoReply;
use App\Mail\ContactMessage;
use App\Models\Project;
use App\Models\Post;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\Education;
use App\Models\Certification;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Message;
use App\Models\User;
use App\Models\Setting;
use App\Services\PortfolioService;
use App\Services\SeoService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    protected $portfolioService;
    protected $seoService;
    protected $fileUploadService;

    public function __construct(
        PortfolioService $portfolioService,
        SeoService $seoService,
        FileUploadService $fileUploadService
    ) {
        $this->portfolioService = $portfolioService;
        $this->seoService = $seoService;
        $this->fileUploadService = $fileUploadService;
    }

    public function index()
    {
        // Use cached portfolio stats
        $portfolioStats = $this->portfolioService->getPortfolioStats();
        
        $featuredProjects = Project::published()
            ->featured()
            ->sorted()
            ->take(6)
            ->get()
            ->map(function ($project) {
                // Get optimized image URL
                $project->optimized_image = $this->getOptimizedImage($project->image);
                return $project;
            });

        $skills = Skill::featured()
            ->sorted()
            ->get();

        $recentPosts = Post::published()
            ->with(['categories', 'tags'])
            ->recent(3)
            ->get()
            ->map(function ($post) {
                // Get optimized featured image URL
                $post->optimized_image = $this->getOptimizedImage($post->featured_image);
                return $post;
            });

        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags([
            'title' => config('app.name') . ' - Portfolio',
            'description' => 'Professional portfolio showcasing projects and skills',
            'og_image' => $user->avatar ?? asset('images/og-default.jpg'),
            'keywords' => 'portfolio, web development, laravel, ' . 'developer',
        ]);

        // Get testimonials or featured content
        $testimonials = $this->getTestimonials();

        $user = User::first();

        return view('frontend.home', compact(
            'featuredProjects',
            'skills',
            'recentPosts',
            'portfolioStats',
            'metaTags',
            'testimonials',
            'user'
        ));
    }

    public function portfolio(Request $request)
    {
        $query = Project::published()
            ->with(['categories', 'tags']);
        
        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by tag
        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        // Filter by search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $projects = $query->sorted()
            ->paginate(12)
            ->through(function ($project) {
                // Get optimized image URL
                $project->optimized_image = $this->getOptimizedImage($project->image, 'medium');
                return $project;
            });

        $categories = Category::projectType()->get();
        $tags = Tag::projectType()->get();

        // Get active filters
        $activeFilters = [
            'category' => $request->category,
            'tag' => $request->tag,
            'search' => $request->search,
        ];

        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags([
            'title' => 'Portfolio - ' . config('app.name'),
            'description' => 'Browse through my portfolio of projects and case studies',
        ]);

        // Generate breadcrumbs
        $breadcrumbs = $this->seoService->generateBreadcrumbs([
            ['label' => 'Portfolio', 'url' => route('portfolio')]
        ]);

        return view('frontend.portfolio', compact(
            'projects',
            'categories',
            'tags',
            'activeFilters',
            'metaTags',
            'breadcrumbs'
        ));
    }

    public function projectDetail($slug)
    {
        $project = Project::where('slug', $slug)
            ->published()
            ->with(['categories', 'tags'])
            ->firstOrFail();

        $project->incrementViews();
        
        // Get optimized images
        $project->optimized_image = $this->getOptimizedImage($project->image, 'large');
        $project->optimized_gallery = $this->getOptimizedGallery($project->gallery);
        
        $relatedProjects = Project::published()
            ->where('id', '!=', $project->id)
            ->where(function ($query) use ($project) {
                $query->whereHas('tags', function ($q) use ($project) {
                    $q->whereIn('tags.id', $project->tags->pluck('id'));
                })
                ->orWhereHas('categories', function ($q) use ($project) {
                    $q->whereIn('categories.id', $project->categories->pluck('id'));
                });
            })
            ->take(4)
            ->get()
            ->map(function ($relatedProject) {
                $relatedProject->optimized_image = $this->getOptimizedImage($relatedProject->image, 'thumbnail');
                return $relatedProject;
            });

        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags([
            'title' => $project->title . ' - ' . config('app.name'),
            'description' => $project->excerpt,
            'og_image' => $project->optimized_image,
            'keywords' => implode(', ', $project->technologies ?? []) . ', ' . $project->client,
        ]);

        // Generate breadcrumbs
        $breadcrumbs = $this->seoService->generateBreadcrumbs([
            ['label' => 'Portfolio', 'url' => route('portfolio')],
            ['label' => $project->title, 'url' => route('portfolio.detail', $project->slug)]
        ]);

        return view('frontend.project-detail', compact(
            'project',
            'relatedProjects',
            'metaTags',
            'breadcrumbs'
        ));
    }

    public function blog(Request $request)
    {
        $query = Post::published()
            ->with(['categories', 'tags']);
        
        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by tag
        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        // Filter by search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $posts = $query->latest('published_at')
            ->paginate(10)
            ->through(function ($post) {
                // Get optimized featured image URL
                $post->optimized_image = $this->getOptimizedImage($post->featured_image, 'medium');
                return $post;
            });

        $categories = Category::postType()->withCount('posts')->get();
        $tags = Tag::postType()->popular(10)->get();
        $recentPosts = Post::published()->recent(5)->get();

        // Get active filters
        $activeFilters = [
            'category' => $request->category,
            'tag' => $request->tag,
            'search' => $request->search,
        ];

        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags([
            'title' => 'Blog - ' . config('app.name'),
            'description' => 'Read articles, tutorials, and insights about web development and technology',
        ]);

        // Generate breadcrumbs
        $breadcrumbs = $this->seoService->generateBreadcrumbs([
            ['label' => 'Blog', 'url' => route('blog')]
        ]);

        return view('frontend.blog', compact(
            'posts',
            'categories',
            'tags',
            'recentPosts',
            'activeFilters',
            'metaTags',
            'breadcrumbs'
        ));
    }

    public function postDetail($slug)
    {
        $post = Post::where('slug', $slug)
            ->published()
            ->with(['categories', 'tags'])
            ->firstOrFail();

        $post->incrementViews();
        
        // Get optimized featured image
        $post->optimized_image = $this->getOptimizedImage($post->featured_image, 'large');
        
        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->whereHas('categories', function ($query) use ($post) {
                $query->whereIn('categories.id', $post->categories->pluck('id'));
            })
            ->take(3)
            ->get()
            ->map(function ($relatedPost) {
                $relatedPost->optimized_image = $this->getOptimizedImage($relatedPost->featured_image, 'thumbnail');
                return $relatedPost;
            });

        // Get previous and next posts
        $previousPost = Post::published()
            ->where('id', '<', $post->id)
            ->orderBy('id', 'desc')
            ->first();

        $nextPost = Post::published()
            ->where('id', '>', $post->id)
            ->orderBy('id', 'asc')
            ->first();

        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags([
            'title' => $post->meta_title ?? $post->title,
            'description' => $post->meta_description ?? $post->excerpt,
            'og_image' => $post->optimized_image,
            'keywords' => $post->meta_keywords ?? implode(', ', $post->tags->pluck('name')->toArray()),
        ]);

        // Generate breadcrumbs
        $breadcrumbs = $this->seoService->generateBreadcrumbs([
            ['label' => 'Blog', 'url' => route('blog')],
            ['label' => $post->title, 'url' => route('blog.detail', $post->slug)]
        ]);

        // Get reading time
        $readingTime = $post->read_time;

        // Get estimated reading time from content
        $wordCount = str_word_count(strip_tags($post->content));
        $readingTimeMinutes = ceil($wordCount / 200);

        return view('frontend.post-detail', compact(
            'post',
            'relatedPosts',
            'previousPost',
            'nextPost',
            'metaTags',
            'breadcrumbs',
            'readingTime',
            'readingTimeMinutes'
        ));
    }

    public function contact()
    {
        $user = User::first();
        
        // Get contact settings
        $contactSettings = Setting::contact()->pluck('value', 'key')->toArray();
        
        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags([
            'title' => 'Contact - ' . config('app.name'),
            'description' => 'Get in touch with me for project inquiries, collaborations, or just to say hello',
        ]);

        // Generate breadcrumbs
        $breadcrumbs = $this->seoService->generateBreadcrumbs([
            ['label' => 'Contact', 'url' => route('contact')]
        ]);

        return view('frontend.contact', compact(
            'user',
            'contactSettings',
            'metaTags',
            'breadcrumbs'
        ));
    }

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
            'g-recaptcha-response' => 'required|captcha',
        ]);

        $message = Message::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'user_agent' => [
                'browser' => $request->header('User-Agent'),
                'platform' => $this->getPlatform($request->header('User-Agent')),
            ],
        ]);

        // Get admin email from settings
        $adminEmail = Setting::getValue('contact_email', config('mail.from.address'));
        
        // Send notification email to admin
        if ($adminEmail) {
            Mail::to($adminEmail)->send(new ContactMessage($message));
        }

        // Send auto-reply to sender if enabled
        $autoReplyEnabled = Setting::getValue('contact_auto_reply', true);
        if ($autoReplyEnabled) {
            Mail::to($validated['email'])->send(new ContactAutoReply($message));
        }

        // Clear cache since we have new message
        Cache::forget('portfolio_stats');
        Cache::forget('recent_activity');

        return redirect()->back()->with('success', 'Your message has been sent successfully! I\'ll get back to you soon.');
    }

    public function sitemap()
    {
        $sitemapContent = $this->portfolioService->generateSitemap();
        
        return response($sitemapContent)
            ->header('Content-Type', 'text/xml');
    }

    public function rssFeed()
    {
        $user = User::first();
        $posts = Post::published()
            ->with(['categories'])
            ->recent(20)
            ->get();

        $feed = '<?xml version="1.0" encoding="UTF-8"?>';
        $feed .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
        $feed .= '<channel>';
        $feed .= '<title>' . htmlspecialchars(config('app.name')) . '</title>';
        $feed .= '<link>' . route('home') . '</link>';
        $feed .= '<description>' . htmlspecialchars(config('portfolio.site.description')) . '</description>';
        $feed .= '<language>en-us</language>';
        $feed .= '<atom:link href="' . route('rss.feed') . '" rel="self" type="application/rss+xml" />';
        
        foreach ($posts as $post) {
            $feed .= '<item>';
            $feed .= '<title>' . htmlspecialchars($post->title) . '</title>';
            $feed .= '<link>' . route('blog.detail', $post->slug) . '</link>';
            $feed .= '<guid>' . route('blog.detail', $post->slug) . '</guid>';
            $feed .= '<description>' . htmlspecialchars($post->excerpt) . '</description>';
            $feed .= '<pubDate>' . $post->published_at->format('r') . '</pubDate>';
            $feed .= '<author>' . htmlspecialchars($user->email) . ' (' . htmlspecialchars($user->name) . ')</author>';
            
            // Add categories
            foreach ($post->categories as $category) {
                $feed .= '<category>' . htmlspecialchars($category->name) . '</category>';
            }
            
            $feed .= '</item>';
        }
        
        $feed .= '</channel>';
        $feed .= '</rss>';

        return response($feed)->header('Content-Type', 'application/rss+xml');
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $searchTerm = $request->q;
        
        // Search projects
        $projects = Project::published()
            ->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('excerpt', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('client', 'like', '%' . $searchTerm . '%');
            })
            ->with(['categories', 'tags'])
            ->paginate(10, ['*'], 'projects_page');

        // Search posts
        $posts = Post::published()
            ->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('excerpt', 'like', '%' . $searchTerm . '%')
                  ->orWhere('content', 'like', '%' . $searchTerm . '%');
            })
            ->with(['categories', 'tags'])
            ->paginate(10, ['*'], 'posts_page');

        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags([
            'title' => 'Search: ' . $searchTerm . ' - ' . config('app.name'),
            'description' => 'Search results for "' . $searchTerm . '"',
        ]);

        // Generate breadcrumbs
        $breadcrumbs = $this->seoService->generateBreadcrumbs([
            ['label' => 'Search: ' . $searchTerm, 'url' => null]
        ]);

        return view('frontend.search', compact(
            'searchTerm',
            'projects',
            'posts',
            'metaTags',
            'breadcrumbs'
        ));
    }

    /**
     * Helper Methods
     */

    private function getOptimizedImage(?string $imagePath, string $size = 'medium'): ?string
    {
        if (!$imagePath) {
            return null;
        }

        // Check if image variants exist
        $pathInfo = pathinfo($imagePath);
        $variantPath = $pathInfo['dirname'] . '/' . $size . '_' . $pathInfo['basename'];
        
        if (Storage::disk('public')->exists($variantPath)) {
            return Storage::url($variantPath);
        }

        // Fallback to original image
        return Storage::url($imagePath);
    }

    private function getOptimizedGallery(?array $gallery): array
    {
        if (!$gallery) {
            return [];
        }

        $optimizedGallery = [];
        foreach ($gallery as $imagePath) {
            $optimizedGallery[] = [
                'original' => Storage::url($imagePath),
                'thumbnail' => $this->getOptimizedImage($imagePath, 'thumbnail'),
                'medium' => $this->getOptimizedImage($imagePath, 'medium'),
            ];
        }

        return $optimizedGallery;
    }

    private function getPlatform($userAgent): string
    {
        $platforms = [
            'Windows' => 'Windows',
            'Macintosh' => 'Mac',
            'Linux' => 'Linux',
            'Android' => 'Android',
            'iPhone' => 'iOS',
            'iPad' => 'iOS',
        ];

        foreach ($platforms as $key => $value) {
            if (stripos($userAgent, $key) !== false) {
                return $value;
            }
        }

        return 'Unknown';
    }

    private function getTestimonials(): array
    {
        // This could come from a Testimonials model or settings
        return [
            [
                'name' => 'John Smith',
                'position' => 'CEO, TechCorp',
                'content' => 'Excellent work on our project. The attention to detail and communication were outstanding.',
                'rating' => 5,
            ],
            [
                'name' => 'Sarah Johnson',
                'position' => 'Product Manager, StartupCo',
                'content' => 'Professional, reliable, and delivered beyond expectations. Highly recommended!',
                'rating' => 5,
            ],
        ];
    }
}