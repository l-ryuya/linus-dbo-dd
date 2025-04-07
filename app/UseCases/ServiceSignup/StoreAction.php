<?php

namespace App\UseCases\ServiceSignup;

use App\Dto\ServiceSignup\StoreInput;
use App\Enums\RoleType;
use App\Models\Company;
use App\Models\ServiceContract;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StoreAction
{
    /**
     * サービス申込
     *
     * @param \App\Dto\ServiceSignup\StoreInput $data
     *
     * @return object
     * @throws \Throwable
     */
    public function __invoke(
        StoreInput $data,
    ): object {
        DB::beginTransaction();

        try {
            $company = $this->createCompany($data);
            $contactPerson = $this->createContactPerson($data, $company);
            $contractPerson = $this->createContractPerson($data, $company);
            $serviceContract = $this->createServiceContract(
                $data,
                $contactPerson,
                $contractPerson,
                $company,
            );

            // デューデリジェンスの申請処理が必要

            DB::commit();

            return (object) [
                'contactPersonUserCode' => $contactPerson->user_code,
                'contractPersonUserCode' => $contractPerson->user_code,
                'companyCode' => $company->company_code,
                'serviceContractCode' => $serviceContract->service_contract_code,
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * サービス契約を作成する
     *
     * @param \App\Dto\ServiceSignup\StoreInput $data
     * @param \App\Models\User                  $contactPerson
     * @param \App\Models\User                  $contractPerson
     * @param \App\Models\Company               $company
     *
     * @return \App\Models\ServiceContract
     */
    private function createServiceContract(
        StoreInput $data,
        User $contactPerson,
        User $contractPerson,
        Company $company,
    ): ServiceContract {
        $serviceContract = new ServiceContract();
        $serviceContract->service_contract_code = ServiceContract::generateNewServiceContractId();
        $serviceContract->company_id = $company->company_id;
        $serviceContract->service_code = $data->serviceCode;
        $serviceContract->service_plan_code = $data->servicePlan;
        $serviceContract->service_usage_status_type = 'service_usage_status';
        $serviceContract->service_usage_status_code = 'Under DD';
        $serviceContract->service_contract_status_type = 'service_contract_status';
        $serviceContract->service_contract_status_code = 'Not Requested';
        $serviceContract->payment_cycle_type = 'payment_cycle';
        $serviceContract->payment_cycle_code = $data->paymentCycle;
        $serviceContract->responsible_user_id = $contactPerson->user_id;
        $serviceContract->contract_manager_user_id = $contractPerson->user_id;
        $serviceContract->service_application_date = Carbon::today();
        $serviceContract->save();

        return $serviceContract;
    }

    /**
     * 担当者を作成する
     *
     * @param \App\Dto\ServiceSignup\StoreInput $data
     * @param \App\Models\Company               $company
     *
     * @return \App\Models\User
     */
    private function createContactPerson(
        StoreInput $data,
        Company $company,
    ): User {
        $contactPerson = new User();
        $contactPerson->company_id = $company->company_id;
        $contactPerson->user_code = User::generateNewUserId();
        $contactPerson->user_status_type = 'user_status';
        $contactPerson->user_status = 'Under DD';
        $contactPerson->last_name_en = $data->contactPersonLastName;
        $contactPerson->middle_name_en = $data->contactPersonMiddleName;
        $contactPerson->first_name_en = $data->contactPersonFirstName;
        $contactPerson->position_en = $data->contactPersonPosition;
        $contactPerson->email = $data->contactPersonEmail;
        // デューデリ承認後にメールを送信するため、仮のパスワードを設定
        $contactPerson->password = Hash::make(Str::random(16));
        $contactPerson->roles = RoleType::Customer->value;
        $contactPerson->save();

        return $contactPerson;
    }

    /**
     * 契約担当者を作成する
     *
     * @param \App\Dto\ServiceSignup\StoreInput $data
     * @param \App\Models\Company               $company
     *
     * @return \App\Models\User
     */
    private function createContractPerson(
        StoreInput $data,
        Company $company,
    ): User {
        $contractPerson = new User();
        $contractPerson->company_id = $company->company_id;
        $contractPerson->user_code = User::generateNewUserId();
        $contractPerson->user_status_type = 'user_status';
        $contractPerson->user_status = 'Under DD';
        $contractPerson->last_name_en = $data->contractPersonFirstName;
        $contractPerson->middle_name_en = $data->contractPersonMiddleName;
        $contractPerson->first_name_en = $data->contractPersonFirstName;
        $contractPerson->position_en = $data->contractPersonPosition;
        $contractPerson->email = $data->contractPersonEmail;
        // デューデリ承認後にメールを送信するため、仮のパスワードを設定
        $contractPerson->password = Hash::make(Str::random(16));
        $contractPerson->roles = RoleType::Customer->value;
        $contractPerson->save();

        return $contractPerson;
    }

    /**
     * 会社を作成する
     *
     * @param \App\Dto\ServiceSignup\StoreInput $data
     *
     * @return \App\Models\Company
     */
    private function createCompany(
        StoreInput $data,
    ): Company {
        $company = new Company();
        $company->company_code = Company::generateNewCompanyId();
        $company->latest_dd_id = 0;
        $company->organization_type_type = 'organization_type';
        $company->organization_type_code = 'Customer';
        $company->company_status_type = 'company_status';
        $company->company_status_code = 'Under DD';
        $company->second_language_type = 'language_code';
        $company->second_language_code = $data->secondLanguage;

        $company->company_name_en = $data->companyName;
        $company->country_region_code = $data->country;
        $company->postal_code_en = $data->postalCode;
        $company->prefecture_en = $data->state;
        $company->city_en = $data->city;
        $company->street_en = $data->addressLine1;
        $company->building_room_en = $data->addressLine2;
        $company->save();

        return $company;
    }
}
