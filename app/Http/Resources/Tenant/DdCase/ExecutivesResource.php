<?php

declare(strict_types=1);

namespace App\Http\Resources\Tenant\DdCase;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $dd_relation_public_id
 * @property string $executive_name
 * @property string $position
 * @property string|null $customer_risk_level
 * @property string|null $asf_check
 * @property string|null $rep_check
 */
class ExecutivesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     ddRelationPublicId: string,
     *     executiveName: string,
     *     position: string,
     *     asfCheckResult: string|null,
     *     repCheckResult: string|null,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'ddRelationPublicId' => $this->dd_relation_public_id,
            'executiveName' => $this->executive_name,
            'position' => $this->position,
            'asfCheckResult' => $this->asf_check ?? null,
            'repCheckResult' => $this->rep_check ?? null,
        ];
    }
}
