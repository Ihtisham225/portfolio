<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // Relationships
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'category_post');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'category_project');
    }

    // Scopes
    public function scopePostType($query)
    {
        return $query->where('type', 'post');
    }

    public function scopeProjectType($query)
    {
        return $query->where('type', 'project');
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeWithSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                     ->orWhere('description', 'like', "%{$search}%");
    }

    // Accessors
    public function getPostCountAttribute()
    {
        return $this->posts()->count();
    }

    public function getProjectCountAttribute()
    {
        return $this->projects()->count();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}