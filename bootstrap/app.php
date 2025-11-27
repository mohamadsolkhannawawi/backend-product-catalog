<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Http\Middleware\HandleCors;

use App\Http\Middleware\ApiAuthenticate;
use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SellerActiveMiddleware;
use App\Http\Middleware\ReviewThrottle;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {

        // Sanctum: Bearer Token Authentication (stateless for API)
        // Use Bearer tokens for API authentication instead of session-based
        // Note: NOT using statefulApi() - we're fully stateless with Bearer tokens

        // Supaya auth API tidak redirect ke "/login"
        // Mendaftarkan dua alias: 'auth.api' dan 'api.auth'
        $middleware->alias([
            'auth.api' => ApiAuthenticate::class,
            'api.auth' => ApiAuthenticate::class,
            'admin' => AdminOnly::class,
            'role' => RoleMiddleware::class,
            'seller.active' => SellerActiveMiddleware::class,
            'review.throttle' => ReviewThrottle::class,
        ]);

        // Global CORS
        $middleware->use([
            HandleCors::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })

->create();
