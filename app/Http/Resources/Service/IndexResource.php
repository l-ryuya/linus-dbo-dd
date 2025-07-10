<?php

declare(strict_types=1);

namespace App\Http\Resources\Service;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $public_id
 * @property string $service_status
 * @property string $service_status_code
 * @property \Carbon\Carbon|null $service_start_date
 * @property \Carbon\Carbon|null $service_end_date
 * @property string $service_condition
 * @property string $dd_plan
 * @property string $service_name
 * @property string $service_description
 */
class IndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     servicePublicId: string,
     *     serviceStatus: string,
     *     serviceStatusCode: string,
     *     serviceStartDate: string|null,
     *     serviceEndDate: string|null,
     *     serviceCondition: string,
     *     ddPlan: string,
     *     serviceName: string,
     *     serviceDescription: string
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'servicePublicId' => $this->public_id,
            'serviceStatus' => $this->service_status,
            'serviceStatusCode' => $this->service_status_code,
            'serviceStartDate' => $this->service_start_date?->format('Y-m-d'),
            'serviceEndDate' => $this->service_end_date?->format('Y-m-d'),
            'serviceCondition' => $this->service_condition,
            'ddPlan' => $this->dd_plan,
            'serviceName' => $this->service_name,
            'serviceDescription' => $this->service_description,
        ];
    }
}
