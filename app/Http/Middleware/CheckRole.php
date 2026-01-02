<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            return redirect('/login');
        }

        if ($request->user()->role !== $role) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }
            
            // Redirect to appropriate dashboard
            if ($request->user()->isAdmin()) {
                return redirect('/admin/dashboard');
            }
            return redirect('/wisudawan/dashboard');
        }

        return $next($request);
    }
}
