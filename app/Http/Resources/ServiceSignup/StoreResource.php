<?php

namespace App\Http\Resources\ServiceSignup;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
     * @return array
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
