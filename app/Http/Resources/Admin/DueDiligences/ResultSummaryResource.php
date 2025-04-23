<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\DueDiligences;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \App\Models\DueDiligence $self
 * @property \Illuminate\Support\Collection<int, Object> $summaries
 */
class ResultSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     ddCode: string,
     *     companyName: string,
     *     ddStatus: string|null,
     *     ddSummaries: \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'ddCode' => $this->self->dd_code,
            'companyName' => $this->self->company_name,
            'ddStatus' => $this->self->dd_status,
            'ddSummaries' => ResultSummaryRelationResource::collection($this->summaries),
        ];
    }
}

