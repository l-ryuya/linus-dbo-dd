<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\Companies;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $company_code
 * @property string $company_name_en
 * @property string $company_status_type
 * @property string $company_status_code
 * @property \Illuminate\Support\Carbon $created_at
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
     *     companyStatusType: string,
     *     companyStatusCode: string,
     *     signupDate: string|null,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'companyCode' => $this->company_code,
            'companyName' => $this->company_name_en,
            'companyStatusType' => $this->company_status_type,
            'companyStatusCode' => $this->company_status_code,
            'signupDate' => convertToUserTimezone($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
