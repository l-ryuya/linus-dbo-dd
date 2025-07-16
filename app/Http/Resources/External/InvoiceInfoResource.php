<?php

declare(strict_types=1);

namespace App\Http\Resources\External;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $public_id
 * @property string $sales_rep_company_name
 * @property string $service_rep_name
 * @property string $service_rep_email
 * @property string $service_rep_phone_number
 * @property string $sales_rep_manager_company_name
 * @property string $service_rep_manager_name
 * @property string $service_rep_manager_email
 * @property string $service_rep_manager_phone_number
 * @property string $service_dept_group_email
 * @property string $backoffice_group_email
 * @property string $customer_payment_user_name
 * @property string $customer_payment_user_dept
 * @property string $customer_payment_user_title
 * @property string $customer_payment_user_email
 */
class InvoiceInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     serviceContractPublicId: string,
     *     sender: array{
     *         salesRepCompanyName: string,
     *         salesRepName: string,
     *         salesRepEmail: string,
     *         salesRepPhoneNumber: string,
     *         salesRepManagerCompanyName: string,
     *         salesRepManagerName: string,
     *         salesRepManagerEmail: string,
     *         salesRepManagerPhoneNumber: string,
     *         serviceDeptGroupEmail: string,
     *         backofficeGroupEmail: string
     *     },
     *     recipient: array{
     *         userName: string,
     *         userDept: string,
     *         userTitle: string,
     *         userEmail: string
     *     }
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'serviceContractPublicId' => $this->public_id,
            'sender' => [
                'salesRepCompanyName' => $this->sales_rep_company_name,
                'salesRepName' => $this->service_rep_name,
                'salesRepEmail' => $this->service_rep_email,
                'salesRepPhoneNumber' => $this->service_rep_phone_number,
                'salesRepManagerCompanyName' => $this->sales_rep_manager_company_name,
                'salesRepManagerName' => $this->service_rep_manager_name,
                'salesRepManagerEmail' => $this->service_rep_manager_email,
                'salesRepManagerPhoneNumber' => $this->service_rep_manager_phone_number,
                'serviceDeptGroupEmail' => $this->service_dept_group_email,
                'backofficeGroupEmail' => $this->backoffice_group_email,
            ],
            'recipient' => [
                'userName' => $this->customer_payment_user_name,
                'userDept' => $this->customer_payment_user_dept,
                'userTitle' => $this->customer_payment_user_title,
                'userEmail' => $this->customer_payment_user_email,
            ],
        ];
    }
}
