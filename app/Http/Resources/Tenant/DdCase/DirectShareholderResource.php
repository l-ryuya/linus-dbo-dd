<?php

declare(strict_types=1);

namespace App\Http\Resources\Tenant\DdCase;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $dd_relation_public_id
 * @property string $shareholder_name
 * @property float $shareholding_ratio
 * @property string|null $industry_check_reg
 * @property string|null $industry_check_web
 * @property string|null $asf_check
 * @property string|null $rep_check
 * @property string|null $exchange_name
 * @property string|null $securities_code
 */
class DirectShareholderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     ddRelationPublicId: string,
     *     shareholderName: string,
     *     shareholdingRatio: float|null,
     *     industryCheckReg: string|null,
     *     industryCheckWeb: string|null,
     *     asfCheckResult: string|null,
     *     repCheckResult: string|null,
     *     exchangeName: string|null,
     *     securitiesCode: string|null,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'ddRelationPublicId' => $this->dd_relation_public_id,
            'shareholderName' => $this->shareholder_name,
            'shareholdingRatio' => $this->shareholding_ratio !== null ? (float) $this->shareholding_ratio : null,
            'industryCheckReg' => $this->industry_check_reg ?? null,
            'industryCheckWeb' => $this->industry_check_web ?? null,
            'asfCheckResult' => $this->asf_check ?? null,
            'repCheckResult' => $this->rep_check ?? null,
            'exchangeName' => $this->exchange_name ?? null,
            'securitiesCode' => $this->securities_code ?? null,
        ];
    }
}
