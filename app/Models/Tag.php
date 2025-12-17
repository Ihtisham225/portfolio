<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
    ];

    // Relationships
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_tag');
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

    public function scopePopular($query, $limit = 10)
    {
        return $query->withCount('posts')
                     ->orderBy('posts_count', 'desc')
                     ->limit($limit);
    }

    public function scopeWithSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
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

    public function getTotalCountAttribute()
    {
        return $this->post_count + $this->project_count;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }
}