<?php

declare(strict_types=1);

namespace App\Http\Controllers\ServiceRepresentatives;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRepresentatives\SearchRequest;
use App\Http\Resources\ServiceRepresentatives\SearchResource;
use App\Services\Role\TenantUserRoleService;
use App\UseCases\ServiceRepresentatives\SearchAction;

class SearchController extends Controller
{
    /**
     * サービス担当者一覧を取得する
     *
     * @param \App\Http\Requests\ServiceRepresentatives\SearchRequest $request
     * @param \App\UseCases\ServiceRepresentatives\SearchAction       $action
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(
        SearchRequest $request,
        SearchAction $action,
    ): \Illuminate\Http\Resources\Json\AnonymousResourceCollection {
        /** @var \App\Auth\GenericUser $user */
        $user = $request->user();
        $tenantUserRoleService = new TenantUserRoleService($user->getUserOption());

        return SearchResource::collection(
            $action(
                $tenantUserRoleService->getTenantId(),
                $tenantUserRoleService->getServiceId(),
                $request->validated('userName'),
            ),
        );
    }
}
