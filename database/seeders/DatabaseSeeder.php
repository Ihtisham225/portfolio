<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Post;
use App\Models\Skill;
use App\Models\Experience;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create skills
        $skills = [
            ['name' => 'Laravel', 'percentage' => 95, 'color' => '#FF2D20', 'is_featured' => true],
            ['name' => 'PHP', 'percentage' => 90, 'color' => '#777BB4', 'is_featured' => true],
            ['name' => 'JavaScript', 'percentage' => 85, 'color' => '#F7DF1E', 'is_featured' => true],
            ['name' => 'Vue.js', 'percentage' => 80, 'color' => '#4FC08D', 'is_featured' => true],
            ['name' => 'MySQL', 'percentage' => 85, 'color' => '#4479A1', 'is_featured' => true],
            ['name' => 'Tailwind CSS', 'percentage' => 90, 'color' => '#06B6D4', 'is_featured' => true],
            ['name' => 'Git', 'percentage' => 88, 'color' => '#F05032', 'is_featured' => true],
            ['name' => 'Docker', 'percentage' => 75, 'color' => '#2496ED', 'is_featured' => true],
        ];

        foreach ($skills as $skill) {
            Skill::create($skill);
        }

        // Create projects
        $projects = [
            [
                'title' => 'E-commerce Platform',
                'slug' => 'ecommerce-platform',
                'excerpt' => 'A full-featured e-commerce platform built with Laravel and Vue.js',
                'description' => 'Complete e-commerce solution with product management, shopping cart, payment integration, and admin dashboard.',
                'client' => 'TechCorp Inc.',
                'technologies' => ['Laravel', 'Vue.js', 'MySQL', 'Redis', 'Stripe'],
                'status' => 'published',
                'is_featured' => true,
            ],
            // Add more projects...
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }

        // Create blog posts
        $posts = [
            [
                'title' => 'Getting Started with Laravel 11',
                'slug' => 'getting-started-with-laravel-11',
                'excerpt' => 'Learn the new features and improvements in Laravel 11',
                'content' => 'Laravel 11 brings exciting new features...',
                'status' => 'published',
                'published_at' => now(),
            ],
            // Add more posts...
        ];

        foreach ($posts as $post) {
            Post::create($post);
        }

        // Create experiences
        $experiences = [
            [
                'title' => 'Senior Laravel Developer',
                'company' => 'Tech Solutions Inc.',
                'start_date' => '2020-01-01',
                'is_current' => true,
                'description' => 'Lead developer for multiple enterprise Laravel applications.',
                'technologies' => ['Laravel', 'Vue.js', 'MySQL', 'Redis', 'AWS'],
            ],
            // Add more experiences...
        ];

        foreach ($experiences as $experience) {
            Experience::create($experience);
        }
    }
}