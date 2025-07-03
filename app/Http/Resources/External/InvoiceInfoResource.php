<?php

declare(strict_types=1);

namespace App\Http\Resources\External;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $public_id
 * @property string $sales_rep_company_name
 * @property string $service_rep_name
 * @property string $service_rep_mail
 * @property string $service_rep_phone_number
 * @property string $customer_payment_user_name
 * @property string $customer_payment_user_dept
 * @property string $customer_payment_user_title
 * @property string $customer_payment_user_mail
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
     *         salesRepPhoneNumber: string
     *     },
     *     recipient: array{
     *         userName: string,
     *         userDept: string,
     *         userTitle: string,
     *         userMail: string
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
                'salesRepEmail' => $this->service_rep_mail,
                'salesRepPhoneNumber' => $this->service_rep_phone_number,
            ],
            'recipient' => [
                'userName' => $this->customer_payment_user_name,
                'userDept' => $this->customer_payment_user_dept,
                'userTitle' => $this->customer_payment_user_title,
                'userMail' => $this->customer_payment_user_mail,
            ],
        ];
    }
}
