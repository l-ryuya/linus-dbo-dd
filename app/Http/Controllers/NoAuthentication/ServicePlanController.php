<?php

declare(strict_types=1);

namespace App\Http\Controllers\NoAuthentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServicePlan\IndexRequest;
use App\Http\Resources\ServicePlan\IndexResource;
use App\Shared\Language\IsoLanguageCode;
use App\UseCases\ServicePlan\IndexAction;

class ServicePlanController extends Controller
{
    /**
     * 指定サービスコード毎のサービスプラン一覧を取得する
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
        return IndexResource::collection(
            $action(
                IsoLanguageCode::getLocaleIso639_1(),
                $request->validated('serviceCode'),
            ),
        );
    }
}
