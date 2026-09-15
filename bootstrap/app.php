<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Daftarkan Alias Middleware RBAC
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
            'workspace' => \App\Http\Middleware\EnsureUserHasWorkspace::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();