<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Post;
use App\Models\User;
use App\Models\Experience;
use App\Models\Education;
use App\Models\Skill;
use App\Models\Certification;
use Carbon\Carbon;
use App\Models\Setting;

class SeoService
{
    public function generateMetaTags(array $data = []): array
    {
        $siteName = config('app.name');
        $siteDescription = Setting::getValue('site_description', config('portfolio.site.description'));
        $siteKeywords = Setting::getValue('site_keywords', config('portfolio.site.keywords'));
        
        $defaults = [
            'title' => $siteName,
            'description' => $siteDescription,
            'keywords' => $siteKeywords,
            'og_title' => null,
            'og_description' => null,
            'og_image' => asset('images/og-default.jpg'),
            'og_type' => 'website',
            'og_url' => url()->current(),
            'og_site_name' => $siteName,
            'twitter_card' => 'summary_large_image',
            'twitter_site' => Setting::getValue('twitter_handle'),
            'twitter_creator' => Setting::getValue('twitter_handle'),
            'canonical' => url()->current(),
            'robots' => 'index, follow',
            'author' => Setting::getValue('site_author', config('portfolio.site.author')),
            'viewport' => 'width=device-width, initial-scale=1.0',
            'charset' => 'utf-8',
            'language' => 'en',
            'theme_color' => Setting::getValue('theme_color', '#3B82F6'),
        ];

        $merged = array_merge($defaults, $data);
        
        // Ensure OG title and description fall back to regular title/description
        if (empty($merged['og_title'])) {
            $merged['og_title'] = $merged['title'];
        }
        
        if (empty($merged['og_description'])) {
            $merged['og_description'] = $merged['description'];
        }
        
        // Generate structured data
        $merged['structured_data'] = $this->generateStructuredData($merged);
        
        return $merged;
    }
    
    public function generateBreadcrumbs(array $items): array
    {
        $breadcrumbs = [
            [
                'label' => 'Home',
                'url' => route('home'),
                'position' => 1,
            ]
        ];
        
        $position = 2;
        foreach ($items as $item) {
            $breadcrumbs[] = [
                'label' => $item['label'],
                'url' => $item['url'] ?? null,
                'position' => $position,
            ];
            $position++;
        }
        
        return $breadcrumbs;
    }
    
    public function generateStructuredData(array $data = []): array
    {
        $siteUrl = config('app.url');
        $siteName = config('app.name');
        
        // Website structured data
        $websiteData = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => $siteUrl,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => $siteUrl . '/search?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
        
        // Organization structured data
        $organizationData = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => $siteUrl,
            'logo' => asset('images/logo.png'),
            'sameAs' => $this->getSocialUrls(),
        ];
        
        // Breadcrumb structured data
        if (isset($data['breadcrumbs'])) {
            $breadcrumbData = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [],
            ];
            
