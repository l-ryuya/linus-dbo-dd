<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Customer\IndexRequest;
use App\Http\Requests\Tenant\Customer\StoreRequest;
use App\Http\Requests\Tenant\Customer\UpdateRequest;
use App\Http\Resources\NoContentResource;
use App\Http\Resources\Tenant\Customer\IndexCollection;
use App\Http\Resources\Tenant\Customer\ShowResource;
use App\Http\Resources\Tenant\Customer\StoreResource;
use App\Models\Tenant;
use App\Services\M5\UserOrganizationService;
use App\Shared\Language\IsoLanguageCode;
use App\UseCases\Tenant\Customer\IndexAction;
use App\UseCases\Tenant\Customer\ShowAction;
use App\UseCases\Tenant\Customer\StoreAction;
use App\UseCases\Tenant\Customer\UpdateAction;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected ?Tenant $identifiedTenant;

    private UserOrganizationService $userOrganizationService;

    public function __construct(
        UserOrganizationService $userOrganizationService,
    ) {
        parent::__construct();
        $this->userOrganizationService = $userOrganizationService;
    }

    /**
     * テナント管理者の顧客一覧を取得する
     *
     * @param \App\Http\Requests\Tenant\Customer\IndexRequest $request
     * @param \App\UseCases\Tenant\Customer\IndexAction       $action
     *
     * @return \App\Http\Resources\Tenant\Customer\IndexCollection
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

    /**
     * テナント管理者の顧客詳細を取得する
     *
     * @param \Illuminate\Http\Request                 $request
     * @param string                                   $publicId
     * @param \App\UseCases\Tenant\Customer\ShowAction $action
     *
     * @return \App\Http\Resources\Tenant\Customer\ShowResource
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function show(
        Request $request,
        string $publicId,
        ShowAction $action,
    ): ShowResource {
        $user = $request->user();
        /** @var \App\Auth\GenericUser $user */
        $this->identifiedTenant = $this->userOrganizationService->getTenantByOrganizationCode(
            $user->token,
            $user->sub,
        );

        return new ShowResource(
            $action(
                IsoLanguageCode::getLocaleIso639_1(),
                $this->identifiedTenant->tenant_id,
                $publicId,
            ),
        );
    }

    /**
     * テナント管理者が顧客登録をする
     *
     * @param \App\Http\Requests\Tenant\Customer\StoreRequest $request
     * @param \App\UseCases\Tenant\Customer\StoreAction       $action
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Throwable
     */
    public function store(
        StoreRequest $request,
        StoreAction $action,
    ): \Illuminate\Http\JsonResponse {
        $user = $request->user();
        /** @var \App\Auth\GenericUser $user */
        $this->identifiedTenant = $this->userOrganizationService->getTenantByOrganizationCode(
            $user->token,
            $user->sub,
        );

        return (new StoreResource(
            $action(
                $this->identifiedTenant,
                $request->toStoreInput(),
            ),
        ))
        ->response()
        ->setStatusCode(201);
    }

    /**
     * テナント管理者が顧客更新をする
     *
     * @param \App\Http\Requests\Tenant\Customer\UpdateRequest $request
     * @param string                                           $publicId
     * @param \App\UseCases\Tenant\Customer\UpdateAction       $action
     *
     * @return \App\Http\Resources\NoContentResource
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Throwable
     */
    public function update(
        UpdateRequest $request,
        string $publicId,
        UpdateAction $action,
    ): NoContentResource {
        $user = $request->user();
        /** @var \App\Auth\GenericUser $user */
        $this->identifiedTenant = $this->userOrganizationService->getTenantByOrganizationCode(
            $user->token,
            $user->sub,
        );

        $action(
            $this->identifiedTenant,
            $publicId,
            $request->toUpdateInput(),
        );

        return new NoContentResource();
    }
}
