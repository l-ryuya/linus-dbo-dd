<?php

declare(strict_types=1);

namespace App\Http\Resources\CountryRegion;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $country_code_alpha3
 * @property string $country_code_alpha2
 * @property integer $country_code_numeric
 * @property string $world_region
 * @property string $country_region_name
 * @property string $capital_name
 */
class IndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array{
     *     countryCodeAlpha3: string,
     *     countryCodeAlpha2: string,
     *     countryCodeNumeric: int,
     *     worldRegion: string,
     *     countryRegionName: string,
     *     capitalName: string
     * }
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
