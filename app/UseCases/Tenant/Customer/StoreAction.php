<?php

declare(strict_types=1);

namespace App\UseCases\Tenant\Customer;

use App\Dto\Tenant\Customer\StoreInput;
use App\Models\Company;
use App\Models\CompanyNameTranslation;
use App\Models\Customer;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class StoreAction
{
    /**
     * 顧客登録
     *
     * @param \App\Models\Tenant                  $identifiedTenant
     * @param \App\Dto\Tenant\Customer\StoreInput $data
     *
     * @return object
     * @throws \Throwable
     */
    public function __invoke(
        Tenant $identifiedTenant,
        StoreInput $data,
    ): object {
        DB::beginTransaction();

        try {
            $company = $this->createCompany($identifiedTenant, $data);
            $customer = $this->createCustomer($identifiedTenant, $company);

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
     * @param \App\Models\Tenant                  $identifiedTenant
     * @param \App\Dto\Tenant\Customer\StoreInput $data
     *
     * @return \App\Models\Company
     */
    private function createCompany(
        Tenant $identifiedTenant,
        StoreInput $data,
    ): Company {
        $company = new Company();
        $company->tenant_id = $identifiedTenant->tenant_id;
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
     * @param \App\Models\Tenant  $identifiedTenant
     * @param \App\Models\Company $company
     *
     * @return \App\Models\Company
     */
    private function createCustomer(
        Tenant $identifiedTenant,
        Company $company,
    ): Company {
        $customer = new Customer();
        $customer->tenant_id = $identifiedTenant->tenant_id;
        $customer->company_id = $company->company_id;
        $customer->sys_organization_code = $identifiedTenant->customers_sys_organization_code;
        $customer->customer_status_type = 'customer_status';
        $customer->customer_status_code = 'under_dd';
        $customer->save();
        $customer->refresh();

        return $company;
    }
}
