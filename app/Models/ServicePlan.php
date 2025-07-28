<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServicePlan extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'service_plan_id';

    protected $fillable = [
        'contract_template_jp_id',
        'contract_template_en_id',
    ];

    protected function casts(): array
    {
        return [
            'service_plan_start_date' => 'date',
            'service_plan_end_date' => 'date',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ServicePlanTranslation, $this>
     */
    public function servicePlanTranslation(): HasMany
    {
        return $this->hasMany(ServicePlanTranslation::class, 'service_plan_id', 'service_plan_id');
    }

    /**
     * 特定の言語コードのサービス名翻訳を取得
     *
     * @param string $languageCode
     * @return ServicePlanTranslation|null
     */
    public function nameTranslation(string $languageCode): ?ServicePlanTranslation
    {
        return $this->servicePlanTranslation()->where('language_code', $languageCode)->first();
    }
}
