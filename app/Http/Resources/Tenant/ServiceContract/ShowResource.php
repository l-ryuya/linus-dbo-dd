<?php

declare(strict_types=1);

namespace App\Http\Resources\Tenant\ServiceContract;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $service_contracts_public_id
 * @property string $tenant_name
 * @property string $service_public_id
 * @property string $service_name
 * @property string $service_plan_public_id
 * @property string $service_plan_name
 * @property string $customer_public_id
 * @property string $company_legal_name
 * @property string $company_name_en
 * @property string $contract_name
 * @property string $contract_language_name
 * @property string $contract_language
 * @property string $contract_status
 * @property string $contract_status_code
 * @property string $service_usage_status
 * @property string $service_usage_status_code
 * @property \Carbon\Carbon $contract_date
 * @property \Carbon\Carbon $contract_start_date
 * @property ?\Carbon\Carbon $contract_end_date
 * @property bool $contract_auto_update
 * @property string $customer_contact_user_name
 * @property string $customer_contact_user_dept
 * @property string $customer_contact_user_title
 * @property string $customer_contact_user_email
 * @property string $customer_contract_user_name
 * @property string $customer_contract_user_dept
 * @property string $customer_contract_user_title
 * @property string $customer_contract_user_email
 * @property string $customer_payment_user_name
 * @property string $customer_payment_user_dept
 * @property string $customer_payment_user_title
 * @property string $customer_payment_user_email
 * @property string $service_rep_user_name
 * @property string $service_rep_user_public_id
 * @property string $service_mgr_user_name
 * @property string $service_mgr_user_public_id
 * @property string $invoice_remind_days
 * @property string $billing_cycle
 * @property string $billing_cycle_code
 * @property ?string $remarks
 */
class ShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     serviceContractPublicId: string,
     *     tenantName: string,
     *     servicePublicId: string,
     *     serviceName: string,
     *     servicePlanPublicId: string,
     *     servicePlanName: string,
     *     customerPublicId: string,
     *     customerName: string,
     *     customerNameEn: string,
     *     contractName: string,
     *     contractLanguageName: string,
     *     contractLanguage: string,
     *     contractStatus: string,
     *     contractStatusCode: string,
     *     serviceUsageStatus: string,
     *     serviceUsageStatusCode: string,
     *     contractDate: string,
     *     contractStartDate: string,
     *     contractEndDate: ?string,
     *     contractAutoUpdate: bool,
     *     customerContactUserName: string,
     *     customerContactUserDept: string,
     *     customerContactUserTitle: string,
     *     customerContactUserEmail: string,
     *     customerContractUserName: string,
     *     customerContractUserDept: string,
     *     customerContractUserTitle: string,
     *     customerContractUserEmail: string,
     *     customerPaymentUserName: string,
     *     customerPaymentUserDept: string,
     *     customerPaymentUserTitle: string,
     *     customerPaymentUserEmail: string,
     *     serviceRepUserName: string,
     *     serviceRepUserPublicId: string,
     *     serviceMgrUserName: string,
     *     serviceMgrUserPublicId: string,
     *     invoiceRemindDays: string,
     *     billingCycle: string,
     *     billingCycleCode: string,
     *     remarks: ?string,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'serviceContractPublicId' => $this->service_contracts_public_id,
            'tenantName' => $this->tenant_name,
            'servicePublicId' => $this->service_public_id,
            'serviceName' => $this->service_name,
            'servicePlanPublicId' => $this->service_plan_public_id,
            'servicePlanName' => $this->service_plan_name,
            'customerPublicId' => $this->customer_public_id,
            'customerName' => $this->company_legal_name,
            'customerNameEn' => $this->company_name_en,
            'contractName' => $this->contract_name,
            'contractLanguageName' => $this->contract_language_name,
            'contractLanguage' => $this->contract_language,
            'contractStatus' => $this->contract_status,
            'contractStatusCode' => $this->contract_status_code,
            'serviceUsageStatus' => $this->service_usage_status,
            'serviceUsageStatusCode' => $this->service_usage_status_code,
            'contractDate' => $this->contract_date->format('Y-m-d'),
            'contractStartDate' => $this->contract_start_date->format('Y-m-d'),
            'contractEndDate' => $this->contract_end_date?->format('Y-m-d'),
            'contractAutoUpdate' => $this->contract_auto_update,
            'customerContactUserName' => $this->customer_contact_user_name,
            'customerContactUserDept' => $this->customer_contact_user_dept,
            'customerContactUserTitle' => $this->customer_contact_user_title,
            'customerContactUserEmail' => $this->customer_contact_user_email,
            'customerContractUserName' => $this->customer_contract_user_name,
            'customerContractUserDept' => $this->customer_contract_user_dept,
            'customerContractUserTitle' => $this->customer_contract_user_title,
            'customerContractUserEmail' => $this->customer_contract_user_email,
            'customerPaymentUserName' => $this->customer_payment_user_name,
            'customerPaymentUserDept' => $this->customer_payment_user_dept,
            'customerPaymentUserTitle' => $this->customer_payment_user_title,
            'customerPaymentUserEmail' => $this->customer_payment_user_email,
            'serviceRepUserName' => $this->service_rep_user_name,
            'serviceRepUserPublicId' => $this->service_rep_user_public_id,
            'serviceMgrUserName' => $this->service_mgr_user_name,
            'serviceMgrUserPublicId' => $this->service_mgr_user_public_id,
            'invoiceRemindDays' => empty($this->invoice_remind_days) ? null : trim($this->invoice_remind_days, '{}'),
            'billingCycle' => $this->billing_cycle,
            'billingCycleCode' => $this->billing_cycle_code,
            'remarks' => $this->remarks,
        ];
    }
}
