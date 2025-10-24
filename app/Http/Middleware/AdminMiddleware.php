<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if logged in and role is admin
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // If not admin → redirect or deny
        return redirect('/')->with('error', 'Access denied. Admins only.');
    }
}
