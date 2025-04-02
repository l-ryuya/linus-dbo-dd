<?php

namespace App\Http\Resources\ServicePlan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'serviceCode' => $this->service_code,
            'servicePlanCode' => $this->service_plan_code,
            'servicePlanStatusType' => $this->service_plan_status_type,
            'servicePlanStatus' => $this->service_plan_status,
            'billingCycle' => $this->billing_cycle,
            'unitPrice' => (float) $this->unit_price,
            'serviceStartDate' => $this->service_start_date ? $this->service_start_date->format('Y-m-d') : null,
            'serviceEndDate' => $this->service_end_date ? $this->service_end_date->format('Y-m-d') : null,
            'servicePlanName' => $this->service_plan_name,
            'servicePlanDescription' => $this->service_plan_description,
        ];
    }
}
