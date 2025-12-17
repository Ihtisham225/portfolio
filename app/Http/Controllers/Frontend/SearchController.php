<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Post;
use App\Models\Page;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $searchTerm = $request->q;
        $searchType = $request->get('type', 'all');
        
        $results = [
            'projects' => collect(),
            'posts' => collect(),
            'pages' => collect(),
        ];

        // Search projects
        if ($searchType === 'all' || $searchType === 'projects') {
            $results['projects'] = Project::published()
                ->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', '%' . $searchTerm . '%')
                      ->orWhere('excerpt', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%')
                      ->orWhere('client', 'like', '%' . $searchTerm . '%')
                      ->orWhere('technologies', 'like', '%' . $searchTerm . '%');
                })
                ->with(['categories', 'tags'])
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'projects_page');
        }

        // Search posts
        if ($searchType === 'all' || $searchType === 'posts') {
            $results['posts'] = Post::published()
                ->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', '%' . $searchTerm . '%')
                      ->orWhere('excerpt', 'like', '%' . $searchTerm . '%')
                      ->orWhere('content', 'like', '%' . $searchTerm . '%');
                })
                ->with(['categories', 'tags'])
                ->orderBy('published_at', 'desc')
                ->paginate(10, ['*'], 'posts_page');
        }

        // Search pages
        if ($searchType === 'all' || $searchType === 'pages') {
            $results['pages'] = Page::published()
                ->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', '%' . $searchTerm . '%')
                      ->orWhere('content', 'like', '%' . $searchTerm . '%');
                })
                ->orderBy('title')
                ->paginate(10, ['*'], 'pages_page');
        }

        // Get total counts
        $totalCounts = [
            'projects' => $results['projects']->total(),
            'posts' => $results['posts']->total(),
            'pages' => $results['pages']->total(),
            'all' => $results['projects']->total() + $results['posts']->total() + $results['pages']->total(),
        ];

        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags([
            'title' => 'Search: "' . $searchTerm . '" - ' . config('app.name'),
            'description' => 'Search results for "' . $searchTerm . '"',
            'robots' => 'noindex,follow',
        ]);

        // Generate breadcrumbs
        $breadcrumbs = $this->seoService->generateBreadcrumbs([
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Search: "' . $searchTerm . '"', 'url' => null]
        ]);

        return view('frontend.pages.search', compact(
            'searchTerm',
            'searchType',
            'results',
            'totalCounts',
            'metaTags',
            'breadcrumbs'
        ));
    }

    public function liveSearch(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:50',
        ]);

        $searchTerm = $request->q;
        $limit = $request->get('limit', 5);

        // Search projects
        $projects = Project::published()
            ->where('title', 'like', '%' . $searchTerm . '%')
            ->orWhere('excerpt', 'like', '%' . $searchTerm . '%')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'excerpt', 'image'])
            ->map(function ($project) {
                return [
                    'type' => 'project',
                    'title' => $project->title,
                    'excerpt' => str_limit($project->excerpt, 100),
                    'url' => route('project.detail', $project->slug),
                    'image' => $project->image_url,
                ];
            });

        // Search posts
        $posts = Post::published()
            ->where('title', 'like', '%' . $searchTerm . '%')
            ->orWhere('excerpt', 'like', '%' . $searchTerm . '%')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'excerpt', 'featured_image'])
            ->map(function ($post) {
                return [
                    'type' => 'post',
                    'title' => $post->title,
                    'excerpt' => str_limit($post->excerpt, 100),
                    'url' => route('post.detail', $post->slug),
                    'image' => $post->featured_image_url,
                ];
            });

        // Combine results
        $results = $projects->merge($posts)->take($limit);

        return response()->json([
            'results' => $results,
            'count' => $results->count(),
            'searchTerm' => $searchTerm,
        ]);
    }
}