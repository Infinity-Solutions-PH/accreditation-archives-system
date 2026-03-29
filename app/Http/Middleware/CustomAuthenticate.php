<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Middleware\Authenticate as AuthenticateMiddleware;

class CustomAuthenticate extends AuthenticateMiddleware
{
    protected function redirectTo($request) {
        if (!$request->expectsJson()) {
            return route('auth');
        }
    }
}
