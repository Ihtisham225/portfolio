<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'company',
        'location',
        'start_date',
        'end_date',
        'is_current',
        'description',
        'technologies',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'technologies' => 'array',
        'sort_order' => 'integer',
    ];

    // Scopes
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order')->orderBy('start_date', 'desc');
    }

    public function scopeWithSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('company', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getDurationAttribute()
    {
        $start = $this->start_date;
        $end = $this->is_current ? now() : $this->end_date;

        $years = $start->diffInYears($end);
        $months = $start->diffInMonths($end) % 12;

        $duration = '';
        if ($years > 0) {
            $duration .= $years . ' ' . ($years === 1 ? 'year' : 'years');
        }
        if ($months > 0) {
            if ($years > 0) $duration .= ' ';
            $duration .= $months . ' ' . ($months === 1 ? 'month' : 'months');
        }

        return $duration ?: 'Less than a month';
    }

    public function getFormattedStartDateAttribute()
    {
        return $this->start_date->format('M Y');
    }

    public function getFormattedEndDateAttribute()
    {
        return $this->is_current ? 'Present' : ($this->end_date?->format('M Y') ?? 'Present');
    }

    public function getTechnologiesArrayAttribute(): Collection
    {
        return collect($this->technologies ?? []);
    }
}