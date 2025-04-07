<?php

namespace App\Http\Controllers\NoAuthentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountryRegion\IndexRequest;
use App\Http\Resources\CountryRegion\IndexCollection;
use App\Shared\Language\IsoLanguageCode;
use App\UseCases\CountryRegion\IndexAction;

class CountryRegionController extends Controller
{
    /**
     * 国・地域一覧を取得する
     *
     * @param \App\Http\Requests\CountryRegion\IndexRequest $request
     * @param \App\UseCases\CountryRegion\IndexAction       $action
     *
     * @return \App\Http\Resources\CountryRegion\IndexCollection
     */
    public function index(
        IndexRequest $request,
        IndexAction $action,
    ): \App\Http\Resources\CountryRegion\IndexCollection {
        return new IndexCollection(
            $action(
                IsoLanguageCode::getLocaleIso639_1(),
                $request->validated('countryCodeAlpha3'),
                $request->validated('countryCodeAlpha2'),
                $request->validated('countryCodeNumeric'),
                $request->validated('displayed', 10),
                $request->validated('page'),
            ),
        );
    }
}
