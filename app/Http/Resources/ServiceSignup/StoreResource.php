<?php
declare(strict_types = 1);

namespace App\Http\Resources\ServiceSignup;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $contactPersonUserCode
 * @property string $contractPersonUserCode
 * @property string $companyCode
 * @property string $serviceContractCode
 */
class StoreResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array{contactPersonUserCode: string, contractPersonUserCode: string, companyCode: string, serviceContractCode: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'contactPersonUserCode' => $this->contactPersonUserCode,
            'contractPersonUserCode' => $this->contractPersonUserCode,
            'companyCode' => $this->companyCode,
            'serviceContractCode' => $this->serviceContractCode,
        ];
    }
}

