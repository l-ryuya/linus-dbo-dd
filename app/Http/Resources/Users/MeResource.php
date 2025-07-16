<?php

declare(strict_types=1);

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $public_id
 * @property string $role_name
 * @property string $company_name
 * @property string $service_name
 * @property string $user_name
 * @property string $user_email
 * @property string|null $user_icon_url
 * @property string $country_region_name
 * @property string $country_code_alpha3
 * @property string $language_name
 * @property string $language_code
 * @property string $time_zone_name
 * @property string $time_zone_id
 * @property string $date_format
 * @property string|null $phone_number
 */
class MeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     userPublicId: string,
     *     roleName: string,
     *     companyName: string,
     *     serviceName: string|null,
     *     userName: string,
     *     userEmail: string,
     *     userIconUrl: string|null,
     *     countryRegionName: string,
     *     countryCodeAlpha3: string,
     *     languageName: string,
     *     languageCode: string,
     *     timeZoneName: string,
     *     timeZoneId: string,
     *     dateFormat: string,
     *     phoneNumber: string|null
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'userPublicId' => $this->public_id,
            'roleName' => $this->role_name,
            'companyName' => $this->company_name,
            'serviceName' => $this->service_name,
            'userName' => $this->user_name,
            'userEmail' => $this->user_email,
            'userIconUrl' => $this->user_icon_url,
            'countryRegionName' => $this->country_region_name,
            'countryCodeAlpha3' => $this->country_code_alpha3,
            'languageName' => $this->language_name,
            'languageCode' => $this->language_code,
            'timeZoneName' => $this->time_zone_name,
            'timeZoneId' => $this->time_zone_id,
            'dateFormat' => $this->date_format,
            'phoneNumber' => $this->phone_number,
        ];
    }
}
