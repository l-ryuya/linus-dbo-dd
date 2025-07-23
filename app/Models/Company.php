<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'company_id';

    protected $fillable = [
        'public_id',
        'tenant_id',
        'company_name_en',
        'default_language_code',
        'country_code_alpha3',
        'website_url',
        'shareholders_url',
        'executives_url',
        'postal',
        'state',
        'city',
        'street',
        'building',
        'remarks',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<CompanyNameTranslation, $this>
     */
    public function companyNameTranslations(): HasOne
    {
        return $this->hasOne(CompanyNameTranslation::class, 'company_id', 'company_id');
    }

    /**
     * 特定の言語コードの会社名翻訳を取得
     *
     * @param string $languageCode
     * @return CompanyNameTranslation|null
     */
    public function nameTranslation(string $languageCode): ?CompanyNameTranslation
    {
        return $this->companyNameTranslations()->where('language_code', $languageCode)->first();
    }
}
