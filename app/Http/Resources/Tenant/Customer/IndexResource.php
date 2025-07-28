<?php

declare(strict_types=1);

namespace App\Http\Resources\Tenant\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $public_id
 * @property string $company_legal_name
 * @property string $company_name_en
 * @property string $customer_status
 * @property string $contract_start_date
 */
class IndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     customerPublicId: string,
     *     customerName: string,
     *     customerNameEn: string,
     *     customerStatus: string,
     *     serviceStartDate: string|null,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'customerPublicId' => $this->public_id,
            'customerName' => $this->company_legal_name,
            'customerNameEn' => $this->company_name_en,
            'customerStatus' => $this->customer_status,
            'serviceStartDate' => $this->contract_start_date,
        ];
    }
}
