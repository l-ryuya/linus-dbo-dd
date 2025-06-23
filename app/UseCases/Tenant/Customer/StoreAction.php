<?php

declare(strict_types=1);

namespace App\UseCases\Tenant\Customer;

use App\Dto\Tenant\Customer\StoreInput;
use App\Models\Company;
use App\Models\CompanyNameTranslation;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class StoreAction
{
    /**
     * 顧客登録
     *
     * @param int                                 $tenantId
     * @param \App\Dto\Tenant\Customer\StoreInput $data
     *
     * @return object
     * @throws \Throwable
     */
    public function __invoke(
        int $tenantId,
        StoreInput $data,
    ): object {
        DB::beginTransaction();

        try {
            // M5にorganizationを登録する？

            $company = $this->createCompany($tenantId, $data);
            $customer = $this->createCustomer($tenantId, $company);

            // AI DD 申請処理が必要

            DB::commit();

            return (object) [
                'companyPublicId' => $company->public_id,
                'customerPublicId' => $customer->public_id,
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * 会社を作成する
     *
     * @param int $tenantId
     * @param \App\Dto\Tenant\Customer\StoreInput $data
     *
     * @return \App\Models\Company
     */
    private function createCompany(
        int $tenantId,
        StoreInput $data,
    ): Company {
        $company = new Company();
        $company->tenant_id = $tenantId;
        $company->company_name_en = $data->customerNameEn;
        $company->country_code_alpha3 = $data->countryCodeAlpha3;
        $company->website_url = $data->websiteUrl;
        $company->shareholders_url = $data->shareholdersUrl;
        $company->executives_url = $data->executivesUrl;
        $company->postal = $data->postalCode;
        $company->state = $data->state;
        $company->city = $data->city;
        $company->street = $data->street;
        $company->building = $data->building;
        $company->remarks = $data->remarks;
        $company->save();
        $company->refresh();

        $companyNameTranslation = new CompanyNameTranslation();
        $companyNameTranslation->company_id = $company->company_id;
        $companyNameTranslation->language_code = $data->languageCode;
        $companyNameTranslation->legal_name = $data->customerName;
        $companyNameTranslation->save();

        return $company;
    }

    /**
     * 顧客を作成する
     *
     * @param int                 $tenantId
     * @param \App\Models\Company $company
     *
     * @return \App\Models\Company
     */
    private function createCustomer(
        int $tenantId,
        Company $company,
    ): Company {
        $customer = new Customer();
        $customer->tenant_id = $tenantId;
        $customer->company_id = $company->company_id;
        // 顧客ユーザーは固定された組織コードを使用
        $customer->sys_organization_code = config('m5.customer.fixed_sys_organization_code');
        $customer->customer_status_type = 'customer_status';
        $customer->customer_status_code = 'under_dd';
        $customer->save();
        $customer->refresh();

        return $company;
    }
}
