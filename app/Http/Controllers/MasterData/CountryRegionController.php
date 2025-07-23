<?php

declare(strict_types=1);

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountryRegion\IndexRequest;
use App\Http\Resources\CountryRegion\IndexCollection;
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
    ): IndexCollection {
        /** @var \App\Auth\GenericUser $user */
        $user = $request->user();

        return new IndexCollection(
            $action(
                $user->getUserOption()->language_code,
                $request->validated('countryCodeAlpha3'),
                $request->validated('countryCodeAlpha2'),
                $request->validated('countryCodeNumeric'),
            ),
        );
    }
}
