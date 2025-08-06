<?php

declare(strict_types=1);

namespace App\UseCases\Service;

use App\Enums\ServiceStatusCode;
use App\Models\Service;

class IndexAction
{
    /**
     * サービスを取得する
     *
     * @param string                       $languageCode 言語コード（ISO639-1）
     * @param int|null                     $tenantId
     * @param \App\Enums\ServiceStatusCode $serviceStatusCode
     *
     * @return \Illuminate\Support\Collection<int, Service>
     */
    public function __invoke(
        string $languageCode,
        ?int $tenantId,
        ServiceStatusCode $serviceStatusCode = ServiceStatusCode::Active,
    ): \Illuminate\Support\Collection {
        return Service::select([
            'services.public_id',
            'services.service_status_code',
            'services.service_start_date',
            'services.service_end_date',
            'services.service_condition',
            'services.dd_plan',
            'service_translations.service_name',
            'service_translations.service_description',
            'selection_item_translations.selection_item_name AS service_status',
        ])
        ->join('service_translations', function ($join) use ($languageCode) {
            $join->on('services.service_id', '=', 'service_translations.service_id')
                ->where('service_translations.language_code', $languageCode);
        })
        ->join('selection_item_translations', function ($join) use ($languageCode) {
            $join->on('services.service_status_type', '=', 'selection_item_translations.selection_item_type')
                ->whereColumn('services.service_status_code', 'selection_item_translations.selection_item_code')
                ->where('selection_item_translations.language_code', $languageCode);
        })
        ->when($tenantId, function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })
        ->where('service_status_type', 'service_status')
        ->where('service_status_code', $serviceStatusCode->value)
        ->get();
    }
}
