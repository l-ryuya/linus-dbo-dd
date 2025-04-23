<?php

declare(strict_types=1);

namespace App\UseCases\Admin\DueDiligences;

use App\Models\DueDiligence;
use App\Models\SelectionItemTranslation;

class ResultSummaryAction
{
    /**
     * デューデリジェンス結果概要を取得する
     *
     * @param string $languageCode
     * @param string $ddCode
     *
     * @return object
     */
    public function __invoke(
        string $languageCode,
        string $ddCode,
    ): object {
        $dueDiligence = DueDiligence::select([
            'dd_id',
            'dd_code',
            'company_name',
            'dd_status_type',
            'dd_status',
            'dd_entity_type_type',
            'dd_entity_type_code',
        ])
        ->where('dd_code', $ddCode)
        ->firstOrFail();

        if (
            $dueDiligence->dd_entity_type_type !== 'dd_entity_type' ||
            $dueDiligence->dd_entity_type_code !== 'target_company'
        ) {
            abort(403);
        }

        $statuses = SelectionItemTranslation::filterByTypeAndLanguage(null, $languageCode)->get();

        $dueDiligence->setAttribute(
            'dd_status',
            $statuses->where('selection_item_type', $dueDiligence->dd_status_type)
                ->where('selection_item_code', $dueDiligence->dd_status)
                ->first()?->selection_item_name,
        );

        $summaries = DueDiligence::select([
            'dd_code',
            'company_name',
            'dd_entity_type_type',
            'dd_entity_type_code',
            'dd_relation_type_type',
            'dd_relation_type_code',
            'individual_last_name',
            'individual_middle_name',
            'individual_first_name',
            'position',
            'investment_sources',
            'investment_targets',
            'shareholding_ratio',
        ])
        ->where('target_company_dd_id', $dueDiligence->dd_id)
        ->orderBy('dd_code')
        ->get();

        $summaries->map(function ($item) use ($statuses) {
            $item->setAttribute(
                'dd_entity_type',
                $statuses->where('selection_item_type', $item->dd_entity_type_type)
                    ->where('selection_item_code', $item->dd_entity_type_code)
                    ->first()?->selection_item_name,
            );

            $dd_relation_type = [];
            foreach (explode(',', $item->dd_relation_type_code) as $code) {
                $dd_relation_type[] = $statuses->where('selection_item_type', $item->dd_relation_type_type)
                    ->where('selection_item_code', $code)
                    ->first()?->selection_item_name;
            }

            $item->setAttribute('dd_relation_type', implode(',', $dd_relation_type));

            return $item;
        });

        return (object) [
            'self' => $dueDiligence,
            'summaries' => $summaries,
        ];
    }
}
