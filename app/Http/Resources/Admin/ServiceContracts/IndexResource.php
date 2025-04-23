<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\ServiceContracts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $company_code
 * @property string $company_name_en
 * @property string $company_status
 * @property \Illuminate\Support\Carbon $created_at
 * @property string $final_dd_completed_date
 * @property string $service_contract_code
 * @property string $service_code
 * @property string $service_name
 * @property string $service_plan_code
 * @property string $service_plan_name
 */
class IndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array{
     *     companyCode: string,
     *     companyName: string,
     *     companyStatus: string,
     *     signupDate: string|null,
     *     activationDate: string|null,
     *     serviceContractCode: string,
     *     serviceCode: string,
     *     serviceName: string,
     *     servicePlanCode: string,
     *     servicePlanName: string,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'companyCode' => $this->company_code,
            'companyName' => $this->company_name_en,
            'companyStatus' => $this->company_status,
            'signupDate' => convertToUserTimezone($this->created_at)->format('Y-m-d'),
            'activationDate' => $this->final_dd_completed_date,
            'serviceContractCode' => $this->service_contract_code,
            'serviceCode' => $this->service_code,
            'serviceName' => $this->service_name,
            'servicePlanCode' => $this->service_plan_code,
            'servicePlanName' => $this->service_plan_name,
        ];
    }
}
