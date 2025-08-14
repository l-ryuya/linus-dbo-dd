<?php

declare(strict_types=1);

namespace App\Services\AiDd;

use App\Enums\Dd\DdResultTypeCode;
use App\Models\DdStepResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DdStepResultService
{
    /**
     * dd_result_type_code毎に最新のステップ結果を取得
     *
     * @param int $ddCaseId
     * @param int $ddEntityId
     *
     * @return \Illuminate\Support\Collection<int, DdStepResult>
     */
    public static function fetchLatestCheckStatusList(
        int $ddCaseId,
        int $ddEntityId,
    ): Collection {
        $ranked = DdStepResult::selectRaw('
                dd_step_results.*,
                ROW_NUMBER() OVER (
                    PARTITION BY dd_result_type_code
                    ORDER BY
                        (step_result_completed_at IS NULL), step_result_completed_at DESC
                ) as latest_rank
            ')
            ->where('dd_case_id', $ddCaseId)
            ->where('dd_entity_id', $ddEntityId);

        return DdStepResult::query()
            ->fromSub($ranked, 'dd_step_results')
            ->where('dd_step_results.latest_rank', 1)
            ->orderBy('dd_step_results.dd_result_type_code')
            ->get();
    }

    /**
     * 最新のステップ結果ステータスをまとめる
     *
     * @param \Illuminate\Support\Collection<int, DdStepResult> $ddStepResults
     *
     * @return object
     */
    public static function buildLatestCheckStatusObject(
        Collection $ddStepResults,
    ): object {
        $result = [];
        foreach ($ddStepResults as $ddStepResult) {
            $upper = Str::upper($ddStepResult->dd_result_code_code ?? '') ?: null;
            $result[$ddStepResult->dd_result_type_code] = $upper;

            // リスクレベル判定の結果から市場と証券コードを取得
            if ($ddStepResult->dd_result_type_code == DdResultTypeCode::CustomerRiskLevel->value) {
                $result['exchange_name'] = $ddStepResult->exchange_name;
                $result['securities_code'] = $ddStepResult->securities_code;
            }
        }

        return (object) $result;
    }
}
