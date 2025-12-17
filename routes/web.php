<?php

use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\{
    HomeController,
    PortfolioController,
    BlogController,
    ContactController,
    ResumeController,
    SearchController
};

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');
Route::get('/portfolio/{project:slug}', [PortfolioController::class, 'show'])->name('portfolio.detail');
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blog/category/{category:slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/tag/{tag:slug}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.detail');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit');
Route::post('/newsletter/subscribe', [ContactController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/resume', [ResumeController::class, 'index'])->name('resume');
Route::get('/resume/download', [ResumeController::class, 'download'])->name('resume.download');
Route::get('/resume/print', [ResumeController::class, 'print'])->name('resume.print');
Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/search/live', [SearchController::class, 'liveSearch'])->name('search.live');

// Newsletter Routes
Route::post('/newsletter/subscribe', [ContactController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/verify/{token}', [ContactController::class, 'verifySubscription'])->name('newsletter.verify');
Route::get('/newsletter/unsubscribe/{email}', [ContactController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Sitemap and RSS
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');
Route::get('/feed', [HomeController::class, 'rssFeed'])->name('rss.feed');


// Auth Routes (Breeze)
require __DIR__.'/auth.php';

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [\App\Http\Controllers\Admin\DashboardController::class, 'analytics'])->name('analytics');

    // Settins
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('index');
        Route::put('/update', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('update');
        Route::post('/clear-cache', [\App\Http\Controllers\Admin\SettingsController::class, 'clearCache'])->name('clear-cache');
        Route::post('/reset', [\App\Http\Controllers\Admin\SettingsController::class, 'reset'])->name('reset');
        Route::post('/seed', [\App\Http\Controllers\Admin\SettingsController::class, 'seed'])->name('seed');
    });
    
    // Projects
    Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class);
    Route::post('projects/bulk-action', [\App\Http\Controllers\Admin\ProjectController::class, 'bulkAction'])->name('projects.bulk-action');
    
    // Posts
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class);
    Route::post('posts/bulk-action', [\App\Http\Controllers\Admin\PostController::class, 'bulkAction'])->name('posts.bulk-action');
    
    // Skills
    Route::resource('skills', \App\Http\Controllers\Admin\SkillController::class);
    Route::post('skills/bulk-action', [\App\Http\Controllers\Admin\SkillController::class, 'bulkAction'])->name('skills.bulk-action');
    
    // Experiences
    Route::resource('experiences', \App\Http\Controllers\Admin\ExperienceController::class);
    
    // Education
    Route::resource('education', \App\Http\Controllers\Admin\EducationController::class);
    
    // Certifications
    Route::resource('certifications', \App\Http\Controllers\Admin\CertificationController::class);
    
    // Messages
    Route::resource('messages', \App\Http\Controllers\Admin\MessageController::class)->except(['create', 'store', 'edit', 'update']);
    Route::post('messages/{message}/reply', [\App\Http\Controllers\Admin\MessageController::class, 'reply'])->name('messages.reply');
    Route::post('messages/{message}/read', [\App\Http\Controllers\Admin\MessageController::class, 'markAsRead'])->name('messages.read');
    Route::post('messages/{message}/archive', [\App\Http\Controllers\Admin\MessageController::class, 'markAsArchived'])->name('messages.archive');
    Route::post('messages/bulk-action', [\App\Http\Controllers\Admin\MessageController::class, 'bulkAction'])->name('messages.bulk-action');
    
    // Categories
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    
    // Tags
    Route::resource('tags', \App\Http\Controllers\Admin\TagController::class);

    // Media
    Route::prefix('media')->name('media.')->group(function () {
        // 📂 Listing / Browser
        Route::get('/', [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('index');
        
        // ⬆ Upload files
        Route::post('/upload', [\App\Http\Controllers\Admin\MediaController::class, 'upload'])->name('upload');
        
        // 📁 Create directory
        Route::post('/create-directory', [\App\Http\Controllers\Admin\MediaController::class, 'createDirectory'])->name('create-directory');
        
        // ✏ Rename file or directory
        Route::post('/rename', [\App\Http\Controllers\Admin\MediaController::class, 'rename'])->name('rename');
        
        // 🗑 Delete single file or directory
        Route::delete('/delete', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('destroy');
        
        // 🗑🗑 Bulk delete files/directories
        Route::delete('/bulk-delete', [\App\Http\Controllers\Admin\MediaController::class, 'bulkDestroy'])->name('bulk-destroy');
        
        // ℹ File / Directory info (sidebar, modal, etc.)
        Route::post('/info', [\App\Http\Controllers\Admin\MediaController::class, 'getFileInfo'])->name('file-info');
        
        // 🔍 Simple view for selecting images (for projects)
        Route::get('/simple', [\App\Http\Controllers\Admin\MediaController::class, 'simpleIndex'])->name('simple');
    });

});

// Profile Routes (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
