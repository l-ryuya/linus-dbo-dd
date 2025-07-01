<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * M5権限判定
 * Route::middleware(['auth', 'functions:FunctionCode1,FunctionCode2'])
 * のようにRouteで指定する
 */
class FunctionsVerifyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function handle(Request $request, Closure $next, string ...$functions): Response
    {
        if (empty($functions)) {
            return $next($request);
        }

        foreach ($functions as $function) {
            /** @var \App\Auth\GenericUser|null $user */
            $user = $request->user();
            if ($user?->tokenCan($function)) {
                return $next($request);
            }
        }

        abort(403, __('auth.forbidden'));
    }
}
