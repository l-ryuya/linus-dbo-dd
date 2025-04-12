<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 権限判定の仮実装版
 * Route::middleware(['auth:sanctum', 'scope:admin,service_manager'])
 * のようにRouteで指定する
 *
 * 権限は users.roles で管理
 *
 * @see \App\Enums\RoleType 権限種別
 */
class ScopeAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()->tokenCan($role)) {
            abort(403, __('auth.forbidden'));
        };

        return $next($request);
    }
}
