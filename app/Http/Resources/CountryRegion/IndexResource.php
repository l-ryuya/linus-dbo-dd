<?php

namespace App\Http\Resources\CountryRegion;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'country_code_alpha3' => $this->country_code_alpha3,
            'country_code_alpha2' => $this->country_code_alpha2,
            'country_code_numeric' => $this->country_code_numeric,
            'world_region' => $this->world_region,
            'country_region_name' => $this->country_region_name,
            'capital_name' => $this->capital_name,
        ];
    }
}
