<?php

declare(strict_types=1);

namespace App\UseCases\Tenant\ServiceContract;

use App\Models\ServiceContract;

class ShowAction
{
    /**
     * 顧客サービス契約詳細取得
     *
     * @param string   $languageCode
     * @param int|null $tenantId
     * @param string   $publicId
     *
     * @return \App\Models\ServiceContract
     */
    public function __invoke(
        string $languageCode,
        ?int $tenantId,
        string $publicId,
    ): ServiceContract {
        return ServiceContract::select([
            'service_contracts.public_id AS service_contracts_public_id',
            'tenants.tenant_name',
            'services.public_id AS service_public_id',
            'service_translations.service_name',
            'service_plans.public_id AS service_plan_public_id',
            'service_plan_translations.service_plan_name',
            'customers.public_id AS customer_public_id',
            'company_name_translations.company_legal_name',
            'companies.company_name_en',
            'service_contracts.contract_name',
            'service_contracts.contract_language',
            'contract_language_translation.selection_item_name AS contract_language_name',
            'contract_status_translation.selection_item_name AS contract_status',
            'service_contracts.contract_status_code',
            'service_usage_status_translation.selection_item_name AS service_usage_status',
            'service_contracts.service_usage_status_code',
            'service_contracts.contract_date',
            'service_contracts.contract_start_date',
            'service_contracts.contract_end_date',
            'service_contracts.contract_auto_update',
            'service_contracts.customer_contact_user_name',
            'service_contracts.customer_contact_user_dept',
            'service_contracts.customer_contact_user_title',
            'service_contracts.customer_contact_user_email',
            'service_contracts.customer_contract_user_name',
            'service_contracts.customer_contract_user_dept',
            'service_contracts.customer_contract_user_title',
            'service_contracts.customer_contract_user_email',
            'service_contracts.customer_payment_user_name',
            'service_contracts.customer_payment_user_dept',
            'service_contracts.customer_payment_user_title',
            'service_contracts.customer_payment_user_email',
            'service_rep_user.user_name AS service_rep_user_name',
            'service_rep_user.public_id AS service_rep_user_public_id',
            'service_mgr_user.user_name AS service_mgr_user_name',
            'service_mgr_user.public_id AS service_mgr_user_public_id',
            'service_contracts.invoice_remind_days',
            'billing_cycle_translation.selection_item_name AS billing_cycle',
            'service_contracts.billing_cycle_code',
            'service_contracts.remarks',
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
        ->join('selection_item_translations AS contract_language_translation', function ($join) use ($languageCode) {
            $join->on('service_contracts.contract_language', 'contract_language_translation.selection_item_code')
                ->where('contract_language_translation.selection_item_type', 'language_code')
                ->where('contract_language_translation.language_code', $languageCode);
        })
        ->join('selection_item_translations AS contract_status_translation', function ($join) use ($languageCode) {
            $join->on('service_contracts.contract_status_code', 'contract_status_translation.selection_item_code')
                ->where('contract_status_translation.selection_item_type', 'service_contract_status')
                ->where('contract_status_translation.language_code', $languageCode);
        })
        ->join('selection_item_translations AS service_usage_status_translation', function ($join) use ($languageCode) {
            $join->on('service_contracts.service_usage_status_code', 'service_usage_status_translation.selection_item_code')
                ->where('service_usage_status_translation.selection_item_type', 'service_usage_status')
                ->where('service_usage_status_translation.language_code', $languageCode);
        })
        ->join('user_options AS service_rep_user', 'service_contracts.service_rep_user_option_id', '=', 'service_rep_user.user_option_id')
        ->join('user_options AS service_mgr_user', 'service_contracts.service_mgr_user_option_id', '=', 'service_mgr_user.user_option_id')
        ->join('selection_item_translations AS billing_cycle_translation', function ($join) use ($languageCode) {
            $join->on('service_contracts.billing_cycle_code', 'billing_cycle_translation.selection_item_code')
                ->where('billing_cycle_translation.selection_item_type', 'billing_cycle')
                ->where('billing_cycle_translation.language_code', $languageCode);
        })
        ->when($tenantId, function ($query) use ($tenantId) {
            $query->where('service_contracts.tenant_id', $tenantId);
        })
        ->where('service_contracts.public_id', $publicId)
        ->firstOrFail();
    }
}
