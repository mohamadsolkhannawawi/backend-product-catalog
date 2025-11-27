<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     * 
     * API routes are protected by Sanctum tokens, not CSRF.
     * Public endpoints (validation, auth) use Bearer tokens or session-based auth.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/*',
        'sanctum/*',
    ];
}
