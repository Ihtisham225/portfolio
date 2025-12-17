<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get or set a setting value
     *
     * @param string|array $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key = null, $default = null)
    {
        if (is_array($key)) {
            // Set multiple settings
            foreach ($key as $k => $v) {
                Setting::setValue($k, $v);
            }
            return true;
        }
        
        if (is_null($key)) {
            // Get all settings
            return Setting::getAll();
        }
        
        // Get single setting
        return Setting::getValue($key, $default);
    }
}

if (!function_exists('settings_group')) {
    /**
     * Get all settings in a group
     *
     * @param string $group
     * @return array
     */
    function settings_group($group)
    {
        return Setting::getGroup($group);
    }
}