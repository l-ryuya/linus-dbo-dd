<?php

declare(strict_types=1);

namespace App\Http\Resources\Tenant\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property object $customer
 * @property object $company
 * @property \Illuminate\Support\Collection<int, Object> $serviceContracts
 */
class ShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     customerPublicId: string,
     *     customerStatus: string,
     *     customerStatusCode: string,
     *     firstServiceStartDate: ?string,
     *     lastServiceEndDate: ?string,
     *     customerNameEn: string,
     *     customerName: string,
     *     websiteUrl: string,
     *     shareholdersUrl: string,
     *     executivesUrl: string,
     *     defaultLanguageCode: string,
     *     countryCodeAlpha3: string,
     *     postal: ?string,
     *     state: ?string,
     *     city: ?string,
     *     street: ?string,
     *     building: ?string,
     *     remarks: ?string,
     *     serviceContracts: \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'customerPublicId' => $this->customer->public_id,
            'customerStatus' => $this->customer->customer_status,
            'customerStatusCode' => $this->customer->customer_status_code,
            'firstServiceStartDate' => $this->customer->first_service_start_date?->format('Y-m-d'),
            'lastServiceEndDate' => $this->customer->last_service_end_date?->format('Y-m-d'),
            'customerNameEn' => $this->company->company_name_en,
            'customerName' => $this->company->company_legal_name,
            'websiteUrl' => $this->company->website_url,
            'shareholdersUrl' => $this->company->shareholders_url,
            'executivesUrl' => $this->company->executives_url,
            'defaultLanguageCode' => $this->company->default_language_code,
            'countryCodeAlpha3' => $this->company->country_code_alpha3,
            'postal' => $this->company->postal,
            'state' => $this->company->state,
            'city' => $this->company->city,
            'street' => $this->company->street,
            'building' => $this->company->building,
            'remarks' => $this->company->remarks,
            'serviceContracts' => ServiceContractResource::collection($this->serviceContracts),
        ];
    }
}
