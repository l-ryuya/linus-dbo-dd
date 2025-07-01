<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        $middleware->alias([
            'functions' => App\Http\Middleware\FunctionsVerifyMiddleware::class,
            'roles' => App\Http\Middleware\RoleSelectorMiddleware::class,
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

        $exceptions->render(function (NotFoundHttpException $e) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Not Found.',
            ], 404);
        });

        $exceptions->render(function (ValidationException $e) {
            return response()->json([
                'message' => __('validation.message'),
                'errors' => $e->errors(),
            ], $e->status);
        });

        $exceptions->render(function (AuthenticationException $e) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        });

        $exceptions->render(function (HttpException $e) {
            return response()->json([
                'statusCode' => $e->getStatusCode(),
                'message' => $e->getMessage() ?: SymfonyResponse::$statusTexts[$e->getStatusCode()] . '.',
            ], $e->getStatusCode());
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if (! strpos($e->getMessage(), 'file_put_contents')) {
                $xRequestId = $request->header('x-request-id', '');
                Log::error(
                    $xRequestId,
                    [
                        'client_ip'	  => $request->getClientIp(),
                        'request_url' => $request->fullUrl(),
                        'request_params' => $request->all(),
                        'exception_file' => $e->getFile() . '::' . $e->getLine(),
                        'exception_message' => get_class($e) . '::' . $e->getMessage(),
                    ],
                );
            }

            return response()->json([
                'statusCode' => 500,
                'message' => 'A system error has occurred.',
            ], 500);
        });
    })->create();
