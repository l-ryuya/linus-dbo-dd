<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 権限判定
 * Route::middleware(['auth', 'roles:admin,tenant'])
 * のようにRouteで指定する
 */
class RoleSelectorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     *
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (empty($roles)) {
            return $next($request);
        }

        foreach ($roles as $role) {
            /** @var \App\Auth\GenericUser|null $user */
            $user = $request->user();

            $userOption = $user->getUserOption();
            if ($role === 'admin' && $userOption->isAdmin()) {
                return $next($request);
            } elseif ($role === 'tenant' && $userOption->isTenant()) {
                return $next($request);
            } elseif ($role === 'customer' && $userOption->isCustomer()) {
                return $next($request);
            }
        }

        abort(403, __('auth.forbidden'));
    }
}
