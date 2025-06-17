<?php

declare(strict_types=1);

namespace App\Http\Resources\Tenant\Customers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $public_id
 * @property string $legal_name
 * @property string $customer_status
 * @property string $contract_start_date
 * @property string $service_name
 * @property string $service_plan_name
 */
class IndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     customerCompanyPublicId: string,
     *     customerName: string,
     *     customerStatus: string,
     *     serviceStartDate: string|null,
     *     service: string|null,
     *     servicePlan: string|null,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'customerCompanyPublicId' => $this->public_id,
            'customerName' => $this->legal_name,
            'customerStatus' => $this->customer_status,
            'serviceStartDate' => $this->contract_start_date,
            'service' => $this->service_name,
            'servicePlan' => $this->service_plan_name,
        ];
    }
}
