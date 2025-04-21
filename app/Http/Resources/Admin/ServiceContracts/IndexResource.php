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
 * @property \Illuminate\Support\Collection<int, Object> $service_contracts
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
     *     serviceContracts: \Illuminate\Http\Resources\Json\AnonymousResourceCollection,
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
            'serviceContracts' => IndexServiceContractResource::collection($this->service_contracts),
        ];
    }
}
