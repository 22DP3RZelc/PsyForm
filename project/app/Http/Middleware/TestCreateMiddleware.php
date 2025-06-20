<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestCreateMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'psychologist'])) {
            return redirect('/');
        }

        return $next($request);
    }
}