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
 */
class IndexServiceContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array{
     *     serviceContractCode: string,
     *     serviceCode: string,
     *     serviceName: string,
     *     servicePlanName: string,
     *     servicePlanCode: string,
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
        ];
    }
}
