<?php

declare(strict_types=1);

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MiscData\IndexRequest;
use App\Http\Resources\MiscData\IndexResource;
use App\UseCases\MiscData\IndexAction;

class MiscDataController extends Controller
{
    /**
     * 指定アイテム種別毎の選択肢アイテム一覧を取得する
     *
     * @param \App\Http\Requests\MiscData\IndexRequest $request
     * @param \App\UseCases\MiscData\IndexAction       $action
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
                $request->validated('type'),
            ),
        );
    }
}
