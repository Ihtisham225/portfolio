<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Category;
use App\Models\Tag;
use App\Services\SeoService;
use App\Services\PortfolioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PortfolioController extends Controller
{
    protected $seoService;
    protected $portfolioService;

    public function __construct(
        SeoService $seoService,
        PortfolioService $portfolioService
    ) {
        $this->seoService = $seoService;
        $this->portfolioService = $portfolioService;
    }

    public function index(Request $request)
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
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('client', 'like', '%' . $request->search . '%')
                  ->orWhere('technologies', 'like', '%' . $request->search . '%');
            });
        }

        // Get sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name':
                $query->orderBy('title', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'featured':
                $query->featured()->orderBy('sort_order');
                break;
            case 'popular':
                $query->orderBy('views', 'desc');
                break;
            default: // latest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $projects = $query->paginate(12);

        // Get categories and tags for filter sidebar
        $categories = Cache::remember('portfolio_categories', 3600, function () {
            return Category::projectType()
                ->withCount(['projects' => function ($query) {
                    $query->published();
                }])
                ->having('projects_count', '>', 0)
                ->get();
        });

        $tags = Cache::remember('portfolio_tags', 3600, function () {
            return Tag::projectType()
                ->withCount(['projects' => function ($query) {
                    $query->published();
                }])
                ->having('projects_count', '>', 0)
                ->orderBy('projects_count', 'desc')
                ->limit(20)
                ->get();
        });

        // Get featured projects for showcase
        $featuredProjects = Cache::remember('featured_projects', 3600, function () {
            return Project::published()
                ->featured()
                ->limit(3)
                ->get();
        });

        // Get active filters
        $activeFilters = [
            'category' => $request->category,
            'tag' => $request->tag,
            'search' => $request->search,
            'sort' => $sort,
        ];

        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags([
            'title' => 'Portfolio - ' . config('app.name'),
            'description' => 'Browse through my collection of projects showcasing my development skills and expertise.',
            'og_image' => $featuredProjects->first()->image_url ?? asset('images/og-default.jpg'),
            'keywords' => 'portfolio, projects, case studies, web development, coding, programming',
        ]);

        // Generate breadcrumbs
        $breadcrumbs = $this->seoService->generateBreadcrumbs([
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Portfolio', 'url' => null]
        ]);

        return view('frontend.pages.portfolio', compact(
            'projects',
            'categories',
            'tags',
            'featuredProjects',
            'activeFilters',
            'metaTags',
            'breadcrumbs'
        ));
    }

    public function show($slug)
    {
        $project = Project::where('slug', $slug)
            ->published()
            ->with(['categories', 'tags'])
            ->firstOrFail();

        // Increment views
        $project->incrementViews();

        // Get related projects
        $relatedProjects = Cache::remember("project_related_{$project->id}", 3600, function () use ($project) {
            return Project::published()
                ->where('id', '!=', $project->id)
                ->where(function ($query) use ($project) {
                    $query->whereHas('categories', function ($q) use ($project) {
                        $q->whereIn('categories.id', $project->categories->pluck('id'));
                    })
                    ->orWhereHas('tags', function ($q) use ($project) {
                        $q->whereIn('tags.id', $project->tags->pluck('id'));
                    });
                })
                ->limit(4)
                ->get();
        });

        // Get next and previous projects
        $nextProject = Project::published()
            ->where('id', '>', $project->id)
            ->orderBy('id', 'asc')
            ->first();

        $previousProject = Project::published()
            ->where('id', '<', $project->id)
            ->orderBy('id', 'desc')
            ->first();

        // Get all project categories for navigation
        $allCategories = Cache::remember('all_portfolio_categories', 3600, function () {
            return Category::projectType()
                ->withCount(['projects' => function ($query) {
                    $query->published();
                }])
                ->having('projects_count', '>', 0)
                ->get();
        });

        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags([
            'title' => $project->title . ' - ' . config('app.name'),
            'description' => $project->excerpt,
            'og_image' => $project->image_url,
            'og_type' => 'article',
            'keywords' => implode(', ', $project->technologies_array->toArray()) . ', ' . $project->client,
        ]);

        // Generate structured data
        $structuredData = $this->seoService->generateProjectSchema($project);

        // Generate breadcrumbs
        $breadcrumbs = $this->seoService->generateBreadcrumbs([
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Portfolio', 'url' => route('portfolio')],
            ['label' => $project->title, 'url' => null]
        ]);

        return view('frontend.pages.portfolio-detail', compact(
            'project',
            'relatedProjects',
            'nextProject',
            'previousProject',
            'allCategories',
            'metaTags',
            'structuredData',
            'breadcrumbs'
        ));
    }
}