<?php
declare(strict_types = 1);

namespace App\UseCases\ServicePlan;

use App\Enums\ServicePlanStatus;
use App\Enums\ServiceStatus;
use App\Models\Service;
use App\Models\ServicePlan;

class IndexAction
{
    /**
     * サービスプランを取得する
     *
     * @param string                       $languageCode          言語コード（ISO639-1）
     * @param string                       $serviceCode           サービスコード
     * @param \App\Enums\ServiceStatus     $serviceStatusCode     サービス提供ステータス
     * @param \App\Enums\ServicePlanStatus $servicePlanStatusCode サービスプラン提供ステータス
     *
     * @return \Illuminate\Support\Collection<int, ServicePlan>
     */
    public function __invoke(
        string $languageCode,
        string $serviceCode,
        ServiceStatus $serviceStatusCode = ServiceStatus::Active,
        ServicePlanStatus $servicePlanStatusCode = ServicePlanStatus::Active,
    ): \Illuminate\Support\Collection {
        $service = Service::where('service_code', $serviceCode)
            ->where('service_status_type', 'service_status')
            ->where('service_status_code', $serviceStatusCode->value)
            ->first();
        if (!$service) {
            return collect([]);
        }

        return ServicePlan::select([
            'service_plans.service_code',
            'service_plans.service_plan_code',
            'service_plans.service_plan_status_type',
            'service_plans.service_plan_status',
            'service_plans.billing_cycle',
            'service_plans.unit_price',
            'service_plans.service_start_date',
            'service_plans.service_end_date',
            'service_plan_translations.service_plan_name',
            'service_plan_translations.service_plan_description',
        ])
        ->join('service_plan_translations', function($join) use ($languageCode) {
            $join->on('service_plans.service_plan_code', '=', 'service_plan_translations.service_plan_code')
                ->where('service_plan_translations.language_code', $languageCode);
        })
        ->where('service_plans.service_code', $service->service_code)
        ->where('service_plans.service_plan_status_type', 'service_plan_status')
        ->where('service_plans.service_plan_status', $servicePlanStatusCode->value)
        ->get();
    }
}
