<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static withLanguage(string $languageCode)
 */
class ServicePlanTranslation extends Model
{
    use SoftDeletes;

    /**
     * @param  Builder<ServicePlanTranslation>  $query
     * @param  string   $languageCode
     * @return Builder<ServicePlanTranslation>
     */
    public function scopeWithLanguage(Builder $query, string $languageCode): Builder
    {
        return $query->where('language_code', $languageCode);
    }
}
