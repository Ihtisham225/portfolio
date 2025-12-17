<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'percentage',
        'color',
        'icon',
        'sort_order',
        'is_featured',
    ];

    protected $casts = [
        'percentage' => 'integer',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Scopes
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeWithSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    // Accessors
    public function getIconClassAttribute()
    {
        return $this->icon ?: 'fas fa-code';
    }

    public function getProgressColorAttribute()
    {
        return $this->color ?: '#3B82F6';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($skill) {
            if (empty($skill->slug)) {
                $skill->slug = Str::slug($skill->name);
            }
        });
    }
}