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
 * @property ?\Carbon\Carbon $first_service_start_date
 * @property ?\Carbon\Carbon $last_service_end_date
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
     *     firstServiceStartDate: string|null,
     *     lastServiceEndDate: string|null
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'customerPublicId' => $this->public_id,
            'customerName' => $this->company_legal_name,
            'customerNameEn' => $this->company_name_en,
            'customerStatus' => $this->customer_status,
            'firstServiceStartDate' => $this->first_service_start_date?->format('Y-m-d'),
            'lastServiceEndDate' => $this->last_service_end_date?->format('Y-m-d'),
        ];
    }
}
