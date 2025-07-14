<?php

declare(strict_types=1);

namespace App\UseCases\Tenant\ServiceContract;

use App\Models\SelectionItemTranslation;
use App\Models\ServiceContract;

class IndexAction
{
    /**
     * 顧客サービス契約一覧取得
     *
     * @param string      $languageCode 言語コード（ISO639-1）
     * @param int|null    $tenantId
     * @param string|null $tenantName
     * @param string|null $servicePublicId
     * @param string|null $servicePlanPublicId
     * @param string|null $customerName
     * @param string|null $contractName
     * @param string|null $contractStatusCode
     * @param string|null $serviceUsageStatusCode
     * @param string|null $contractDate
     * @param string|null $contractStartDate
     * @param int         $displayedNumber 表示件数
     * @param int         $page ページ番号
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, ServiceContract>
     */
    public function __invoke(
        string $languageCode,
        ?int $tenantId,
        ?string $tenantName,
        ?string $servicePublicId,
        ?string $servicePlanPublicId,
        ?string $customerName,
        ?string $contractName,
        ?string $contractStatusCode,
        ?string $serviceUsageStatusCode,
        ?string $contractDate,
        ?string $contractStartDate,
        int $displayedNumber,
        int $page,
    ): \Illuminate\Pagination\LengthAwarePaginator {
        $paginator = ServiceContract::select([
            'tenants.tenant_name',
            'service_translations.service_name',
            'service_plan_translations.service_plan_name',
            'company_name_translations.company_legal_name',
            'companies.company_name_en',
            'service_contracts.contract_name',
            'service_contracts.contract_status_type',
            'service_contracts.contract_status_code',
            'service_contracts.service_usage_status_type',
            'service_contracts.service_usage_status_code',
            'service_contracts.contract_date',
            'service_contracts.contract_start_date',
        ])
        ->join('tenants', 'tenants.tenant_id', '=', 'service_contracts.tenant_id')
        ->join('customers', 'customers.customer_id', '=', 'service_contracts.customer_id')
        ->join('companies', 'companies.company_id', '=', 'customers.company_id')
        ->join('company_name_translations', function ($join) {
            $join->on('customers.company_id', '=', 'company_name_translations.company_id')
                ->whereColumn('company_name_translations.language_code', 'companies.default_language_code');
        })
        ->join('services', 'service_contracts.service_id', '=', 'services.service_id')
        ->join('service_translations', function ($join) use ($languageCode) {
            $join->on('service_contracts.service_id', '=', 'service_translations.service_id')
                ->where('service_translations.language_code', $languageCode);
        })
        ->join('service_plans', 'service_contracts.service_plan_id', '=', 'service_plans.service_plan_id')
        ->join('service_plan_translations', function ($join) use ($languageCode) {
            $join->on('service_contracts.service_plan_id', '=', 'service_plan_translations.service_plan_id')
                ->where('service_plan_translations.language_code', $languageCode);
        })
        ->when($tenantId, function ($query) use ($tenantId) {
            $query->where('service_contracts.tenant_id', $tenantId);
        })
        ->when($tenantName, function ($query) use ($tenantName) {
            $query->where('tenants.tenant_name', 'LIKE', "%{$tenantName}%");
        })
        ->when($servicePublicId, function ($query) use ($servicePublicId) {
            $query->where('services.public_id', $servicePublicId);
        })
        ->when($servicePlanPublicId, function ($query) use ($servicePlanPublicId) {
            $query->where('service_plans.public_id', $servicePlanPublicId);
        })
        ->when($customerName, function ($query) use ($customerName) {
            $query->where('company_name_translations.company_legal_name', 'LIKE', "%{$customerName}%")
                ->orWhere('companies.company_name_en', 'LIKE', "%{$customerName}%");
        })
        ->when($contractName, function ($query) use ($contractName) {
            $query->where('service_contracts.contract_name', 'LIKE', "%{$contractName}%");
        })
        ->when($contractStatusCode, function ($query) use ($contractStatusCode) {
            $query->where('service_contracts.contract_status_type', 'service_contract_status')
                ->where('service_contracts.contract_status_code', $contractStatusCode);
        })
        ->when($serviceUsageStatusCode, function ($query) use ($serviceUsageStatusCode) {
            $query->where('service_contracts.service_usage_status_type', 'service_usage_status')
                ->where('service_contracts.service_usage_status_code', $serviceUsageStatusCode);
        })
        ->when($contractDate, function ($query) use ($contractDate) {
            $query->whereDate('service_contracts.contract_date', $contractDate);
        })
        ->when($contractStartDate, function ($query) use ($contractStartDate) {
            $query->whereDate('service_contracts.contract_start_date', $contractStartDate);
        })
        ->orderBy('service_contracts.service_contract_id', 'DESC')
        ->paginate(perPage: $displayedNumber, page: $page);

        $contractStatuses = SelectionItemTranslation::filterByTypeAndLanguage('service_contract_status', $languageCode)->get();
        $usageStatuses = SelectionItemTranslation::filterByTypeAndLanguage('service_usage_status', $languageCode)->get();

        $paginator->map(function ($item) use ($contractStatuses, $usageStatuses) {
            $item->setAttribute('contract_status', $contractStatuses->firstWhere('selection_item_code', $item->contract_status_code)->selection_item_name);
            $item->setAttribute('service_usage_status', $usageStatuses->firstWhere('selection_item_code', $item->service_usage_status_code)->selection_item_name);

            return $item;
        });

        return $paginator;
    }
}
