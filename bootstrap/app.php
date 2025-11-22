<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi()
            ->prependToGroup('api', \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class);

        // untuk Postman & Sanctum
        $middleware->appendToGroup('api', \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class);

        // session (wajib untuk Sanctum SPA)
        $middleware->appendToGroup('api', \Illuminate\Session\Middleware\StartSession::class);

        // CORS
        $middleware->use([
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })



    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
