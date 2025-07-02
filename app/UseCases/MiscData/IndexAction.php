<?php

declare(strict_types=1);

namespace App\UseCases\MiscData;

use App\Models\SelectionItemTranslation;

class IndexAction
{
    /**
     * 選択肢アイテムを取得する
     *
     * @param string      $languageCode      言語コード（ISO639-1）
     * @param string|null $selectionItemType 選択肢アイテム種別
     *
     * @return \Illuminate\Support\Collection<int, SelectionItemTranslation>
     */
    public function __invoke(
        string $languageCode,
        ?string $selectionItemType,
    ): \Illuminate\Support\Collection {
        return SelectionItemTranslation::select([
            'selection_item_type',
            'selection_item_code',
            'selection_item_name',
            'selection_item_short_name',
        ])
        ->when($selectionItemType, function ($query) use ($selectionItemType) {
            $query->where('selection_item_type', $selectionItemType);
        })
        ->where('language_code', $languageCode)
        ->get();
    }
}
