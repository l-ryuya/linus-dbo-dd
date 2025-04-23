<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DueDiligences\ResultSummaryResource;
use App\Http\Resources\Admin\DueDiligences\ShowResource;
use App\Shared\Language\IsoLanguageCode;
use App\UseCases\Admin\DueDiligences\ResultSummaryAction;
use App\UseCases\Admin\DueDiligences\ShowAction;
use Illuminate\Http\Request;

class DueDiligencesController extends Controller
{
    /**
     * デューデリジェンス詳細を取得する
     *
     * @param \Illuminate\Http\Request                     $request
     * @param \App\UseCases\Admin\DueDiligences\ShowAction $action
     *
     * @return \App\Http\Resources\Admin\DueDiligences\ShowResource
     */
    public function show(
        Request $request,
        ShowAction $action,
    ): ShowResource {
        return new ShowResource(
            $action(
                IsoLanguageCode::getLocaleIso639_1(),
                $request->route('dd_code'),
            ),
        );
    }

    /**
     * デューデリジェンス結果概要を取得する
     *
     * @param \Illuminate\Http\Request                              $request
     * @param \App\UseCases\Admin\DueDiligences\ResultSummaryAction $action
     *
     * @return \App\Http\Resources\Admin\DueDiligences\ResultSummaryResource
     */
    public function resultSummary(
        Request $request,
        ResultSummaryAction $action,
    ): ResultSummaryResource {
        return new ResultSummaryResource(
            $action(
                IsoLanguageCode::getLocaleIso639_1(),
                $request->route('dd_code'),
            ),
        );
    }
}
