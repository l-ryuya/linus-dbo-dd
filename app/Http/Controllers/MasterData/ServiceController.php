<?php

declare(strict_types=1);

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Resources\Service\IndexResource;
use App\Services\Role\TenantUserRoleService;
use App\UseCases\Service\IndexAction;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * サービス一覧を取得する
     *
     * @param \Illuminate\Http\Request              $request
     * @param \App\UseCases\Service\IndexAction $action
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(
        Request $request,
        IndexAction $action,
    ): \Illuminate\Http\Resources\Json\AnonymousResourceCollection {
        /** @var \App\Auth\GenericUser $user */
        $user = $request->user();

        return IndexResource::collection(
            $action(
                $user->getUserOption()->language_code,
                (new TenantUserRoleService($user->getUserOption()))->getTenantId(),
            ),
        );
    }
}
