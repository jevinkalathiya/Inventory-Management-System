<?php

use Illuminate\Http\Request;
use App\Http\Middleware\SendOtp;
use Illuminate\Foundation\Application;
use App\Http\Middleware\VerifyApiClient;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'SendOtp' => SendOtp::class,
            'verifyApiClient' => VerifyApiClient::class,
        ]);
    })
    ->withExceptions(function ($exceptions): void {
        // $exceptions->renderable(function (Throwable $e, Request $request) {
        //     // Skip API routes → let API-specific handler take care of them
        //     if ($request->is('api/*') || $request->expectsJson()) {
        //         return null; // means "don't handle here"
        //     }
        //     // Determine HTTP status
        //     $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

        //     // Show custom view if exists
        //     if (view()->exists("errors.$status")) {
        //         return response()->view("errors.$status", ['exception' => $e], $status);
        //     }

        //     // Fallback default view
        //     return response()->view("errors.default", ['status' => $status, 'exception' => $e], $status);
        // });

        // for api
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            $authHeader = $request->header('Authorization');

            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                return response()->json([
                    "message" => "Authorization header missing or invalid format",
                    "code"    => "unauthorized"
                ], 401);
            }

            // Extract token from "Bearer <token>"
            $token = substr($authHeader, 7);

            // Validate token against DB (instead of Auth::user())
            $user = \App\Models\User::where('user_api', $token)->first();

            if (!$user) {
                return response()->json([
                    "message" => "Invalid token",
                    "code"    => "unauthorized"
                ], 401);
            }

            // Optionally check your secret key header
            if (!$request->hasHeader('X-API-Client') || 
                $request->header('X-API-Client') !== $user->user_code) {
                return response()->json([
                    "message" => "Secret key not found or invalid",
                    "code"    => "unauthorized"
                ], 401);
            }

            
            return null;
        });
    

})->create();


