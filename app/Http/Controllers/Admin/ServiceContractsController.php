<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceContracts\IndexRequest;
use App\Http\Resources\Admin\ServiceContracts\IndexCollection;
use App\Http\Resources\Admin\ServiceContracts\ShowResource;
use App\Shared\Language\IsoLanguageCode;
use App\UseCases\Admin\ServiceContracts\IndexAction;
use App\UseCases\Admin\ServiceContracts\ShowAction;
use Illuminate\Http\Request;

class ServiceContractsController extends Controller
{
    /**
     * サービス契約一覧を取得する
     *
     * @param \App\Http\Requests\Admin\ServiceContracts\IndexRequest $request
     * @param \App\UseCases\Admin\ServiceContracts\IndexAction       $action
     *
     * @return \App\Http\Resources\Admin\ServiceContracts\IndexCollection
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

    /**
     * サービス契約詳細を取得する
     *
     * @param \Illuminate\Http\Request                               $request
     * @param \App\UseCases\Admin\ServiceContracts\ShowAction        $action
     *
     * @return \App\Http\Resources\Admin\ServiceContracts\ShowResource
     */
    public function show(
        Request $request,
        ShowAction $action,
    ): ShowResource {
        return new ShowResource(
            $action(
                IsoLanguageCode::getLocaleIso639_1(),
                $request->route('company_code'),
            ),
        );
    }
}
