<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\DueDiligences;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $dd_code
 * @property string $company_name
 * @property string $dd_entity_type
 * @property string $dd_relation_type
 * @property string|null $individual_last_name
 * @property string|null $individual_middle_name
 * @property string|null $individual_first_name
 * @property string|null $position
 * @property string|null $investment_sources
 * @property string|null $investment_targets
 * @property string|null $shareholding_ratio
 */
class ResultSummaryRelationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     ddCode: string,
     *     companyName: string,
     *     ddEntityType: string,
     *     ddRelationType: string,
     *     individualLastName: string|null,
     *     individualMiddleName: string|null,
     *     individualFirstName: string|null,
     *     position: string|null,
     *     investmentSources: string|null,
     *     investmentTargets: string|null,
     *     shareholdingRatio: string|null,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'ddCode' => $this->dd_code,
            'companyName' => $this->company_name,
            'ddEntityType' => $this->dd_entity_type,
            'ddRelationType' => $this->dd_relation_type,
            'individualLastName' => $this->individual_last_name,
            'individualMiddleName' => $this->individual_middle_name,
            'individualFirstName' => $this->individual_first_name,
            'position' => $this->position,
            'investmentSources' => $this->investment_sources,
            'investmentTargets' => $this->investment_targets,
            'shareholdingRatio' => $this->shareholding_ratio,
        ];
    }
}

