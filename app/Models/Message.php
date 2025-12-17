<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'status',
        'response',
        'responded_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'user_agent' => 'array',
        'responded_at' => 'datetime',
    ];

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeWithSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('subject', 'like', "%{$search}%")
              ->orWhere('message', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getIsUnreadAttribute()
    {
        return $this->status === 'unread';
    }

    public function getIsRepliedAttribute()
    {
        return $this->status === 'replied';
    }

    public function getShortMessageAttribute()
    {
        return Str::limit(strip_tags($this->message), 100);
    }

    public function getBrowserAttribute()
    {
        return $this->user_agent['browser'] ?? 'Unknown';
    }

    public function getPlatformAttribute()
    {
        return $this->user_agent['platform'] ?? 'Unknown';
    }

    // Methods
    public function markAsRead()
    {
        $this->status = 'read';
        $this->save();
    }

    public function markAsReplied($response = null)
    {
        $this->status = 'replied';
        $this->response = $response;
        $this->responded_at = now();
        $this->save();
    }

    public function markAsArchived()
    {
        $this->status = 'archived';
        $this->save();
    }
}