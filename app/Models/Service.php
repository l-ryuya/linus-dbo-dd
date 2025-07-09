<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'service_id';

    protected function casts(): array
    {
        return [
            'service_start_date' => 'date',
            'service_end_date' => 'date',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<ServiceTranslation, $this>
     */
    public function serviceTranslations(): HasOne
    {
        return $this->hasOne(ServiceTranslation::class, 'service_id', 'service_id');
    }

    /**
     * 特定の言語コードのサービス名翻訳を取得
     *
     * @param string $languageCode
     * @return ServiceTranslation|null
     */
    public function nameTranslation(string $languageCode): ?ServiceTranslation
    {
        return $this->serviceTranslations()->where('language_code', $languageCode)->first();
    }
}
