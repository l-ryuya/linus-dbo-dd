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
            'countryCodeAlpha3' => $this->country_code_alpha3,
            'countryCodeAlpha2' => $this->country_code_alpha2,
            'countryCodeNumeric' => $this->country_code_numeric,
            'worldRegion' => $this->world_region,
            'countryRegionName' => $this->country_region_name,
            'capitalName' => $this->capital_name,
        ];
    }
}
