<?php

declare(strict_types=1);

namespace App\UseCases\External;

use App\Models\ServiceContract;
use Illuminate\Support\Facades\DB;

class InvoiceInfoAction
{
    /**
     * 請求情報取得
     *
     * @param string $publicId
     *
     * @return \App\Models\ServiceContract
     */
    public function __invoke(
        string $publicId,
    ): ServiceContract {
        return ServiceContract::select([
            'service_contracts.public_id',
            'service_contracts.contract_language',
            'services.service_dept_group_email',
            'services.backoffice_group_email',

            'service_rep_company_translation.company_legal_name AS sales_rep_company_name',
            DB::raw("
                CASE
                    WHEN service_contracts.contract_language = 'eng' THEN service_rep_user.user_name_en
                    ELSE service_rep_user.user_name
                END AS service_rep_name
            "),
            'service_rep_user.user_email AS service_rep_email',
            'service_rep_user.phone_number AS service_rep_phone_number',

            'service_mgr_company_translation.company_legal_name AS sales_rep_manager_company_name',
            DB::raw("
                CASE
                    WHEN service_contracts.contract_language = 'eng' THEN service_mgr_user.user_name_en
                    ELSE service_mgr_user.user_name
                END AS service_rep_manager_name
            "),
            'service_mgr_user.user_email AS service_rep_manager_email',
            'service_mgr_user.phone_number AS service_rep_manager_phone_number',

            'service_contracts.customer_payment_user_name',
            'service_contracts.customer_payment_user_dept',
            'service_contracts.customer_payment_user_title',
            'service_contracts.customer_payment_user_email',
        ])
        ->join('services', 'services.service_id', '=', 'service_contracts.service_id')
        ->join('user_options AS service_rep_user', 'service_rep_user.user_option_id', '=', 'service_contracts.service_rep_user_option_id')
        ->join('companies AS service_rep_company', 'service_rep_company.tenant_id', '=', 'service_rep_user.tenant_id')
        ->join('company_name_translations AS service_rep_company_translation', function ($join) {
            $join->on('service_rep_company.company_id', '=', 'service_rep_company_translation.company_id')
                ->whereColumn('service_rep_company_translation.language_code', 'service_rep_company.default_language_code');
        })
        ->join('user_options AS service_mgr_user', 'service_rep_user.user_option_id', '=', 'service_contracts.service_mgr_user_option_id')
        ->join('companies AS service_mgr_company', 'service_mgr_company.tenant_id', '=', 'service_mgr_user.tenant_id')
        ->join('company_name_translations AS service_mgr_company_translation', function ($join) {
            $join->on('service_mgr_company.company_id', '=', 'service_mgr_company_translation.company_id')
                ->whereColumn('service_mgr_company_translation.language_code', 'service_mgr_company.default_language_code');
        })
        ->where('service_contracts.public_id', $publicId)
        ->firstOrFail();
    }
}
