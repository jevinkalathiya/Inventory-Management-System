<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SendOtp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('auth_stage')) {
            // sending error for custom view file
            abort(403);
        }

        $stage = session('auth_stage'); // get the actual value, or null if not set

        if (!in_array($stage, ['login', 'mfa'])) {
            abort(403);
        }

        return $next($request);
    }
}
