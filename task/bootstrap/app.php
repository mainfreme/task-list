<?php

use App\Shared\Infrastructure\Providers\MigrationServiceProvider;
use App\Shared\Infrastructure\Providers\RepositoryServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        RepositoryServiceProvider::class,
        MigrationServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust all proxies (nginx-proxy) - niezbędne dla HTTPS za reverse proxy
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'api.key' => App\ApplicationManager\Infrastructure\Middleware\ApiKeyMiddleware::class,
            'jwt' => App\ApplicationManager\Infrastructure\Middleware\JwtMiddleware::class,
            'user.jwt' => App\Auth\Infrastructure\Middleware\UserJwtMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
        // $exceptions->render(function (ValidationException $e, $request) {
        //     if ($request->is('api/*')) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Validation failed',
        //             'errors' => $e->errors(),
        //         ], 422);
        //     }
        // });
    })->create();
