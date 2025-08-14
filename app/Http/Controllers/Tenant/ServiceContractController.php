<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Enums\Service\ServiceContractStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ServiceContract\IndexRequest;
use App\Http\Requests\Tenant\ServiceContract\StoreDraftRequest;
use App\Http\Requests\Tenant\ServiceContract\StoreRequest;
use App\Http\Requests\Tenant\ServiceContract\UpdateDraftRequest;
use App\Http\Requests\Tenant\ServiceContract\UpdateRequest;
use App\Http\Resources\NoContentResource;
use App\Http\Resources\Tenant\ServiceContract\CloudsignStatusSyncResource;
use App\Http\Resources\Tenant\ServiceContract\IndexCollection;
use App\Http\Resources\Tenant\ServiceContract\ShowResource;
use App\Http\Resources\Tenant\ServiceContract\StoreResource;
use App\Services\Role\TenantUserRoleService;
use App\UseCases\Tenant\ServiceContract\CloudsignStatusSyncAction;
use App\UseCases\Tenant\ServiceContract\IndexAction;
use App\UseCases\Tenant\ServiceContract\ShowAction;
use App\UseCases\Tenant\ServiceContract\StoreAction;
use App\UseCases\Tenant\ServiceContract\UpdateAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceContractController extends Controller
{
    /**
     * テナント管理者 顧客サービス契約一覧を取得する
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

    /**
     * テナント管理者 顧客サービス契約詳細を取得する
     *
     * @param \Illuminate\Http\Request                        $request
     * @param string                                          $publicId
     * @param \App\UseCases\Tenant\ServiceContract\ShowAction $action
     *
     * @return \App\Http\Resources\Tenant\ServiceContract\ShowResource
     */
    public function show(
        Request $request,
        string $publicId,
        ShowAction $action,
    ): ShowResource {
        /** @var \App\Auth\GenericUser $user */
        $user = $request->user();

        return new ShowResource(
            $action(
                $user->getUserOption()->language_code,
                (new TenantUserRoleService($user->getUserOption()))->getTenantId(),
                $publicId,
            ),
        );
    }

    /**
     * テナント管理者 顧客サービス契約を登録する
     *
     * @param \App\Http\Requests\Tenant\ServiceContract\StoreRequest $request
     * @param \App\UseCases\Tenant\ServiceContract\StoreAction       $action
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store(
        StoreRequest $request,
        StoreAction $action,
    ): \Illuminate\Http\JsonResponse {
        /** @var \App\Auth\GenericUser $user */
        $user = $request->user();

        return (new StoreResource(
            $action(
                $user->getUserOption()->tenant,
                ServiceContractStatusCode::ContractInfoRegistered,
                $request->toStoreInput(),
            ),
        ))
        ->response()
        ->setStatusCode(201);
    }

    /**
     * テナント管理者 顧客サービス契約を下書き登録する
     *
     * @param \App\Http\Requests\Tenant\ServiceContract\StoreDraftRequest $request
     * @param \App\UseCases\Tenant\ServiceContract\StoreAction            $action
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function storeDraft(
        StoreDraftRequest $request,
        StoreAction $action,
    ): \Illuminate\Http\JsonResponse {
        /** @var \App\Auth\GenericUser $user */
        $user = $request->user();

        return (new StoreResource(
            $action(
                $user->getUserOption()->tenant,
                ServiceContractStatusCode::ContractInfoDrafted,
                $request->toStoreInput(),
            ),
        ))
        ->response()
        ->setStatusCode(201);
    }

    /**
     * テナント管理者 顧客サービス契約を更新する
     *
     * @param \App\Http\Requests\Tenant\ServiceContract\UpdateRequest $request
     * @param string                                                  $publicId
     * @param \App\UseCases\Tenant\ServiceContract\UpdateAction       $action
     *
     * @return \App\Http\Resources\NoContentResource
     * @throws \Throwable
     */
    public function update(
        UpdateRequest $request,
        string $publicId,
        UpdateAction $action,
    ): NoContentResource {
        /** @var \App\Auth\GenericUser $user */
        $user = $request->user();

        $action(
            $user->getUserOption()->tenant,
            $publicId,
            ServiceContractStatusCode::ContractInfoRegistered,
            $request->toUpdateInput(),
        );

        return new NoContentResource();
    }

    /**
     * テナント管理者 顧客サービス契約の下書きを更新する
     *
     * @param \App\Http\Requests\Tenant\ServiceContract\UpdateDraftRequest $request
     * @param string                                                       $publicId
     * @param \App\UseCases\Tenant\ServiceContract\UpdateAction            $action
     *
     * @return \App\Http\Resources\NoContentResource
     * @throws \Throwable
     */
    public function updateDraft(
        UpdateDraftRequest $request,
        string $publicId,
        UpdateAction $action,
    ): NoContentResource {
        /** @var \App\Auth\GenericUser $user */
        $user = $request->user();

        $action(
            $user->getUserOption()->tenant,
            $publicId,
            ServiceContractStatusCode::ContractInfoDrafted,
            $request->toUpdateInput(),
        );

        return new NoContentResource();
    }

    /**
     * テナント管理者 顧客サービス契約のステータスを同期する
     *
     * @param \Illuminate\Http\Request                                       $request
     * @param string                                                         $publicId
     * @param \App\UseCases\Tenant\ServiceContract\CloudsignStatusSyncAction $action
     *
     * @return \App\Http\Resources\Tenant\ServiceContract\CloudsignStatusSyncResource
     * @throws \App\Exceptions\LogicValidationException
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Throwable
     */
    public function cloudsignStatusSync(
        Request $request,
        string $publicId,
        CloudsignStatusSyncAction $action,
    ): CloudsignStatusSyncResource {
        /** @var \App\Auth\GenericUser $user */
        $user = $request->user();

        return new CloudsignStatusSyncResource(
            $action(
                $user->getUserOption()->language_code,
                (new TenantUserRoleService($user->getUserOption()))->getTenantId(),
                $publicId,
            ),
        );
    }
}
