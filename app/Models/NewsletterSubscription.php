<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'ip_address',
        'user_agent',
        'is_active',
        'is_verified',
        'verification_token',
        'verified_at',
        'unsubscribed_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_verified', false);
    }

    // Methods
    public function generateVerificationToken()
    {
        $this->verification_token = Str::random(40);
        $this->save();
        
        return $this->verification_token;
    }

    public function verify()
    {
        $this->is_verified = true;
        $this->verification_token = null;
        $this->verified_at = now();
        $this->save();
    }

    public function unsubscribe()
    {
        $this->is_active = false;
        $this->unsubscribed_at = now();
        $this->save();
    }

    public function resubscribe()
    {
        $this->is_active = true;
        $this->unsubscribed_at = null;
        $this->save();
    }

    // Accessors
    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return 'unsubscribed';
        }
        
        if (!$this->is_verified) {
            return 'pending';
        }
        
        return 'subscribed';
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'subscribed' => 'success',
            'pending' => 'warning',
            'unsubscribed' => 'danger',
            default => 'secondary',
        };
    }
}