<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('login'); // Redirect jika belum login
        }

        if (in_array(auth()->user()->role, $roles)) {
            return $next($request);
        }

        return redirect('unauthorized'); // Redirect jika role tidak sesuai
    }
}
