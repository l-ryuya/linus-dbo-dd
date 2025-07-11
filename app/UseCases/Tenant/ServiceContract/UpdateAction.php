<?php

declare(strict_types=1);

namespace App\UseCases\Tenant\ServiceContract;

use App\Dto\Tenant\ServiceContract\UpdateInput;
use App\Enums\ServiceContractStatus;
use App\Exceptions\LogicValidationException;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceContract;
use App\Models\ServicePlan;
use App\Models\Tenant;
use App\Models\UserOption;
use Illuminate\Support\Facades\DB;

class UpdateAction
{
    /**
     * 顧客サービス契約更新
     *
     * @param Tenant      $identifiedTenant
     * @param string      $serviceContractPublicId
     * @param UpdateInput $data
     *
     * @return void
     * @throws \Throwable
     */
    public function __invoke(
        Tenant $identifiedTenant,
        string $serviceContractPublicId,
        UpdateInput $data,
    ): void {
        DB::beginTransaction();

        try {
            $customer = Customer::where('public_id', $data->customerPublicId)
                ->where('tenant_id', $identifiedTenant->tenant_id)
                ->first();

            $service = Service::where('public_id', $data->servicePublicId)
                ->where('tenant_id', $identifiedTenant->tenant_id)
                ->firstOrFail();
            $servicePlan = ServicePlan::where('public_id', $data->servicePlanPublicId)
                ->where('service_id', $service->service_id)
                ->firstOrFail();
            $serviceRepUserOption = UserOption::where('public_id', $data->serviceRepUserOptionPublicId)
                ->where('tenant_id', $identifiedTenant->tenant_id)
                ->where('service_id', $service->service_id)
                ->firstOrFail();
            $serviceMgrUserOption = UserOption::where('public_id', $data->serviceMgrUserOptionPublicId)
                ->where('tenant_id', $identifiedTenant->tenant_id)
                ->where('service_id', $service->service_id)
                ->firstOrFail();

            $this->updateServiceContract(
                $serviceContractPublicId,
                $identifiedTenant->tenant_id,
                $customer->customer_id,
                $service->service_id,
                $servicePlan->service_plan_id,
                $serviceRepUserOption->user_option_id,
                $serviceMgrUserOption->user_option_id,
                $data,
            );

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * サービス契約を更新する
     *
     * @param string                                      $serviceContractPublicId
     * @param int                                         $tenantId
     * @param int                                         $customerId
     * @param int                                         $serviceId
     * @param int                                         $servicePlanId
     * @param int                                         $serviceRepUserOptionId
     * @param int                                         $serviceMgrUserOptionId
     * @param \App\Dto\Tenant\ServiceContract\UpdateInput $data
     *
     * @return void
     * @throws \App\Exceptions\LogicValidationException|\Throwable
     */
    private function updateServiceContract(
        string $serviceContractPublicId,
        int $tenantId,
        int $customerId,
        int $serviceId,
        int $servicePlanId,
        int $serviceRepUserOptionId,
        int $serviceMgrUserOptionId,
        UpdateInput $data,
    ): void {
        $serviceContract = ServiceContract::where('public_id', $serviceContractPublicId)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();
        throw_if(
            $serviceContract->contract_status_code != ServiceContractStatus::ContractInfoRegistered->value,
            new LogicValidationException(
                errors: ['contractStatusCode' => [__('logic.contract_status_locked')]],
            ),
        );

        $serviceContract->tenant_id = $tenantId;
        $serviceContract->customer_id = $customerId;
        $serviceContract->service_id = $serviceId;
        $serviceContract->service_plan_id = $servicePlanId;

        $serviceContract->contract_name = $data->contractName;
        $serviceContract->contract_language = $data->contractLanguage;
        // ステータスタイプ設定
        $serviceContract->contract_status_code = $data->contractStatusCode;
        $serviceContract->service_usage_status_code = $data->serviceUsageStatusCode;
        $serviceContract->contract_date = $data->contractDate;
        $serviceContract->contract_start_date = $data->contractStartDate;
        $serviceContract->contract_end_date = $data->contractEndDate;
        $serviceContract->contract_auto_update = $data->contractAutoUpdate;

        // 顧客連絡担当者情報
        $serviceContract->customer_contact_user_name = $data->customerContactUserName;
        $serviceContract->customer_contact_user_dept = $data->customerContactUserDept;
        $serviceContract->customer_contact_user_title = $data->customerContactUserTitle;
        $serviceContract->customer_contact_user_mail = $data->customerContactUserMail;
        // 顧客契約担当者情報
        $serviceContract->customer_contract_user_name = $data->customerContractUserName;
        $serviceContract->customer_contract_user_dept = $data->customerContractUserDept;
        $serviceContract->customer_contract_user_title = $data->customerContractUserTitle;
        $serviceContract->customer_contract_user_mail = $data->customerContractUserMail;
        // 顧客支払担当者情報
        $serviceContract->customer_payment_user_name = $data->customerPaymentUserName;
        $serviceContract->customer_payment_user_dept = $data->customerPaymentUserDept;
        $serviceContract->customer_payment_user_title = $data->customerPaymentUserTitle;
        $serviceContract->customer_payment_user_mail = $data->customerPaymentUserMail;
        // サービス担当者・管理者情報
        $serviceContract->service_rep_user_option_id = $serviceRepUserOptionId;
        $serviceContract->service_mgr_user_option_id = $serviceMgrUserOptionId;

        // 請求サイクル情報
        $serviceContract->billing_cycle_code = $data->billingCycleCode;

        // 請求書督促タイミング設定
        if ($data->invoiceRemindDays) {
            $remindDays = array_map('intval', explode(',', $data->invoiceRemindDays));
            $serviceContract->invoice_remind_days = '{' . implode(',', $remindDays) . '}';
        }

        $serviceContract->remarks = $data->remarks;

        $serviceContract->save();
    }
}
