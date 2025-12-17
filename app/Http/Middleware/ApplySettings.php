<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApplySettings
{
    public function handle(Request $request, Closure $next)
    {
        // Apply timezone setting
        if (setting('timezone')) {
            config(['app.timezone' => setting('timezone')]);
            date_default_timezone_set(setting('timezone'));
        }
        
        // Apply maintenance mode
        if (setting('maintenance_mode') && !$request->is('admin/*') && !auth()->check()) {
            abort(503, 'Site is under maintenance');
        }
        
        return $next($request);
    }
}