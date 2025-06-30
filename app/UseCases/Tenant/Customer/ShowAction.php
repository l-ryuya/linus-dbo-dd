<?php

declare(strict_types=1);

namespace App\UseCases\Tenant\Customer;

use App\Models\Company;
use App\Models\Customer;
use App\Models\SelectionItemTranslation;
use App\Models\ServiceContract;

class ShowAction
{
    /**
     * 顧客詳細取得
     *
     * @param string $languageCode
     * @param int    $tenantId
     * @param string $publicId
     *
     * @return object
     */
    public function __invoke(
        string $languageCode,
        int $tenantId,
        string $publicId,
    ): object {
        $customer = Customer::select([
            'customer_id',
            'public_id',
            'tenant_id',
            'company_id',
            'customer_code',
            'customer_status_type',
            'customer_status_code',
        ])
        ->where('tenant_id', $tenantId)
        ->where('public_id', $publicId)
        ->firstOrFail();

        $statuses = SelectionItemTranslation::filterByTypeAndLanguage($customer->customer_status_type, $languageCode)->get();

        $customer->setAttribute(
            'customer_status',
            $statuses->firstWhere('selection_item_code', $customer->customer_status_code)?->selection_item_name,
        );

        $company = Company::select([
            'companies.company_id',
            'companies.company_code',
            'companies.public_id',
            'companies.company_name_en',
            'companies.default_language_code',
            'companies.country_code_alpha3',
            'companies.postal',
            'companies.state',
            'companies.city',
            'companies.street',
            'companies.building',
            'companies.website_url',
            'companies.shareholders_url',
            'companies.executives_url',
            'companies.remarks',
            'companies.updated_at',
            'company_name_translations.company_legal_name',
        ])
        ->leftJoin('company_name_translations', function ($join) use ($languageCode) {
            $join->on('companies.company_id', '=', 'company_name_translations.company_id')
                ->where('company_name_translations.language_code', $languageCode);
        })
        ->where('companies.company_id', $customer->company_id)
        ->first();

        $serviceContracts = ServiceContract::select([
            'service_contracts.public_id',
            'service_contracts.service_id',
            'service_contracts.service_plan_id',
            'service_contracts.contract_name',
            'service_contracts.service_usage_status_type',
            'service_contracts.service_usage_status_code',
            'service_contracts.contract_status_type',
            'service_contracts.contract_status_code',
            'service_translations.service_name',
            'service_plan_translations.service_plan_name',
        ])
        ->join('service_translations', function ($join) use ($languageCode) {
            $join->on('service_contracts.service_id', '=', 'service_translations.service_id')
                ->where('service_translations.language_code', $languageCode);
        })
        ->join('service_plan_translations', function ($join) use ($languageCode) {
            $join->on('service_contracts.service_plan_id', '=', 'service_plan_translations.service_plan_id')
                ->where('service_plan_translations.language_code', $languageCode);
        })
        ->where('service_contracts.customer_id', $customer->customer_id)
        ->get();

        $serviceUsageStatus = SelectionItemTranslation::filterByTypeAndLanguage('service_usage_status', $languageCode)->get();
        $serviceContractStatus = SelectionItemTranslation::filterByTypeAndLanguage('service_contract_status', $languageCode)->get();

        $serviceContracts->map(function ($item) use ($serviceUsageStatus, $serviceContractStatus) {
            $item->setAttribute('service_usage_status', $serviceUsageStatus->firstWhere('selection_item_code', $item->service_usage_status_code)->selection_item_name);
            $item->setAttribute('contract_status', $serviceContractStatus->firstWhere('selection_item_code', $item->contract_status_code)->selection_item_name);

            return $item;
        });

        return (object) [
            'customer' => $customer,
            'company' => $company,
            'serviceContracts' => $serviceContracts,
        ];
    }
}
