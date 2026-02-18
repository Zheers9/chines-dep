<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterForExaming
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $setting = Setting::latest()->first();
        if ($setting->active == 0 || $setting->start_date >= now() || $setting->end_date <= now()) {
            return response()->json([
                'message' => 'Register for exam is closed',
            ], 403);
        }

        return $next($request);
    }
}
