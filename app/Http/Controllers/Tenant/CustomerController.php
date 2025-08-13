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
use App\Services\Role\TenantUserRoleService;
use App\UseCases\Tenant\Customer\IndexAction;
use App\UseCases\Tenant\Customer\ShowAction;
use App\UseCases\Tenant\Customer\StoreAction;
use App\UseCases\Tenant\Customer\UpdateAction;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * テナント管理者の顧客一覧を取得する
     *
     * @param \App\Http\Requests\Tenant\Customer\IndexRequest $request
     * @param \App\UseCases\Tenant\Customer\IndexAction       $action
     *
     * @return \App\Http\Resources\Tenant\Customer\IndexCollection
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
                $request->validated('customerName'),
                $request->validated('customerStatusCode'),
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
     * テナント管理者が顧客登録をする
     *
     * @param \App\Http\Requests\Tenant\Customer\StoreRequest $request
     * @param \App\UseCases\Tenant\Customer\StoreAction       $action
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
                $user->getUserOption()->user_option_id,
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
            $request->toUpdateInput(),
        );

        return new NoContentResource();
    }
}
