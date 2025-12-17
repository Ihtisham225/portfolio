<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'issuer',
        'issue_date',
        'expiration_date',
        'credential_id',
        'credential_url',
        'image',
        'sort_order',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiration_date' => 'date',
        'sort_order' => 'integer',
    ];

    // Scopes
    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expiration_date')
              ->orWhere('expiration_date', '>=', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiration_date')
                     ->where('expiration_date', '<', now());
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order')->orderBy('issue_date', 'desc');
    }

    public function scopeWithSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('issuer', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getFormattedIssueDateAttribute()
    {
        return $this->issue_date->format('M Y');
    }

    public function getFormattedExpirationDateAttribute()
    {
        return $this->expiration_date?->format('M Y');
    }

    public function getIsValidAttribute()
    {
        if (!$this->expiration_date) {
            return true;
        }

        return $this->expiration_date >= now();
    }

    public function getValidityStatusAttribute()
    {
        if (!$this->expiration_date) {
            return 'No Expiration';
        }

        if ($this->is_valid) {
            return 'Valid until ' . $this->formatted_expiration_date;
        }

        return 'Expired on ' . $this->formatted_expiration_date;
    }
}