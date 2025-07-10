<?php

declare(strict_types=1);

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServicePlan\IndexRequest;
use App\Http\Resources\ServicePlan\IndexResource;
use App\Services\Role\TenantUserRoleService;
use App\UseCases\ServicePlan\IndexAction;

class ServicePlanController extends Controller
{
    /**
     * 指定サービス毎のサービスプラン一覧を取得する
     *
     * @param \App\Http\Requests\ServicePlan\IndexRequest $request
     * @param \App\UseCases\ServicePlan\IndexAction       $action
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(
        IndexRequest $request,
        IndexAction $action,
    ): \Illuminate\Http\Resources\Json\AnonymousResourceCollection {
        /** @var \App\Auth\GenericUser $user */
        $user = $request->user();

        return IndexResource::collection(
            $action(
                $user->getUserOption()->language_code,
                (new TenantUserRoleService($user->getUserOption()))->getTenantId(),
                $request->validated('servicePublicId'),
            ),
        );
    }
}
