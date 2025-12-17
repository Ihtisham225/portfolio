<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $fillable = [
        'degree',
        'institution',
        'location',
        'start_date',
        'end_date',
        'is_current',
        'description',
        'score',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'score' => 'decimal:2',
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
            $q->where('degree', 'like', "%{$search}%")
              ->orWhere('institution', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getDurationAttribute()
    {
        if (!$this->start_date) {
            return null;
        }

        $start = $this->start_date;
        $end = $this->is_current
            ? now()
            : ($this->end_date ?? now());

        $years = $start->diffInYears($end);
        $months = $start->diffInMonths($end) % 12;

        $duration = '';

        if ($years > 0) {
            $duration .= $years . ' ' . ($years === 1 ? 'year' : 'years');
        }

        if ($months > 0) {
            if ($years > 0) {
                $duration .= ' ';
            }
            $duration .= $months . ' ' . ($months === 1 ? 'month' : 'months');
        }

        return $duration ?: 'Less than a month';
    }

    public function getFormattedStartDateAttribute()
    {
        return $this->start_date?->format('M Y');
    }

    public function getFormattedEndDateAttribute()
    {
        return $this->is_current
            ? 'Present'
            : ($this->end_date?->format('M Y') ?? 'Present');
    }

    public function getScoreFormattedAttribute()
    {
        return $this->score ? number_format($this->score, 2) : null;
    }
}