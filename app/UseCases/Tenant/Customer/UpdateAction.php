<?php

declare(strict_types=1);

namespace App\UseCases\Tenant\Customer;

use App\Dto\Tenant\Customer\UpdateInput;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class UpdateAction
{
    /**
     * 顧客更新
     *
     * @param \App\Models\Tenant                   $identifiedTenant
     * @param string                               $publicId
     * @param \App\Dto\Tenant\Customer\UpdateInput $data
     *
     * @return void
     * @throws \Throwable
     */
    public function __invoke(
        Tenant $identifiedTenant,
        string $publicId,
        UpdateInput $data,
    ): void {
        DB::beginTransaction();

        try {
            $customer = $this->updateCustomer($identifiedTenant, $publicId, $data);
            $this->updateCompany($customer, $data);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * 法人情報を更新する
     *
     * @param \App\Models\Customer                 $customer
     * @param \App\Dto\Tenant\Customer\UpdateInput $data
     *
     * @return void
     */
    private function updateCompany(
        Customer $customer,
        UpdateInput $data,
    ): void {
        $company = Company::findOrFail($customer->company_id);
        $company->company_name_en = $data->customerNameEn;
        $company->default_language_code = $data->defaultLanguageCode;
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

        // 複合主キーにEloquentが対応していないため、DBファサードを使用して更新
        DB::table('company_name_translations')->updateOrInsert(
            [
                'company_id' => $company->company_id,
                'language_code' => $data->defaultLanguageCode,
            ],
            [
                'company_legal_name' => $data->customerName,
                'updated_at' => now(),
                'created_at' => now(),
            ],
        );
    }

    /**
     * 顧客情報を更新する
     *
     * @param \App\Models\Tenant                   $identifiedTenant
     * @param string                               $publicId
     * @param \App\Dto\Tenant\Customer\UpdateInput $data
     *
     * @return \App\Models\Customer
     */
    private function updateCustomer(
        Tenant $identifiedTenant,
        string $publicId,
        UpdateInput $data,
    ): Customer {
        $customer = Customer::where('tenant_id', $identifiedTenant->tenant_id)
            ->where('public_id', $publicId)
            ->firstOrFail();

        $customer->customer_status_code = $data->customerStatusCode;
        $customer->save();

        return $customer;
    }
}
