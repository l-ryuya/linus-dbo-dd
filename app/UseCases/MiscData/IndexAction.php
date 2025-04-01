<?php

namespace App\UseCases\MiscData;

use App\Models\SelectionItemTranslation;

class IndexAction
{
    /**
     * 選択肢アイテムから指定した言語の選択肢アイテムを取得する
     *
     * @param string $languageCode 言語コード
     * @param string $type 選択肢アイテム種別
     *
     * @return \Illuminate\Support\Collection
     */
    public function __invoke(
        string $languageCode,
        string $type,
    ): \Illuminate\Support\Collection {
        return SelectionItemTranslation::select([
            'selection_item_type',
            'selection_item_code',
            'language_code',
            'selection_item_name',
            'selection_item_short_name',
        ])
        ->where('selection_item_type', $type)
        ->where('language_code', $languageCode)
        ->get();
    }
}
