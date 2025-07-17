<?php

declare(strict_types=1);

namespace App\Http\Resources\Tenant\ServiceContract;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $public_id
 * @property string $tenant_name
 * @property string $service_name
 * @property string $service_plan_name
 * @property string $company_legal_name
 * @property string $company_name_en
 * @property string $contract_name
 * @property string $contract_status
 * @property string $service_usage_status
 * @property string $contract_date
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
     *     serviceContractPublicId: string,
     *     tenantName: string,
     *     serviceName: string|null,
     *     servicePlanName: string|null,
     *     customerName: string,
     *     customerNameEn: string|null,
     *     contractName: string|null,
     *     contractStatus: string|null,
     *     serviceUsageStatus: string|null,
     *     contractDate: string|null,
     *     contractStartDate: string|null,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'serviceContractPublicId' => $this->public_id,
            'tenantName' => $this->tenant_name,
            'serviceName' => $this->service_name,
            'servicePlanName' => $this->service_plan_name,
            'customerName' => $this->company_legal_name,
            'customerNameEn' => $this->company_name_en,
            'contractName' => $this->contract_name,
            'contractStatus' => $this->contract_status,
            'serviceUsageStatus' => $this->service_usage_status,
            'contractDate' => $this->contract_date,
            'contractStartDate' => $this->contract_start_date,
        ];
    }
}
