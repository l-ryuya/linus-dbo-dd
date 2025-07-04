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

    protected $primaryKey = 'selection_item_translation_id';

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
