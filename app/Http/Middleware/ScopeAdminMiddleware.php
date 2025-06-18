<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 権限判定
 * Route::middleware(['auth', 'scope:FunctionCode1,FunctionCode2'])
 * のようにRouteで指定する
 */
class ScopeAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function handle(Request $request, Closure $next, string ...$scopes): Response
    {
        if (empty($scopes)) {
            return $next($request);
        }

        foreach ($scopes as $scope) {
            /** @var \App\Auth\GenericUser|null $user */
            $user = $request->user();
            if ($user?->tokenCan($scope)) {
                return $next($request);
            }
        }

        abort(403, __('auth.forbidden'));
    }
}
