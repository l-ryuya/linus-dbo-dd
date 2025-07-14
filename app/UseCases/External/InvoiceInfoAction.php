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
            'company_name_translations.company_legal_name AS sales_rep_company_name',
            DB::raw("
                CASE
                    WHEN service_contracts.contract_language = 'eng' THEN user_options.user_name_en
                    ELSE user_options.user_name
                END AS service_rep_name
            "),
            'user_options.user_mail AS service_rep_mail',
            'user_options.phone_number AS service_rep_phone_number',
            'service_contracts.customer_payment_user_name',
            'service_contracts.customer_payment_user_dept',
            'service_contracts.customer_payment_user_title',
            'service_contracts.customer_payment_user_mail',
        ])
        ->join('user_options', 'user_options.user_option_id', '=', 'service_contracts.service_rep_user_option_id')
        ->join('companies', 'companies.tenant_id', '=', 'user_options.tenant_id')
        ->join('company_name_translations', function ($join) {
            $join->on('companies.company_id', '=', 'company_name_translations.company_id')
                ->whereColumn('company_name_translations.language_code', 'companies.default_language_code');
        })
        ->where('service_contracts.public_id', $publicId)
        ->firstOrFail();
    }
}
