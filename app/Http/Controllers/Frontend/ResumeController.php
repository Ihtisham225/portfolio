<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\Education;
use App\Models\Certification;
use App\Models\Project;
use App\Services\SeoService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ResumeController extends Controller
{
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    public function index()
    {
        // Get user info
        $user = User::first();

        // Get all skills (categorized)
        $skillsGrouped = Cache::remember('resume_skills_grouped', 3600, function () {
            return Skill::sorted()
                ->get()
                ->groupBy(function ($skill) {
                    $name = strtolower($skill->name);

                    if (Str::contains($name, ['laravel', 'php', 'symfony'])) {
                        return 'Backend';
                    } elseif (Str::contains($name, ['vue', 'react', 'javascript', 'typescript'])) {
                        return 'Frontend';
                    } elseif (Str::contains($name, ['mysql', 'postgresql', 'mongodb', 'redis'])) {
                        return 'Database';
                    } elseif (Str::contains($name, ['aws', 'docker', 'nginx', 'ubuntu'])) {
                        return 'DevOps';
                    }

                    return 'Other';
                });
        });

        $skillsFlat = $skillsGrouped->flatten();

        // Get experiences
        $experiences = Cache::remember('resume_experiences', 3600, function () {
            return Experience::sorted()
                ->get();
        });

        // Get education
        $educations = Cache::remember('resume_educations', 3600, function () {
            return Education::sorted()
                ->get();
        });

        // Get certifications
        $certifications = Cache::remember('resume_certifications', 3600, function () {
            return Certification::sorted()
                ->get();
        });

        // Get featured projects for resume
        $featuredProjects = Cache::remember('resume_projects', 3600, function () {
            return Project::published()
                ->featured()
                ->limit(6)
                ->get();
        });

        // Calculate total experience in years
        $oldestExperience = Experience::oldest('start_date')->first();
        $totalExperience = $oldestExperience 
            ? now()->diffInYears($oldestExperience->start_date) . '+ Years'
            : '5+ Years';

        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags([
            'title' => 'Resume - ' . config('app.name'),
            'description' => 'Download my professional resume and view my complete work history, education, and skills.',
            'og_image' => $user->avatar_url ?? asset('images/og-default.jpg'),
            'keywords' => 'resume, CV, work experience, education, skills, download CV, professional resume',
        ]);

        // Generate breadcrumbs
        $breadcrumbs = $this->seoService->generateBreadcrumbs([
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Resume', 'url' => null]
        ]);

        // Generate structured data for resume
        $structuredData = $this->seoService->generateResumeSchema(
            $user,
            $experiences,
            $educations,
            $skillsGrouped,
            $certifications
        );

        return view('frontend.pages.resume', compact(
            'user',
            'skillsGrouped',
            'skillsFlat',
            'experiences',
            'educations',
            'certifications',
            'featuredProjects',
            'totalExperience',
            'metaTags',
            'structuredData',
            'breadcrumbs'
        ));
    }

    public function download()
    {
        $user = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();

        // Check if CV file exists
        $cvPath = $user->cv_path ?? null;
        
        if (!$cvPath || !file_exists(storage_path('app/public/' . $cvPath))) {
            return redirect()->back()->with('error', 'CV file not available for download.');
        }

        $filename = $user->name . '_CV_' . date('Y') . '.pdf';
        
        return response()->download(
            storage_path('app/public/' . $cvPath),
            $filename
        );
    }

    public function print()
    {
        $user = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();

        $skills = Skill::sorted()->get();
        $experiences = Experience::sorted()->get();
        $educations = Education::sorted()->get();
        $certifications = Certification::sorted()->get();

        return view('frontend.pages.resume-print', compact(
            'user',
            'skills',
            'experiences',
            'educations',
            'certifications'
        ));
    }
}