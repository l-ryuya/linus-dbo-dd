<?php

declare(strict_types=1);

namespace App\Http\Resources\Tenant\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $companyPublicId
 * @property string $customerPublicId
 */
class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array{companyPublicId: string, customerPublicId: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'companyPublicId' => $this->companyPublicId,
            'customerPublicId' => $this->customerPublicId,
        ];
    }
}
