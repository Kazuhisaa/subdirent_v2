<?php

use Illuminate\Foundation\Application;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
// Removed: use Throwable; // This use statement is unnecessary

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['api', \App\Http\Middleware\EnsureJsonRequests::class]) // Apply to mobile routes only
                ->prefix('api/mobile/v1') // URL: domain.com/api/v1/...
                ->group(base_path('routes/mobile.php'));
        },
    )
    
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(); // Remove EnsureJsonRequests from global API middleware

        $routeMiddleware = [
    'auth' => Authenticate::class,
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
];

 $middleware->validateCsrfTokens(except: [
        'stripe/webhook',
        'tenant/*/contract/*/autopay',
    ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            // Check if the request is for an API route
            if ($request->is('api/mobile/*')) {
                // Handle ValidationException
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'message' => $e->getMessage(),
                        'errors' => $e->errors(),
                    ], 422);
                }

                // Handle AuthenticationException
                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json(['message' => 'Unauthenticated.'], 401);
                }

                // Handle NotFoundHttpException (e.g., model not found or route not found)
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                     return response()->json(['message' => 'Resource not found.'], 404);
                }

                // Default handler for other exceptions
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                return response()->json([
                    'message' => $e->getMessage(),
                ], $statusCode);
            }
        });
    })->create();
