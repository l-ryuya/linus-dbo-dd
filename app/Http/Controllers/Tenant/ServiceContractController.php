<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ServiceContract\IndexRequest;
use App\Http\Resources\Tenant\ServiceContract\IndexCollection;
use App\Services\Role\TenantUserRoleService;
use App\UseCases\Tenant\ServiceContract\IndexAction;

class ServiceContractController extends Controller
{
    /**
     * テナント管理者の顧客サービス契約一覧を取得する
     *
     * @param \App\Http\Requests\Tenant\ServiceContract\IndexRequest $request
     * @param \App\UseCases\Tenant\ServiceContract\IndexAction       $action
     *
     * @return \App\Http\Resources\Tenant\ServiceContract\IndexCollection
     */
    public function index(
        IndexRequest $request,
        IndexAction $action,
    ): IndexCollection {
        /** @var \App\Auth\GenericUser $user */
        $user = $request->user();

        return new IndexCollection(
            $action(
                $user->getUserOption()->language_code,
                (new TenantUserRoleService($user->getUserOption()))->getTenantId(),
                $request->validated('tenantName'),
                $request->validated('servicePublicId'),
                $request->validated('servicePlanPublicId'),
                $request->validated('customerName'),
                $request->validated('contractName'),
                $request->validated('contractStatusCode'),
                $request->validated('serviceUsageStatusCode'),
                $request->validated('contractDate'),
                $request->validated('contractStartDate'),
                $request->validated('displayed'),
                $request->validated('page'),
            ),
        );
    }
}
