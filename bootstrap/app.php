<?php

use App\Http\Middlewares\AdminAuthRequest;
use App\Http\Middlewares\CheckJsonRequest;
use App\Http\Middlewares\AuthApiMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'json' => CheckJsonRequest::class,
            'auth.admin' => AdminAuthRequest::class,
            'auth.api' => AuthApiMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
$app->usePublicPath(base_path('public_html'));
return $app;
