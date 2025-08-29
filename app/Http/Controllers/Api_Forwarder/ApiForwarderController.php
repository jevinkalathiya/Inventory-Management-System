<?php

namespace App\Http\Controllers\Api_Forwarder;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\CategoryProductController;

class ApiForwarderController
{
    public function ApiForwarder(Request $request, $endpoint)
    {
        if (!$request->ajax() && !$request->expectsJson()) {
            abort(403);
        }

        $method = strtolower($request->method());
        $token = $request->header('Authorization');
        $client = $request->header('X-API-Client');

        if ($endpoint === 'getcategory') {
            // Set headers on the request object
            $request->headers->set('Authorization', 'Bearer ' . $token);
            $request->headers->set('X-API-Client', $client);

            // Call the local controller method
            return app(CategoryProductController::class)->getCategory($request);

        }

    }
}
