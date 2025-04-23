<?php

declare(strict_types=1);

namespace App\UseCases\Admin\DueDiligences;

use App\Models\DueDiligence;
use App\Models\SelectionItemTranslation;

class ShowAction
{
    /**
     * 管理者向けのデューデリジェンス詳細を取得する
     *
     * @param string $languageCode
     * @param string $ddCode
     *
     * @return \App\Models\DueDiligence
     */
    public function __invoke(
        string $languageCode,
        string $ddCode,
    ): DueDiligence {
        $dueDiligence = DueDiligence::select([
            'dd_code',
            'company_name',
            'dd_status_type',
            'dd_status',
            'ai_dd_result',
            'ai_dd_completed_date',
            'primary_dd_result',
            'primary_dd_user_id',
            'primary_dd_completed_date',
            'primary_dd_comment',
            'final_dd_result',
            'final_dd_user_id',
            'final_dd_completed_date',
            'final_dd_comment',
        ])
        ->where('dd_code', $ddCode)
        ->firstOrFail();

        $statuses = SelectionItemTranslation::filterByTypeAndLanguage($dueDiligence->dd_status_type, $languageCode)->get();

        $dueDiligence->setAttribute(
            'dd_status',
            $statuses->firstWhere('selection_item_code', $dueDiligence->dd_status)?->selection_item_name,
        );

        return $dueDiligence;
    }
}
