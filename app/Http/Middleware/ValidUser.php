<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Log;

class ValidUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response{

        if (!auth()->check()) {

            // Always return JSON for proxy / API / AJAX
            if ($request->is('wa1/*') || $request->ajax() || $request->expectsJson()) {
                
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            
            
            // For normal web routes
            return redirect()->route('login');
        }

        return $next($request);
    }

}
