<?php

declare(strict_types=1);

namespace App\Http\Resources\ServicePlan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $public_id
 * @property string $service_plan_status
 * @property string $service_plan_status_code
 * @property string $billing_cycle
 * @property float $unit_price
 * @property \Carbon\Carbon|null $service_start_date
 * @property \Carbon\Carbon|null $service_end_date
 * @property string $service_plan_name
 * @property string $service_plan_description
 */
class IndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     servicePlanPublicId: string,
     *     servicePlanStatus: string,
     *     servicePlanStatusCode: string,
     *     billingCycle: string,
     *     unitPrice: float,
     *     serviceStartDate: string|null,
     *     serviceEndDate: string|null,
     *     servicePlanName: string,
     *     servicePlanDescription: string
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'servicePlanPublicId' => $this->public_id,
            'servicePlanStatus' => $this->service_plan_status,
            'servicePlanStatusCode' => $this->service_plan_status_code,
            'billingCycle' => $this->billing_cycle,
            'unitPrice' => (float) $this->unit_price,
            'serviceStartDate' => $this->service_start_date?->format('Y-m-d'),
            'serviceEndDate' => $this->service_end_date?->format('Y-m-d'),
            'servicePlanName' => $this->service_plan_name,
            'servicePlanDescription' => $this->service_plan_description,
        ];
    }
}
