<?php

use App\Admin\Http\Middleware\EnsureStaff;
use App\Markets\Http\Middleware\ResolveMarket;
use App\Seo\Http\Middleware\HandleRedirects;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(ResolveMarket::class);
        $middleware->append(HandleRedirects::class);

        $middleware->alias([
            'staff' => EnsureStaff::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhooks/shopify*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
