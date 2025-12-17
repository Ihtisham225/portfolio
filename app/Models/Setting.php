<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    // Scopes
    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopeGeneral($query)
    {
        return $query->where('group', 'general');
    }

    public function scopeSeo($query)
    {
        return $query->where('group', 'seo');
    }

    public function scopeSocial($query)
    {
        return $query->where('group', 'social');
    }

    public function scopeContact($query)
    {
        return $query->where('group', 'contact');
    }

    // Accessors & Mutators
    public function getValueAttribute($value)
    {
        switch ($this->type) {
            case 'json':
                return json_decode($value, true);
            case 'boolean':
                return (bool) $value;
            case 'number':
                return is_numeric($value) ? $value + 0 : $value;
            case 'array':
                return explode(',', $value);
            default:
                return $value;
        }
    }

    public function setValueAttribute($value)
    {
        switch ($this->type) {
            case 'json':
                $this->attributes['value'] = json_encode($value);
                break;
            case 'boolean':
                $this->attributes['value'] = $value ? '1' : '0';
                break;
            case 'array':
                $this->attributes['value'] = is_array($value) ? implode(',', $value) : $value;
                break;
            default:
                $this->attributes['value'] = $value;
                break;
        }
    }

    // Static Methods
    public static function getValue($key, $default = null)
    {
        $cacheKey = 'setting_' . $key;
        
        return Cache::rememberForever($cacheKey, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function setValue($key, $value, $type = 'text', $group = 'general')
    {
        $setting = self::where('key', $key)->first();

        if ($setting) {
            $setting->value = $value;
            $setting->save();
        } else {
            $setting = self::create([
                'key' => $key,
                'value' => $value,
                'type' => $type,
                'group' => $group,
            ]);
        }

        return $setting;
    }

    public static function getGroup($group)
    {
        return self::where('group', $group)->pluck('value', 'key')->toArray();
    }

    public static function getAll()
    {
        return self::pluck('value', 'key')->toArray();
    }

    public static function cacheKey()
    {
        return 'settings_cache';
    }

    public static function clearCache($key = null)
    {
        if ($key) {
            Cache::forget('setting_' . $key);
        } else {
            Cache::forget(self::cacheKey());
            // Clear all setting caches
            Cache::forget('settings_cache');
            // You might want to clear all setting-specific caches
            // This requires storing keys or using a cache tag system
        }
    }

    public static function clearAllSettingsCache()
    {
        // Get all setting keys
        $keys = self::pluck('key')->toArray();
        
        foreach ($keys as $key) {
            Cache::forget('setting_' . $key);
        }
        
        Cache::forget('settings_cache');
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }
}