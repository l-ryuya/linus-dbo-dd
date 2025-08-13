<?php

declare(strict_types=1);

namespace App\Http\Resources\Tenant\DdCase;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $public_id
 * @property string $tenant_name
 * @property string $company_name
 * @property string $dd_case_no
 * @property string $current_dd_step
 * @property ?string $overall_result
 * @property ?string $customer_risk_level
 * @property ?string $started_at
 * @property ?string $ended_at
 */
class IndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     ddCasePublicId: string,
     *     tenantName: string,
     *     companyName: string,
     *     ddCaseNo: string,
     *     currentDdStep: string,
     *     overallResult: string|null,
     *     customerRiskLevel: string|null,
     *     startedAt: string|null,
     *     endedAt: string|null,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'ddCasePublicId' => $this->public_id,
            'tenantName' => $this->tenant_name,
            'companyName' => $this->company_name,
            'ddCaseNo' => $this->dd_case_no,
            'currentDdStep' => $this->current_dd_step,
            'overallResult' => $this->overall_result,
            'customerRiskLevel' => $this->customer_risk_level,
            'startedAt' => $this->started_at,
            'endedAt' => $this->ended_at,
        ];
    }
}
