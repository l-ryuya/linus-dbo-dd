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
            'service_code' => $this->service_code,
            'service_plan_code' => $this->service_plan_code,
            'service_plan_status_type' => $this->service_plan_status_type,
            'service_plan_status' => $this->service_plan_status,
            'billing_cycle' => $this->billing_cycle,
            'unit_price' => (float) $this->unit_price,
            'service_start_date' => $this->service_start_date ? $this->service_start_date->format('Y-m-d') : null,
            'service_end_date' => $this->service_end_date ? $this->service_end_date->format('Y-m-d') : null,
            'service_plan_name' => $this->service_plan_name,
            'service_plan_description' => $this->service_plan_description,
        ];
    }
}
