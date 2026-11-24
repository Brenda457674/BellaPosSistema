<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Middleware para controlar los roles
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // Middleware para cambiar el idioma
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

    })
    ->withCommands([
        \App\Console\Commands\CreateAdmin::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {

        // Las rutas API responden en formato JSON
        $exceptions->shouldRenderJsonWhen(
            function (Request $request, Throwable $e) {
                return $request->is('api/*')
                    || $request->expectsJson();
            }
        );

    })
    ->create();
