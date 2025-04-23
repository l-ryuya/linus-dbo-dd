<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\ServiceContracts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $company_code
 * @property string $company_name_en
 * @property string $company_status
 * @property string|null $postal_code_en
 * @property string|null $prefecture_en
 * @property string|null $city_en
 * @property string|null $street_en
 * @property string|null $building_room_en
 * @property \App\Models\DueDiligence|null $latestDd
 * @property string|null $dd_status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Collection<int, Object> $service_contracts
 */
class ShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     companyCode: string,
     *     companyName: string,
     *     companyStatus: string,
     *     postalCode: string|null,
     *     prefecture: string|null,
     *     city: string|null,
     *     street: string|null,
     *     buildingRoom: string|null,
     *     latestDdCode: string|null,
     *     ddStatus: string|null,
     *     serviceContracts: \Illuminate\Http\Resources\Json\AnonymousResourceCollection,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'companyCode' => $this->company_code,
            'companyName' => $this->company_name_en,
            'companyStatus' => $this->company_status,
            'postalCode' => $this->postal_code_en,
            'prefecture' => $this->prefecture_en,
            'city' => $this->city_en,
            'street' => $this->street_en,
            'buildingRoom' => $this->building_room_en,
            'latestDdCode' => $this->latestDd?->dd_code,
            'ddStatus' => $this->dd_status,
            'serviceContracts' => ShowServiceContractResource::collection($this->service_contracts),
        ];
    }
}
