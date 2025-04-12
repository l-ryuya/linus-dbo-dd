<?php
declare(strict_types = 1);

namespace App\Http\Resources\ServicePlan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $service_code
 * @property string $service_plan_code
 * @property string $service_plan_status_type
 * @property string $service_plan_status
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
     *     serviceCode: string,
     *     servicePlanCode: string,
     *     servicePlanStatusType: string,
     *     servicePlanStatus: string,
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
            'serviceCode' => $this->service_code,
            'servicePlanCode' => $this->service_plan_code,
            'servicePlanStatusType' => $this->service_plan_status_type,
            'servicePlanStatus' => $this->service_plan_status,
            'billingCycle' => $this->billing_cycle,
            'unitPrice' => (float)$this->unit_price,
            'serviceStartDate' => $this->service_start_date?->format('Y-m-d'),
            'serviceEndDate' => $this->service_end_date?->format('Y-m-d'),
            'servicePlanName' => $this->service_plan_name,
            'servicePlanDescription' => $this->service_plan_description,
        ];
    }
}

