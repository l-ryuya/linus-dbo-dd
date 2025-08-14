<?php

declare(strict_types=1);

namespace App\Services\AiDd;

use App\Models\DdCase;
use App\Models\SelectionItemTranslation;
use Illuminate\Support\Collection;
use stdClass;

class DdStepListService
{
    /**
     * DDステップのリストを取得
     *
     * @param string $languageCode
     * @return Collection<int, object{stepNumber: int, ddStepCode: string, stepName: string, stepInfo: ''}&stdClass>
     */
    public static function getStepList(
        string $languageCode,
    ): Collection {
        return SelectionItemTranslation::where('selection_item_type', 'dd_step')
            ->where('language_code', $languageCode)
            ->orderBy('display_order')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'stepNumber' => $item->display_order,
                    'ddStepCode' => $item->selection_item_code,
                    'stepName' => $item->selection_item_name,
                    'stepInfo' => '',
                ];
            });
    }

    /**
     * DDケースのステップ情報をマージする
     *
     * @param \App\Models\DdCase $ddCase
     * @param \Illuminate\Support\Collection<int, object{stepNumber: int, ddStepCode: string, stepName: string, stepInfo: ''}&stdClass> $stepList
     *
     * @return \Illuminate\Support\Collection<int, object{stepNumber: int, ddStepCode: string, stepName: string, stepInfo: ''}&stdClass>
     */
    public static function mergeStepList(
        DdCase $ddCase,
        Collection $stepList,
    ): Collection {
        return $stepList->map(function ($item) use ($ddCase) {
            $property = "step_" . ($item->stepNumber + 1) . "_info";
            $item->stepInfo = $ddCase->$property ?? '';
            return $item;
        });
    }
}