            foreach ($data['breadcrumbs'] as $index => $breadcrumb) {
                $breadcrumbData['itemListElement'][] = [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $breadcrumb['label'],
                    'item' => $breadcrumb['url'] ?? $siteUrl,
                ];
            }
        }
        
        // Combine all structured data
        $structuredData = [
            'website' => $websiteData,
            'organization' => $organizationData,
        ];
        
        if (isset($breadcrumbData)) {
            $structuredData['breadcrumb'] = $breadcrumbData;
        }
        
        // Add article data for blog posts
        if (isset($data['type']) && $data['type'] === 'article') {
            $structuredData['article'] = $this->generateArticleData($data);
        }
        
        // Add project data for portfolio items
        if (isset($data['type']) && $data['type'] === 'project') {
            $structuredData['project'] = $this->generateProjectData($data);
        }
        
        return $structuredData;
    }
    
    public function generateArticleData(array $data): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'image' => $data['og_image'] ?? '',
            'datePublished' => $data['published_date'] ?? now()->toIso8601String(),
            'dateModified' => $data['modified_date'] ?? now()->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $data['author_name'] ?? '',
                'url' => $data['author_url'] ?? '',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png'),
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $data['canonical'] ?? url()->current(),
            ],
        ];
    }
    
    public function generateProjectData(array $data): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'image' => $data['og_image'] ?? '',
            'dateCreated' => $data['created_date'] ?? now()->toIso8601String(),
            'dateModified' => $data['modified_date'] ?? now()->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $data['author_name'] ?? '',
                'url' => $data['author_url'] ?? '',
            ],
            'about' => [
                '@type' => 'Thing',
                'name' => $data['technologies'] ?? 'Web Development',
            ],
            'url' => $data['project_url'] ?? '',
            'isBasedOn' => $data['github_url'] ?? '',
        ];
    }
    
    public function getSocialUrls(): array
    {
        $socialUrls = [];
        
        $socialPlatforms = [
            'facebook' => Setting::getValue('facebook_url'),
            'twitter' => Setting::getValue('twitter_url'),
            'linkedin' => Setting::getValue('linkedin_url'),
            'github' => Setting::getValue('github_url'),
            'instagram' => Setting::getValue('instagram_url'),
            'youtube' => Setting::getValue('youtube_url'),
        ];
        
        foreach ($socialPlatforms as $platform => $url) {
            if (!empty($url)) {
                $socialUrls[] = $url;
            }
        }
        
        return $socialUrls;
    }
    
    public function renderMetaTags(array $metaTags): string
    {
        $html = '';
        
        // Basic meta tags
        $basicTags = [
            'title' => '<title>' . e($metaTags['title']) . '</title>',
            'description' => '<meta name="description" content="' . e($metaTags['description']) . '">',
            'keywords' => '<meta name="keywords" content="' . e($metaTags['keywords']) . '">',
            'robots' => '<meta name="robots" content="' . e($metaTags['robots']) . '">',
            'author' => '<meta name="author" content="' . e($metaTags['author']) . '">',
            'viewport' => '<meta name="viewport" content="' . e($metaTags['viewport']) . '">',
            'charset' => '<meta charset="' . e($metaTags['charset']) . '">',
            'theme-color' => '<meta name="theme-color" content="' . e($metaTags['theme_color']) . '">',
        ];
        
        foreach ($basicTags as $tag) {
            $html .= $tag . "\n";
        }
        
        // Open Graph tags
        $ogTags = [
            'og:title' => $metaTags['og_title'],
            'og:description' => $metaTags['og_description'],
            'og:image' => $metaTags['og_image'],
            'og:type' => $metaTags['og_type'],
            'og:url' => $metaTags['og_url'],
            'og:site_name' => $metaTags['og_site_name'],
        ];
        
        foreach ($ogTags as $property => $content) {
            $html .= '<meta property="' . $property . '" content="' . e($content) . '">' . "\n";
        }
        
        // Twitter Card tags
        $twitterTags = [
            'twitter:card' => $metaTags['twitter_card'],
            'twitter:site' => $metaTags['twitter_site'],
            'twitter:creator' => $metaTags['twitter_creator'],
            'twitter:title' => $metaTags['title'],
            'twitter:description' => $metaTags['description'],
            'twitter:image' => $metaTags['og_image'],
        ];
        
        foreach ($twitterTags as $name => $content) {
            if (!empty($content)) {
                $html .= '<meta name="' . $name . '" content="' . e($content) . '">' . "\n";
            }
        }
        
        // Canonical URL
        $html .= '<link rel="canonical" href="' . e($metaTags['canonical']) . '">' . "\n";
        
        // Structured data
        if (!empty($metaTags['structured_data'])) {
            foreach ($metaTags['structured_data'] as $data) {
                $html .= '<script type="application/ld+json">' . 
                         json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . 
                         '</script>' . "\n";
            }
        }
        
        return $html;
    }
    
    public function generateSitemapIndex(array $sitemaps): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        foreach ($sitemaps as $sitemap) {
            $xml .= '<sitemap>';
            $xml .= '<loc>' . htmlspecialchars($sitemap['loc']) . '</loc>';
            $xml .= '<lastmod>' . htmlspecialchars($sitemap['lastmod'] ?? now()->toIso8601String()) . '</lastmod>';
            $xml .= '</sitemap>';
        }
        
        $xml .= '</sitemapindex>';
        
        return $xml;
    }

    /**
     * Generate Project schema (CreativeWork/SoftwareApplication)
     */
    public function generateProjectSchema(Project $project)
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $project->title,
            'description' => $project->excerpt,
            'applicationCategory' => 'DeveloperApplication',
            'operatingSystem' => 'Web',
            'url' => route('portfolio.detail', $project->slug),
            'dateCreated' => $project->created_at->toIso8601String(),
            'datePublished' => $project->project_date ? $project->project_date->toIso8601String() : $project->created_at->toIso8601String(),
            'dateModified' => $project->updated_at->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => config('app.name'),
                'url' => route('home'),
            ],
            'publisher' => [
                '@type' => 'Person',
                'name' => config('app.name'),
                'url' => route('home'),
            ],
        ];

        // Add image
        if ($project->image) {
            $schema['image'] = [
                $project->image_url,
            ];
        }

        // Add gallery images
        if ($project->gallery && count($project->gallery) > 0) {
            $schema['image'] = $schema['image'] ?? [];
            foreach ($project->gallery_urls as $galleryImage) {
                $schema['image'][] = $galleryImage;
            }
        }

        // Add technologies as keywords
        if ($project->technologies_array->isNotEmpty()) {
            $schema['keywords'] = implode(', ', $project->technologies_array->toArray());
        }

        // Add client information if available
        if ($project->client) {
            $schema['contributor'] = [
                '@type' => 'Organization',
                'name' => $project->client,
            ];
        }

        // Add URLs
        $schema['urls'] = [];
        if ($project->project_url) {
            $schema['urls'][] = [
                '@type' => 'WebApplication',
                'url' => $project->project_url,
                'name' => 'Live Demo',
            ];
        }
        if ($project->github_url) {
            $schema['urls'][] = [
                '@type' => 'WebApplication',
                'url' => $project->github_url,
                'name' => 'Source Code',
            ];
        }

        // Add categories
        if ($project->categories->isNotEmpty()) {
            $schema['about'] = [];
            foreach ($project->categories as $category) {
                $schema['about'][] = [
                    '@type' => 'Thing',
                    'name' => $category->name,
                    'url' => route('portfolio', ['category' => $category->slug]),
                ];
            }
        }

        // Add features/technologies
        if ($project->technologies_array->isNotEmpty()) {
            $schema['featureList'] = $project->technologies_array->toArray();
        }

        // Add aggregate rating (if you have ratings)
        if ($project->reviews && $project->reviews->avg('rating')) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $project->reviews->avg('rating'),
                'ratingCount' => $project->reviews->count(),
                'bestRating' => 5,
                'worstRating' => 1,
            ];
        }

        // Add BreadcrumbList schema
        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => route('home'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Portfolio',
                    'item' => route('portfolio'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $project->title,
                    'item' => route('portfolio.detail', $project->slug),
                ],
            ],
        ];

        // Combine schemas
        $combinedSchema = [$schema, $breadcrumbSchema];

        // Add WebPage schema
        $webPageSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $project->title,
            'description' => $project->excerpt,
            'url' => route('portfolio.detail', $project->slug),
            'datePublished' => $project->project_date ? $project->project_date->toIso8601String() : $project->created_at->toIso8601String(),
            'dateModified' => $project->updated_at->toIso8601String(),
            'mainEntity' => $schema,
            'breadcrumb' => $breadcrumbSchema,
        ];

        $combinedSchema[] = $webPageSchema;

        return $combinedSchema;
    }

    /**
     * Generate Article/BlogPost schema
     */
    public function generateArticleSchema(Post $post, User $author = null)
    {
        // Main Article schema
        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->title,
            'description' => $post->excerpt,
            'articleBody' => strip_tags($post->content),
            'url' => route('blog.detail', $post->slug),
            'datePublished' => $post->published_at->toIso8601String(),
            'dateModified' => $post->updated_at->toIso8601String(),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('blog.detail', $post->slug),
            ],
            'wordCount' => str_word_count(strip_tags($post->content)),
            'timeRequired' => 'PT' . ceil(str_word_count(strip_tags($post->content)) / 200) . 'M',
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'url' => route('home'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png'),
                    'width' => 600,
                    'height' => 60,
                ],
            ],
        ];

        // Add author
        if ($author) {
            $articleSchema['author'] = [
                '@type' => 'Person',
                'name' => $author->name,
                'url' => route('home'),
                'description' => $author->bio ?? '',
            ];
            
            // Add author image if available
            if ($author->avatar_url) {
                $articleSchema['author']['image'] = [
                    '@type' => 'ImageObject',
                    'url' => $author->avatar_url,
                    'width' => 200,
                    'height' => 200,
                ];
            }
        } else {
            $articleSchema['author'] = [
                '@type' => 'Person',
                'name' => config('app.name'),
                'url' => route('home'),
            ];
        }

        // Add image
        if ($post->featured_image_url) {
            $articleSchema['image'] = [
                '@type' => 'ImageObject',
                'url' => $post->featured_image_url,
                'width' => 1200,
                'height' => 630,
            ];
        }

        // Add categories
        if ($post->categories->isNotEmpty()) {
            $articleSchema['articleSection'] = [];
            foreach ($post->categories as $category) {
                $articleSchema['articleSection'][] = $category->name;
            }
            if (count($articleSchema['articleSection']) === 1) {
                $articleSchema['articleSection'] = $articleSchema['articleSection'][0];
            }
        }

        // Add keywords from tags
        if ($post->tags->isNotEmpty()) {
            $articleSchema['keywords'] = implode(', ', $post->tags->pluck('name')->toArray());
        }

        // Add comment count (if you have comments)
        if (method_exists($post, 'comments') && $post->comments()->count() > 0) {
            $articleSchema['commentCount'] = $post->comments()->count();
            
            // Add comment schema
            $comments = $post->comments()->approved()->limit(5)->get();
            if ($comments->isNotEmpty()) {
                $articleSchema['comment'] = $comments->map(function ($comment) {
                    return [
                        '@type' => 'Comment',
                        'text' => $comment->content,
                        'dateCreated' => $comment->created_at->toIso8601String(),
                        'author' => [
                            '@type' => 'Person',
                            'name' => $comment->author_name,
                        ],
                    ];
                })->toArray();
            }
        }

        // Add aggregate rating (if you have ratings)
        if ($post->rating) {
            $articleSchema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $post->rating,
                'ratingCount' => $post->rating_count,
                'bestRating' => 5,
                'worstRating' => 1,
            ];
        }

        // Add BreadcrumbList schema
        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => route('home'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Blog',
                    'item' => route('blog'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $post->title,
                    'item' => route('blog.detail', $post->slug),
                ],
            ],
        ];

        // Combine schemas
        $combinedSchema = [$articleSchema, $breadcrumbSchema];

        // Add WebPage schema
        $webPageSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $post->title,
            'description' => $post->excerpt,
            'url' => route('blog.detail', $post->slug),
            'datePublished' => $post->published_at->toIso8601String(),
            'dateModified' => $post->updated_at->toIso8601String(),
            'mainEntity' => $articleSchema,
            'breadcrumb' => $breadcrumbSchema,
        ];

        $combinedSchema[] = $webPageSchema;

        // Add Blog schema if this is a blog post
        $blogSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Blog',
            'headline' => $post->title,
            'description' => $post->excerpt,
            'url' => route('blog.detail', $post->slug),
            'datePublished' => $post->published_at->toIso8601String(),
            'dateModified' => $post->updated_at->toIso8601String(),
            'author' => $articleSchema['author'],
            'publisher' => $articleSchema['publisher'],
        ];

        $combinedSchema[] = $blogSchema;

        return $combinedSchema;
    }

    /**
     * Generate Resume/Person schema
     */
    public function generateResumeSchema(
        User $user,
        $experiences,
        $educations,
        $skills,
        $certifications
    ) {
        $personSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $user->name,
            'url' => route('home'),
            'description' => $user->bio ?? 'Professional developer portfolio',
            'jobTitle' => $user->title ?? 'Full Stack Developer',
            'email' => $user->email,
            'telephone' => $user->phone ?? '',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $user->city ?? '',
                'addressRegion' => $user->state ?? '',
                'addressCountry' => $user->country ?? '',
            ],
            'sameAs' => [],
        ];

        // Add social profiles
        $socialFields = ['github', 'linkedin', 'twitter', 'facebook', 'instagram'];
        foreach ($socialFields as $field) {
            if ($user->{$field}) {
                $personSchema['sameAs'][] = $user->{$field};
            }
        }

        // Add skills
        if ($skills) {
            $personSchema['knowsAbout'] = $skills->flatten()->pluck('name')->toArray();
        }

        // Add work experience
        if ($experiences && $experiences->isNotEmpty()) {
            $personSchema['workLocation'] = $experiences->first()->location ?? '';
            
            $worksFor = [];
            foreach ($experiences as $experience) {
                $worksFor[] = [
                    '@type' => 'Organization',
                    'name' => $experience->company,
                    'location' => $experience->location,
                    'employee' => [
                        '@type' => 'Person',
                        'name' => $user->name,
                        'jobTitle' => $experience->title,
                        'worksFor' => [
                            '@type' => 'Organization',
                            'name' => $experience->company,
                        ],
                    ],
                ];
            }
            
            if (count($worksFor) === 1) {
                $personSchema['worksFor'] = $worksFor[0];
            } else {
                $personSchema['worksFor'] = $worksFor;
            }
        }

        // Add education
        if ($educations && $educations->isNotEmpty()) {
            $personSchema['alumniOf'] = [];
            foreach ($educations as $education) {
                $personSchema['alumniOf'][] = [
                    '@type' => 'EducationalOrganization',
                    'name' => $education->institution,
                    'location' => $education->location,
                    'description' => $education->degree,
                ];
            }
        }

        // Add certifications
        if ($certifications && $certifications->isNotEmpty()) {
            $personSchema['hasCredential'] = [];
            foreach ($certifications as $certification) {
                $personSchema['hasCredential'][] = [
                    '@type' => 'EducationalOccupationalCredential',
                    'name' => $certification->name,
                    'credentialCategory' => 'license',
                    'recognizedBy' => [
                        '@type' => 'Organization',
                        'name' => $certification->issuer,
                    ],
                    'validIn' => [
                        '@type' => 'Country',
                        'name' => 'Worldwide',
                    ],
                ];
            }
        }

        // Generate Breadcrumb schema for resume page
        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => route('home'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Resume',
                    'item' => route('resume'),
                ],
            ],
        ];

        // Combine schemas
        return [$personSchema, $breadcrumbSchema];
    }

    /**
     * Generate Breadcrumb schema
     */
    public function generateBreadcrumbSchema(array $items)
    {
        $itemListElement = [];
        $position = 1;

        foreach ($items as $item) {
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $item['label'],
                'item' => $item['url'] ?? null,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement,
        ];
    }

    /**
     * Generate WebSite schema
     */
    public function generateWebsiteSchema()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => config('app.name'),
            'url' => route('home'),
            'description' => config('app.description', 'Professional developer portfolio'),
            'publisher' => [
                '@type' => 'Person',
                'name' => config('app.name'),
            ],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('search') . '?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Generate Organization schema
     */
    public function generateOrganizationSchema()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('app.name'),
            'url' => route('home'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('images/logo.png'),
                'width' => 600,
                'height' => 60,
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'email' => config('mail.from.address'),
                'url' => route('contact'),
            ],
            'sameAs' => [
                // Add your social media URLs here
            ],
        ];
    }
}