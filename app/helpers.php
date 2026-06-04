<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        $settings = Cache::rememberForever('settings', function () {
            return Setting::pluck('value', 'key')->all();
        });

        return $settings[$key] ?? $default;
    }
}
