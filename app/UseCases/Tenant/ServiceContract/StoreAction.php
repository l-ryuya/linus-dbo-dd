<?php

declare(strict_types=1);

namespace App\UseCases\Tenant\ServiceContract;

use App\Dto\Tenant\ServiceContract\StoreInput;
use App\Enums\ServiceContractStatus;
use App\Jobs\CloudSign\ContractJob;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceContract;
use App\Models\ServicePlan;
use App\Models\Tenant;
use App\Models\UserOption;
use Illuminate\Support\Facades\DB;

class StoreAction
{
    /**
     * 顧客サービス契約登録
     *
     * @param Tenant $identifiedTenant テナント情報
     * @param ServiceContractStatus $serviceContractStatus サービス契約ステータス
     * @param StoreInput $data 入力データ
     *
     * @return object 作成されたサービス契約の情報
     * @throws \Throwable
     */
    public function __invoke(
        Tenant $identifiedTenant,
        ServiceContractStatus $serviceContractStatus,
        StoreInput $data,
    ): object {
        DB::beginTransaction();

        try {
            $customer = Customer::where('public_id', $data->customerPublicId)
                ->where('tenant_id', $identifiedTenant->tenant_id)
                ->first();

            $service = Service::where('public_id', $data->servicePublicId)
                ->where('tenant_id', $identifiedTenant->tenant_id)
                ->firstOrFail();
            $servicePlan = null;
            if ($data->servicePlanPublicId) {
                $servicePlan = ServicePlan::where('public_id', $data->servicePlanPublicId)
                    ->where('service_id', $service->service_id)
                    ->firstOrFail();
            }
            $serviceRepUserOption = null;
            if ($data->serviceRepUserPublicId) {
                $serviceRepUserOption = UserOption::where('public_id', $data->serviceRepUserPublicId)
                    ->where('tenant_id', $identifiedTenant->tenant_id)
                    ->where('service_id', $service->service_id)
                    ->firstOrFail();
            }
            $serviceMgrUserOption = null;
            if ($data->serviceMgrUserPublicId) {
                $serviceMgrUserOption = UserOption::where('public_id', $data->serviceMgrUserPublicId)
                    ->where('tenant_id', $identifiedTenant->tenant_id)
                    ->where('service_id', $service->service_id)
                    ->firstOrFail();
            }

            $serviceContract = $this->createServiceContract(
                $identifiedTenant->tenant_id,
                $customer->customer_id,
                $serviceContractStatus,
                $service->service_id,
                $servicePlan?->service_plan_id,
                $serviceRepUserOption?->user_option_id,
                $serviceMgrUserOption?->user_option_id,
                $data,
            );

            DB::commit();

            ContractJob::dispatch($serviceContract->service_contract_id);

            return (object) [
                'serviceContractPublicId' => $serviceContract->public_id,
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * サービス契約を作成する
     *
     * @param int                              $tenantId
     * @param int                              $customerId
     * @param \App\Enums\ServiceContractStatus $serviceContractStatus
     * @param int                              $serviceId
     * @param int|null                         $servicePlanId
     * @param int|null                         $serviceRepUserOptionId
     * @param int|null                         $serviceMgrUserOptionId
     * @param StoreInput                       $data
     *
     * @return ServiceContract 作成されたサービス契約
     */
    private function createServiceContract(
        int $tenantId,
        int $customerId,
        ServiceContractStatus $serviceContractStatus,
        int $serviceId,
        ?int $servicePlanId,
        ?int $serviceRepUserOptionId,
        ?int $serviceMgrUserOptionId,
        StoreInput $data,
    ): ServiceContract {
        $serviceContract = new ServiceContract();
        $serviceContract->tenant_id = $tenantId;
        $serviceContract->customer_id = $customerId;
        $serviceContract->service_id = $serviceId;
        $serviceContract->service_plan_id = $servicePlanId;

        $serviceContract->contract_name = $data->contractName;
        $serviceContract->contract_language = $data->contractLanguage;
        // ステータスタイプ設定
        $serviceContract->contract_status_type = 'service_contract_status';
        $serviceContract->contract_status_code = $serviceContractStatus->value;
        $serviceContract->service_usage_status_type = 'service_usage_status';
        $serviceContract->service_usage_status_code = $data->serviceUsageStatusCode;
        $serviceContract->contract_date = $data->contractDate;
        $serviceContract->contract_start_date = $data->contractStartDate;
        $serviceContract->contract_end_date = $data->contractEndDate;
        $serviceContract->contract_auto_update = $data->contractAutoUpdate;

        // 顧客連絡担当者情報
        $serviceContract->customer_contact_user_name = $data->customerContactUserName;
        $serviceContract->customer_contact_user_dept = $data->customerContactUserDept;
        $serviceContract->customer_contact_user_title = $data->customerContactUserTitle;
        $serviceContract->customer_contact_user_email = $data->customerContactUserEmail;
        // 顧客契約担当者情報
        $serviceContract->customer_contract_user_name = $data->customerContractUserName;
        $serviceContract->customer_contract_user_dept = $data->customerContractUserDept;
        $serviceContract->customer_contract_user_title = $data->customerContractUserTitle;
        $serviceContract->customer_contract_user_email = $data->customerContractUserEmail;
        // 顧客支払担当者情報
        $serviceContract->customer_payment_user_name = $data->customerPaymentUserName;
        $serviceContract->customer_payment_user_dept = $data->customerPaymentUserDept;
        $serviceContract->customer_payment_user_title = $data->customerPaymentUserTitle;
        $serviceContract->customer_payment_user_email = $data->customerPaymentUserEmail;
        // サービス担当者・管理者情報
        $serviceContract->service_rep_user_option_id = $serviceRepUserOptionId;
        $serviceContract->service_mgr_user_option_id = $serviceMgrUserOptionId;
        // 見積情報
        $serviceContract->quotation_name = $data->quotationName;
        $serviceContract->quotation_number = $data->quotationNumber;
        $serviceContract->quotation_date = $data->quotationDate;
        // 提案情報
        $serviceContract->proposal_name = $data->proposalName;
        $serviceContract->proposal_number = $data->proposalNumber;
        $serviceContract->proposal_date = $data->proposalDate;

        // 請求サイクル情報
        $serviceContract->billing_cycle_type = 'billing_cycle';
        $serviceContract->billing_cycle_code = $data->billingCycleCode ?? 'monthly';
        // 請求書督促タイミング設定
        if ($data->invoiceRemindDays) {
            $remindDays = array_map('intval', explode(',', $data->invoiceRemindDays));
            $serviceContract->invoice_remind_days = '{' . implode(',', $remindDays) . '}';
        }

        $serviceContract->remarks = $data->remarks;

        $serviceContract->save();
        $serviceContract->refresh();

        return $serviceContract;
    }
}
