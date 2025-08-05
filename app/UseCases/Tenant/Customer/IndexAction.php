<?php

declare(strict_types=1);

namespace App\UseCases\Tenant\Customer;

use App\Models\Customer;

class IndexAction
{
    /**
     * 顧客一覧取得
     *
     * @param string      $languageCode 言語コード（ISO639-1）
     * @param int|null    $tenantId
     * @param string|null $customerName
     * @param string|null $customerStatusCode
     * @param int         $displayedNumber 表示件数
     * @param int         $page ページ番号
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, Customer>
     */
    public function __invoke(
        string $languageCode,
        ?int $tenantId,
        ?string $customerName,
        ?string $customerStatusCode,
        int $displayedNumber,
        int $page,
    ): \Illuminate\Pagination\LengthAwarePaginator {
        return Customer::select([
            'customers.public_id',
            'selection_item_translations.selection_item_name AS customer_status',
            'customers.customer_status_type',
            'customers.customer_status_code',
            'companies.company_name_en',
            'company_name_translations.company_legal_name',
            'customers.first_service_start_date',
            'customers.last_service_end_date',
        ])
        ->join('companies', 'companies.company_id', '=', 'customers.company_id')
        ->join('company_name_translations', function ($join) {
            $join->on('customers.company_id', '=', 'company_name_translations.company_id')
                ->whereColumn('company_name_translations.language_code', 'companies.default_language_code');
        })
        ->join('selection_item_translations', function ($join) use ($languageCode) {
            $join->on('customers.customer_status_code', 'selection_item_translations.selection_item_code')
                ->where('selection_item_translations.selection_item_type', 'customer_status')
                ->where('selection_item_translations.language_code', $languageCode);
        })
        ->when($tenantId, function ($query) use ($tenantId) {
            $query->where('customers.tenant_id', $tenantId);
        })
        ->when($customerName, function ($query) use ($customerName) {
            $query->where('company_name_translations.company_legal_name', 'ILIKE', "%{$customerName}%")
                ->orWhere('companies.company_name_en', 'ILIKE', "%{$customerName}%");
        })
        ->when($customerStatusCode, function ($query) use ($customerStatusCode) {
            $query->where('customers.customer_status_type', 'customer_status')
                ->where('customers.customer_status_code', $customerStatusCode);
        })
        ->orderBy('customers.customer_id', 'DESC')
        ->paginate(perPage: $displayedNumber, page: $page);
    }
}
