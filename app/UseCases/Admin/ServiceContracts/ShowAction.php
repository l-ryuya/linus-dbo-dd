<?php

declare(strict_types=1);

namespace App\UseCases\Admin\ServiceContracts;

use App\Models\Company;
use App\Models\SelectionItemTranslation;
use App\Models\ServicePlanTranslation;
use App\Models\ServiceTranslation;

class ShowAction
{
    /**
     * 管理者向けのサービス契約詳細を取得する
     *
     * @param string $languageCode
     * @param string $companyCode
     * @return Company
     */
    public function __invoke(
        string $languageCode,
        string $companyCode,
    ): Company {
        $company = Company::select([
            'companies.company_id',
            'companies.company_code',
            'companies.company_name_en',
            'companies.latest_dd_id',
            'companies.postal_code_en',
            'companies.prefecture_en',
            'companies.city_en',
            'companies.street_en',
            'companies.building_room_en',
            'companies.created_at',
        ])
        ->where('companies.company_code', $companyCode)
        ->first();

        if (empty($company)) {
            abort(404);
        }

        $statuses = SelectionItemTranslation::filterByTypeAndLanguage(null, $languageCode)->get();
        $services = ServiceTranslation::withLanguage($languageCode)->get();
        $servicePlans = ServicePlanTranslation::withLanguage($languageCode)->get();

        $company->setAttribute(
            'dd_status',
            $statuses->where('selection_item_type', 'company_status')
                ->where('selection_item_code', $company->latestDd?->dd_status)
                ->first()?->selection_item_name,
        );

        $company->setAttribute('service_contracts', collect());
        foreach ($company->serviceContracts as $contract) {
            $company->getAttribute('service_contracts')->push((object) [
                'service_code' => $contract->service_code,
                'service_plan_code' => $contract->service_plan_code,
                'service_contract_code' => $contract->service_contract_code,
                'service_name' => $services->firstWhere('service_code', $contract->service_code)->service_name,
                'service_plan_name' => $servicePlans->firstWhere('service_plan_code', $contract->service_plan_code)->service_plan_name,
                'department_name' => $contract->department_name_en,
                'service_usage_status' => $statuses
                    ->where('selection_item_type', $contract->service_usage_status_type)
                    ->where('selection_item_code', $contract->service_usage_status_code)
                    ->first()?->selection_item_name,
                'service_contract_status' => $statuses
                    ->where('selection_item_type', $contract->service_contract_status_type)
                    ->where('selection_item_code', $contract->service_contract_status_code)
                    ->first()?->selection_item_name,
                'payment_method' => $statuses
                    ->where('selection_item_type', $contract->payment_method_type)
                    ->where('selection_item_code', $contract->payment_method_code)
                    ->first()?->selection_item_name,
                'person_in_charge' => $contract->personInCharge,
                'contract_manager' => $contract->contractManager,
            ]);
        }

        return $company;
    }
}
