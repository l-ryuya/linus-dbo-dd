<?php

declare(strict_types=1);

namespace App\UseCases\ServicePlan;

use App\Enums\Service\ServicePlanStatusCode;
use App\Enums\Service\ServiceStatusCode;
use App\Models\Service;
use App\Models\ServicePlan;

class IndexAction
{
    /**
     * サービスプランを取得する
     *
     * @param string                                   $languageCode 言語コード（ISO639-1）
     * @param int|null                                 $tenantId
     * @param string                                   $servicePublicId
     * @param \App\Enums\Service\ServiceStatusCode     $serviceStatusCode
     * @param \App\Enums\Service\ServicePlanStatusCode $servicePlanStatusCode
     *
     * @return \Illuminate\Support\Collection<int, ServicePlan>
     */
    public function __invoke(
        string $languageCode,
        ?int $tenantId,
        string $servicePublicId,
        ServiceStatusCode $serviceStatusCode = ServiceStatusCode::Active,
        ServicePlanStatusCode $servicePlanStatusCode = ServicePlanStatusCode::Active,
    ): \Illuminate\Support\Collection {
        $service = Service::where('public_id', $servicePublicId)
            ->when($tenantId, function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })
            ->where('service_status_type', 'service_status')
            ->where('service_status_code', $serviceStatusCode->value)
            ->first();
        if (!$service) {
            return collect([]);
        }

        return ServicePlan::select([
            'service_plans.public_id',
            'service_plans.service_plan_status_code',
            'service_plans.billing_cycle',
            'service_plans.unit_price',
            'service_plans.service_plan_start_date',
            'service_plans.service_plan_end_date',
            'service_plan_translations.service_plan_name',
            'service_plan_translations.service_plan_description',
            'selection_item_translations.selection_item_name AS service_plan_status',
        ])
        ->join('service_plan_translations', function ($join) use ($languageCode) {
            $join->on('service_plans.service_plan_id', '=', 'service_plan_translations.service_plan_id')
                ->where('service_plan_translations.language_code', $languageCode);
        })
        ->join('selection_item_translations', function ($join) use ($languageCode) {
            $join->on('service_plans.service_plan_status_type', '=', 'selection_item_translations.selection_item_type')
                ->whereColumn('service_plans.service_plan_status_code', 'selection_item_translations.selection_item_code')
                ->where('selection_item_translations.language_code', $languageCode);
        })
        ->where('service_plans.service_id', $service->service_id)
        ->where('service_plans.service_plan_status_type', 'service_plan_status')
        ->where('service_plans.service_plan_status_code', $servicePlanStatusCode->value)
        ->get();
    }
}
