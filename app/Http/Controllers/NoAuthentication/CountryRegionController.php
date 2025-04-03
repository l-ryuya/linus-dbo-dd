<?php

namespace App\Http\Controllers\NoAuthentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountryRegion\IndexRequest;
use App\Http\Resources\CountryRegion\IndexResource;
use App\Services\LocaleService;
use App\UseCases\CountryRegion\IndexAction;

class CountryRegionController extends Controller
{
    /**
     * 国・地域一覧を取得する
     *
     * @param \App\Http\Requests\CountryRegion\IndexRequest $request
     * @param \App\UseCases\CountryRegion\IndexAction       $action
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(
        IndexRequest $request,
        IndexAction $action,
    ): \Illuminate\Http\Resources\Json\AnonymousResourceCollection {
        return IndexResource::collection(
            $action(
                LocaleService::getLocaleIso639_1(),
                $request->validated('country_code_alpha3'),
                $request->validated('country_code_alpha2'),
                $request->validated('country_code_numeric'),
                $request->validated('displayed', 10),
                $request->validated('page'),
            ),
        );
    }
}
