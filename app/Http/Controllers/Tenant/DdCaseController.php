<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\DdCase\IndexRequest;
use App\Http\Resources\Tenant\DdCase\IndexCollection;
use App\Http\Resources\Tenant\DdCase\SummaryResource;
use App\Services\Role\TenantUserRoleService;
use App\UseCases\Tenant\DdCase\IndexAction;
use App\UseCases\Tenant\DdCase\SummaryAction;
use Illuminate\Http\Request;

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

    /**
     * デューデリジェンスケースのサマリー取得
     *
     * @param \Illuminate\Http\Request $request
     * @param string $publicId
     * @param \App\UseCases\Tenant\DdCase\SummaryAction $action
     *
     * @return \App\Http\Resources\Tenant\DdCase\SummaryResource
     */
    public function summary(
        Request $request,
        string $publicId,
        SummaryAction $action,
    ): SummaryResource {
        /** @var \App\Auth\GenericUser $user */
        $user = $request->user();

        return new SummaryResource(
            $action(
                $user->getUserOption()->language_code,
                (new TenantUserRoleService($user->getUserOption()))->getTenantId(),
                $publicId,
            ),
        );
    }
}
