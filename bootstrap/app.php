<?php

use Illuminate\Foundation\Application;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Configuration\Exceptions;

use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api/mobile/v1') // URL: domain.com/api/v1/...
                ->group(base_path('routes/mobile.php'));
        },
    )
    
    ->withMiddleware(function (Middleware $middleware): void {
        $routeMiddleware = [
    'auth' => Authenticate::class,
    'admin' => \App\Http\Middleware\AdminMiddleware::class, // ✅ add this
];

 $middleware->validateCsrfTokens(except: [
        'stripe/webhook',
        'tenant/*/contract/*/autopay',
    ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (InvalidCredentialsException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode());
        });

        $exceptions->renderable(function (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        });
    })->create();
