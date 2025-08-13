<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\DdCase\IndexRequest;
use App\Http\Resources\Tenant\DdCase\IndexCollection;
use App\Services\Role\TenantUserRoleService;
use App\UseCases\Tenant\DdCase\IndexAction;

class DdCaseController extends Controller
{
    /**
     * デューデリジェンスケース一覧取得
     *
     * @param \App\Http\Requests\Tenant\DdCase\IndexRequest $request
     * @param \App\UseCases\Tenant\DdCase\IndexAction $action
     *
     * @return \App\Http\Resources\Tenant\DdCase\IndexCollection
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
                $request->validated('tenantPublicId'),
                $request->validated('companyName'),
                $request->validated('ddCaseNo'),
                $request->validated('currentDdStepCode'),
                $request->validated('overallResult'),
                $request->validated('customerRiskLevel'),
                $request->validated('startedAtFrom'),
                $request->validated('startedAtTo'),
                $request->validated('endedAtFrom'),
                $request->validated('endedAtTo'),
                $request->validated('displayed'),
                $request->validated('page'),
            ),
        );
    }
}
