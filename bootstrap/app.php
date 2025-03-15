<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Console\Commands\PruneExpired;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: 'v1',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->throttleApi();

        $middleware->redirectGuestsTo(
            fn(Request $request) => response()->json(
                ['message' => 'Unauthenticated.'],
                401,
            ),
        );

        $middleware->api(append: [
            App\Http\Middleware\LocaleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReport([
            AuthorizationException::class,
            AuthenticationException::class,
            HttpException::class,
            ModelNotFoundException::class,
            ValidationException::class,
        ]);

        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
        ]);

        $exceptions->render(function (AuthenticationException $e) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        });

        $exceptions->render(function (HttpException $e) {
            return response()->json([
                'statusCode' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        });

        $exceptions->render(function (Throwable $e) {
            Log::error($e);

            return response()->json([
                'statusCode' => 500,
                'message' => 'A system error has occurred.',
            ], 500);
        });
    })->withCommands([
        PruneExpired::class, // アクセストークン削除
    ])->create();
