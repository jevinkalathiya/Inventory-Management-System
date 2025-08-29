<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiClient
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Extract Authorization header
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                "message" => "Authorization Bearer token missing",
                "code"    => "unauthorized"
            ], 401);
        }

        $bearerToken = trim(substr($authHeader, 7)); // remove "Bearer "

        // 2. Check in DB (users.user_api stores encrypted value)
        $user = DB::table('users')->where('user_api', $bearerToken)->first();

        if (!$user) {
            return response()->json([
                "message" => "Invalid API Key",
                "code"    => "unauthorized"
            ], 401);
        }

        // after decrypting
        try {
            $decryptedToken = Crypt::decryptString($bearerToken);

            // Replace the header with decrypted Sanctum token
            $request->headers->set('Authorization', 'Bearer ' . $decryptedToken);

            // ✅ Also tell Laravel which user is being authenticated
            $userModel = User::where('id', $user->id)->first();
            Auth::setUser($userModel);

        } catch (\Exception $ex) {
            return response()->json([
                "message" => "Invalid or corrupted API Key",
                "code"    => "unauthorized"
            ], 401);
        }

        if (
            !$request->hasHeader('X-API-Client') ||
            $request->header('X-API-Client') !== Auth::user()?->user_code // if user is not logged in it not throws error instead set null
        ) {
            // return error page for missing header
            return response()->json([
                "message" => "Client key not found in headers",
                "code"    => "unauthorized"
            ], 401); // 401 = Unauthorized
        }

        return $next($request);
    }
}
