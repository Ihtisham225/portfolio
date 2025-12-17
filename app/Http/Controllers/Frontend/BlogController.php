<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    public function index(Request $request)
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

        // Get sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('published_at', 'asc');
                break;
            case 'name':
                $query->orderBy('title', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'popular':
                $query->orderBy('views', 'desc');
                break;
            case 'featured':
                $query->featured()->orderBy('published_at', 'desc');
                break;
            default: // latest
                $query->orderBy('published_at', 'desc');
                break;
        }

        $posts = $query->paginate(10);

        // Get sidebar data
        $sidebarData = $this->getSidebarData();

        // Get active filters
        $activeFilters = [
            'category' => $request->category,
            'tag' => $request->tag,
            'search' => $request->search,
            'sort' => $sort,
        ];

        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags([
            'title' => 'Blog - ' . config('app.name'),
            'description' => 'Read articles, tutorials, and insights about web development, technology, and programming.',
            'og_image' => asset('images/og-default.jpg'),
            'keywords' => 'blog, articles, tutorials, web development, programming, technology',
        ]);

        // Generate breadcrumbs
        $breadcrumbs = $this->seoService->generateBreadcrumbs([
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Blog', 'url' => null]
        ]);

        return view('frontend.pages.blog', array_merge(
            [
                'user' => User::first(),
                'posts' => $posts,
                'activeFilters' => $activeFilters,
                'metaTags' => $metaTags,
                'breadcrumbs' => $breadcrumbs,
            ],
            $sidebarData
        ));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->published()
            ->with(['categories', 'tags'])
            ->firstOrFail();

        // Increment views
        $post->incrementViews();

        // Get author info
        $author = User::first();

        // Get related posts
        $relatedPosts = Cache::remember("post_related_{$post->id}", 3600, function () use ($post) {
            return Post::published()
                ->where('id', '!=', $post->id)
                ->whereHas('categories', function ($q) use ($post) {
                    $q->whereIn('categories.id', $post->categories->pluck('id'));
                })
                ->limit(3)
                ->get();
        });

        // Get next and previous posts
        $nextPost = Post::published()
            ->where('id', '>', $post->id)
            ->orderBy('id', 'asc')
            ->first();

        $previousPost = Post::published()
            ->where('id', '<', $post->id)
            ->orderBy('id', 'desc')
            ->first();

        // Get sidebar data
        $sidebarData = $this->getSidebarData();

        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags([
            'title' => $post->meta_title ?? $post->title,
            'description' => $post->meta_description ?? $post->excerpt,
            'og_image' => $post->featured_image_url,
            'og_type' => 'article',
            'keywords' => $post->meta_keywords ?? implode(', ', $post->tags->pluck('name')->toArray()),
        ]);

        // Generate structured data
        $structuredData = $this->seoService->generateArticleSchema($post, $author);

        // Generate breadcrumbs
        $breadcrumbs = $this->seoService->generateBreadcrumbs([
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Blog', 'url' => route('blog')],
            ['label' => $post->title, 'url' => null]
        ]);

        return view('frontend.pages.blog-detail', array_merge(
            [
                'user' => User::first(),
                'post' => $post,
                'author' => $author,
                'relatedPosts' => $relatedPosts,
                'nextPost' => $nextPost,
                'previousPost' => $previousPost,
                'metaTags' => $metaTags,
                'structuredData' => $structuredData,
                'breadcrumbs' => $breadcrumbs,
            ],
            $sidebarData
        ));
    }

    private function getSidebarData()
    {
        return Cache::remember('blog_sidebar_data', 3600, function () {
            // Get categories with post counts
            $categories = Category::postType()
                ->withCount(['posts' => function ($query) {
                    $query->published();
                }])
                ->having('posts_count', '>', 0)
                ->orderBy('posts_count', 'desc')
                ->limit(10)
                ->get();

            // Get popular tags
            $tags = Tag::postType()
                ->withCount(['posts' => function ($query) {
                    $query->published();
                }])
                ->having('posts_count', '>', 0)
                ->orderBy('posts_count', 'desc')
                ->limit(20)
                ->get();

            // Get popular posts
            $popularPosts = Post::published()
                ->orderBy('views', 'desc')
                ->limit(5)
                ->get();

            // Get recent posts
            $recentPosts = Post::published()
                ->orderBy('published_at', 'desc')
                ->limit(5)
                ->get();

            // Get all categories for sidebar
            $allCategories = Category::postType()
                ->withCount(['posts' => function ($query) {
                    $query->published();
                }])
                ->having('posts_count', '>', 0)
                ->get();

            return [
                'categories' => $categories,
                'tags' => $tags,
                'popularPosts' => $popularPosts,
                'recentPosts' => $recentPosts,
                'allCategories' => $allCategories,
            ];
        });
    }

    public function category($slug)
    {
        $category = Category::postType()
            ->where('slug', $slug)
            ->firstOrFail();

        $posts = Post::published()
            ->whereHas('categories', function ($q) use ($category) {
                $q->where('categories.id', $category->id);
            })
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        // Get sidebar data
        $sidebarData = $this->getSidebarData();

        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags([
            'title' => $category->name . ' - Blog Category - ' . config('app.name'),
            'description' => $category->description ?? 'Browse all posts in the ' . $category->name . ' category.',
            'keywords' => $category->name . ', blog category, articles, posts',
        ]);

        // Generate breadcrumbs
        $breadcrumbs = $this->seoService->generateBreadcrumbs([
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Blog', 'url' => route('blog')],
            ['label' => $category->name, 'url' => null]
        ]);

        return view('frontend.pages.blog-category', array_merge(
            [
                'category' => $category,
                'posts' => $posts,
                'metaTags' => $metaTags,
                'breadcrumbs' => $breadcrumbs,
            ],
            $sidebarData
        ));
    }

    public function tag($slug)
    {
        $tag = Tag::postType()
            ->where('slug', $slug)
            ->firstOrFail();

        $posts = Post::published()
            ->whereHas('tags', function ($q) use ($tag) {
                $q->where('tags.id', $tag->id);
            })
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        // Get sidebar data
        $sidebarData = $this->getSidebarData();

        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags([
            'title' => '#' . $tag->name . ' - Blog Tag - ' . config('app.name'),
            'description' => 'Browse all posts tagged with ' . $tag->name,
            'keywords' => $tag->name . ', blog tag, articles, posts',
        ]);

        // Generate breadcrumbs
        $breadcrumbs = $this->seoService->generateBreadcrumbs([
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Blog', 'url' => route('blog')],
            ['label' => '#' . $tag->name, 'url' => null]
        ]);

        return view('frontend.pages.blog-tag', array_merge(
            [
                'tag' => $tag,
                'posts' => $posts,
                'metaTags' => $metaTags,
                'breadcrumbs' => $breadcrumbs,
            ],
            $sidebarData
        ));
    }
}