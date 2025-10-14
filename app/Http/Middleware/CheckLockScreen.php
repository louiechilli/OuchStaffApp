<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLockScreen
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check config if lock screen feature is enabled
        if (!config('features.lock_screen', false)) {
            return $next($request);
        }

        // Skip lock screen check for lock/unlock routes
        if ($request->is('lock') || $request->is('unlock') || $request->is('check-lock-status')) {
            return $next($request);
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            return $next($request);
        }

        // Check if screen is locked
        if (session('screen_locked', false)) {
            if ($request->expectsJson()) {
                return response()->json(['locked' => true], 423);
            }
            return redirect()->route('lock.screen');
        }

        // Update last activity timestamp ONLY for non-status-check requests
        session(['last_activity' => now()->timestamp]);

        return $next($request);
    }
}