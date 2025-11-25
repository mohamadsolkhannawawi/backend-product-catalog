<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SellerActiveMiddleware
{
    /**
     * Ensure the authenticated user is a seller and the seller is approved + active.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if ($user->role !== 'seller') {
            return response()->json(['message' => 'Forbidden: not a seller'], 403);
        }

        $seller = $user->seller;
        if (!$seller) {
            return response()->json(['message' => 'Seller profile not found'], 403);
        }

        if ($seller->status !== 'approved' || !$seller->is_active) {
            return response()->json(['message' => 'Seller account not active or not approved'], 403);
        }

        return $next($request);
    }
}
