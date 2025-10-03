<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function (\Illuminate\Routing\Router $router) {
            $router->middleware('web')->group(base_path('routes/frontend/web.php'));
            $router->middleware('web')->prefix('admin')->group(base_path('routes/backend/web.php'));
            $router->middleware('web')->prefix('test')->group(base_path('routes/test.php'));

            $router->middleware('api')->prefix('api')->name('api.frontend.')->group(base_path('routes/frontend/api.php'));
            $router->middleware('api')->prefix('api/backend')->name('api.backend.')->group(base_path('routes/backend/api.php'));
        },
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
