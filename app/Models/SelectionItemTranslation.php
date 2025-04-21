<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static filterByTypeAndLanguage(?string $selectionItemType, string $languageCode)
 */
class SelectionItemTranslation extends Model
{
    use SoftDeletes;

    // 複合主キーをサポートしていない為、無効化させる
    protected $primaryKey = 'Not supported Composite Primary Key';

    public $incrementing = false;

    /**
     * @param Builder<SelectionItemTranslation> $query
     * @param string|null                     $selectionItemType
     * @param string                          $languageCode
     *
     * @return Builder<SelectionItemTranslation>
     */
    public function scopeFilterByTypeAndLanguage(
        Builder $query,
        ?string $selectionItemType,
        string $languageCode,
    ): Builder {
        return $query->withTrashed()
            ->when($selectionItemType, function ($query) use ($selectionItemType) {
                $query->where('selection_item_type', $selectionItemType);
            })
            ->where('language_code', $languageCode);
    }
}
