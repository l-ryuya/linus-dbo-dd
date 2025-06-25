<?php

declare(strict_types=1);

namespace App\UseCases\Tenant\Customer;

use App\Models\Customer;
use App\Models\SelectionItemTranslation;

class IndexAction
{
    /**
     * テナント管理者の顧客一覧を取得する
     *
     * @param string      $languageCode $languageCode 言語コード（ISO639-1）
     * @param int         $tenantId
     * @param string|null $organizationCode
     * @param string|null $customerName
     * @param string|null $customerStatusCode
     * @param string|null $servicePublicId
     * @param string|null $servicePlanPublicId
     * @param int         $displayedNumber $displayedNumber 表示件数
     * @param int         $page $page ページ番号
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, Customer>
     */
    public function __invoke(
        string $languageCode,
        int $tenantId,
        ?string $organizationCode,
        ?string $customerName,
        ?string $customerStatusCode,
        ?string $servicePublicId,
        ?string $servicePlanPublicId,
        int $displayedNumber,
        int $page,
    ): \Illuminate\Pagination\LengthAwarePaginator {
        $paginator = Customer::select([
            'customers.public_id',
            'customers.customer_status_type',
            'customers.customer_status_code',
            'customers.created_at',
            'company_name_translations.legal_name',
            'service_contracts.service_id',
            'service_contracts.service_plan_id',
            'service_contracts.contract_start_date',
            'service_translations.service_name',
            'service_plan_translations.service_plan_name',
        ])
        ->join('companies', 'companies.company_id', '=', 'customers.company_id')
        ->join('company_name_translations', function ($join) use ($languageCode) {
            $join->on('customers.company_id', '=', 'company_name_translations.company_id')
                ->where('company_name_translations.language_code', $languageCode);
        })
        ->leftJoin('service_contracts', 'service_contracts.customer_id', '=', 'customers.customer_id')
        ->leftJoin('service_translations', function ($join) use ($languageCode) {
            $join->on('service_contracts.service_id', '=', 'service_translations.service_id')
                ->where('service_translations.language_code', $languageCode);
        })
        ->leftJoin('service_plan_translations', function ($join) use ($languageCode) {
            $join->on('service_contracts.service_plan_id', '=', 'service_plan_translations.service_plan_id')
                ->where('service_plan_translations.language_code', $languageCode);
        })
        ->where('customers.tenant_id', $tenantId)
        ->when($organizationCode, function ($query) use ($organizationCode) {
            $query->where('customers.sys_organization_code', $organizationCode);
        })
        ->when($customerName, function ($query) use ($customerName) {
            $query->where('company_name_translations.legal_name', 'LIKE', "%{$customerName}%");
        })
        ->when($customerStatusCode, function ($query) use ($customerStatusCode) {
            $query->where('customers.customer_status_type', 'customer_status')
                ->where('customers.customer_status_code', $customerStatusCode);
        })
        ->orderBy('customers.created_at', 'DESC')
        ->paginate(perPage: $displayedNumber, page: $page);

        $statuses = SelectionItemTranslation::filterByTypeAndLanguage('customer_status', $languageCode)->get();

        $paginator->map(function ($item) use ($statuses) {
            $item->setAttribute('customer_status', $statuses->firstWhere('selection_item_code', $item->customer_status_code)->selection_item_name);

            return $item;
        });

        return $paginator;
    }
}
