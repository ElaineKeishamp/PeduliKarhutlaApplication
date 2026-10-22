<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SetLocale;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [SetLocale::class]);
        $middleware->alias(['role' => RoleMiddleware::class]);
        // cloudflared connects to the local Laravel server and forwards the
        // original HTTPS host/protocol; trust only the loopback connector.
        $middleware->trustProxies(at: ['127.0.0.1', '::1']);
        // Midtrans sends server-to-server notifications without a browser CSRF token.
        $middleware->validateCsrfTokens(except: ['payments/midtrans/notification']);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
