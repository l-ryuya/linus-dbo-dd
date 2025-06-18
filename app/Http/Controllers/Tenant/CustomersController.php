<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Customers\IndexRequest;
use App\Http\Resources\Tenant\Customers\IndexCollection;
use App\Models\Tenant;
use App\Services\M5\UserOrganizationService;
use App\Shared\Language\IsoLanguageCode;
use App\UseCases\Tenant\Customers\IndexAction;

class CustomersController extends Controller
{
    protected ?Tenant $identifiedTenant;

    public function __construct(
        private UserOrganizationService $userOrganizationService,
    ) {
        parent::__construct();
    }

    /**
     * テナント管理者の顧客一覧を取得する
     *
     * @param \App\Http\Requests\Tenant\Customers\IndexRequest $request
     * @param \App\UseCases\Tenant\Customers\IndexAction       $action
     *
     * @return \App\Http\Resources\Tenant\Customers\IndexCollection
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function index(
        IndexRequest $request,
        IndexAction $action,
    ): IndexCollection {
        $user = $request->user();
        /** @var \App\Auth\GenericUser $user */
        $this->identifiedTenant = $this->userOrganizationService->getTenantByOrganizationCode(
            $user->token,
            $user->sub,
        );

        return new IndexCollection(
            $action(
                IsoLanguageCode::getLocaleIso639_1(),
                $this->identifiedTenant->tenant_id,
                $request->validated('organizationCode'),
                $request->validated('customerName'),
                $request->validated('customerStatusCode'),
                $request->validated('servicePublicId'),
                $request->validated('servicePlanPublicId'),
                $request->validated('displayed'),
                $request->validated('page'),
            ),
        );
    }
}
