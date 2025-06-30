<?php

declare(strict_types=1);

namespace App\Http\Resources\Tenant\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $public_id
 * @property string $contract_name
 * @property string $service_name
 * @property string $service_plan_name
 * @property string $service_usage_status
 * @property string $service_usage_status_code
 * @property string $contract_status
 * @property string $contract_status_code
 */
class ServiceContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     publicId: string,
     *     contractName: string,
     *     serviceName: string,
     *     servicePlanName: string,
     *     serviceUsageStatus: string,
     *     serviceUsageStatusCode: string,
     *     contractStatus: string,
     *     contractStatusCode: string,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'publicId' => $this->public_id,
            'contractName' => $this->contract_name,
            'serviceName' => $this->service_name,
            'servicePlanName' => $this->service_plan_name,
            'serviceUsageStatus' => $this->service_usage_status,
            'serviceUsageStatusCode' => $this->service_usage_status_code,
            'contractStatus' => $this->contract_status,
            'contractStatusCode' => $this->contract_status_code,
        ];
    }
}
