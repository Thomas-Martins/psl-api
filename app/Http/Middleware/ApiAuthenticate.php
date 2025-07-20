<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class ApiAuthenticate extends Middleware
{
    protected function unauthenticated($request, array $guards)
    {
        // Toujours JSON pour l'API
        abort(response()->json(['message' => 'Unauthenticated.'], 401));
    }
}
