<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'description',
        'image',
        'gallery',
        'client',
        'project_date',
        'project_url',
        'github_url',
        'technologies',
        'status',
        'views',
        'sort_order',
        'is_featured',
    ];

    protected $casts = [
        'gallery' => 'array',
        'technologies' => 'array',
        'project_date' => 'date',
        'views' => 'integer',
        'sort_order' => 'integer',
        'is_featured' => 'boolean',
    ];

    // Relationships
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_project');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'project_tag');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    public function scopeWithSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('excerpt', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('client', 'like', "%{$search}%");
        });
    }

    // Accessors & Mutators
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : 'https://via.placeholder.com/800x600';
    }

    public function getGalleryUrlsAttribute()
    {
        if (!$this->gallery) {
            return [];
        }

        return array_map(function ($image) {
            return asset('storage/' . $image);
        }, $this->gallery);
    }

    public function getTechnologiesArrayAttribute(): Collection
    {
        return collect($this->technologies ?? []);
    }

    public function getFormattedDateAttribute()
    {
        return $this->project_date?->format('F Y');
    }

    public function getReadTimeAttribute()
    {
        $words = str_word_count(strip_tags($this->description));
        $minutes = ceil($words / 200);
        return $minutes . ' min read';
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views');
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });

        static::updating(function ($project) {
            if ($project->isDirty('title') && empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });
    }
}