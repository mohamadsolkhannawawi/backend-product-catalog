<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:admin') or middleware('role:seller')
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!isset($user->role) || $user->role !== $role) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
