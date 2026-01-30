<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as AuthenticateMiddleware;

class CustomAuthenticate extends AuthenticateMiddleware
{
    protected function redirectTo($request) {
        if (!$request->expectsJson()) {
            return route('auth');
        }
    }
}
