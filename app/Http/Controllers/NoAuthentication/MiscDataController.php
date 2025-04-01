<?php

namespace App\Http\Controllers\NoAuthentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\MiscData\IndexRequest;
use App\Http\Resources\MiscData\IndexResource;
use App\Services\LocaleService;
use App\UseCases\MiscData\IndexAction;

class MiscDataController extends Controller
{
    /**
     * 選択肢アイテム種別毎の一覧を取得する
     *
     * @param \App\Http\Requests\MiscData\IndexRequest $request
     * @param \App\UseCases\MiscData\IndexAction       $action
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request, IndexAction $action): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return IndexResource::collection($action(
            LocaleService::getLocaleIso639_1(),
            $request->validated('type'),
        ));
    }
}
