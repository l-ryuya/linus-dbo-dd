<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Companies\IndexRequest;
use App\Http\Resources\Admin\Companies\IndexCollection;
use App\Shared\Language\IsoLanguageCode;
use App\UseCases\Admin\Companies\IndexAction;

class CompaniesController extends Controller
{
    /**
     * クライアント一覧を取得する
     *
     * @param \App\Http\Requests\Admin\Companies\IndexRequest $request
     * @param \App\UseCases\Admin\Companies\IndexAction       $action
     *
     * @return \App\Http\Resources\Admin\Companies\IndexCollection
     */
    public function index(
        IndexRequest $request,
        IndexAction $action,
    ): IndexCollection {
        return new IndexCollection(
            $action(
                IsoLanguageCode::getLocaleIso639_1(),
                $request->validated('companyName'),
                $request->validated('companyStatusCode'),
                $request->serviceSignupStartDate,
                $request->serviceSignupEndDate,
                $request->validated('displayed'),
                $request->validated('page'),
            ),
        );
    }
}
