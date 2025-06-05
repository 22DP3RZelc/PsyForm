<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestCreateMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin' || Auth::user()->role !== 'psychologist') {
            return redirect('/');
        }

        return $next($request);
    }
}