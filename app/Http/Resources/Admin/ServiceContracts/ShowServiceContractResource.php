<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\ServiceContracts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $service_contract_code
 * @property string $service_code
 * @property string $service_name
 * @property string $service_plan_code
 * @property string $service_plan_name
 * @property string $department_name
 * @property string $service_usage_status
 * @property string $service_contract_status
 * @property \App\Models\User $person_in_charge
 * @property \App\Models\User $contract_manager
 */
class ShowServiceContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     serviceContractCode: string,
     *     serviceCode: string,
     *     serviceName: string,
     *     servicePlanCode: string,
     *     servicePlanName: string,
     *     departmentName: string,
     *     serviceUsageStatus: string,
     *     serviceContractStatus: string,
     *     personInCharge: \Illuminate\Http\Resources\Json\JsonResource,
     *     contractManager: \Illuminate\Http\Resources\Json\JsonResource,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'serviceContractCode' => $this->service_contract_code,
            'serviceCode' => $this->service_code,
            'serviceName' => $this->service_name,
            'servicePlanCode' => $this->service_plan_code,
            'servicePlanName' => $this->service_plan_name,
            'departmentName' => $this->department_name,
            'serviceUsageStatus' => $this->service_usage_status,
            'serviceContractStatus' => $this->service_contract_status,
            'personInCharge' => new ShowManagerResource($this->person_in_charge),
            'contractManager' => new ShowManagerResource($this->contract_manager),
        ];
    }
}
