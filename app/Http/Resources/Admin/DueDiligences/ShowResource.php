<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\DueDiligences;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $dd_code
 * @property string $company_name
 * @property string|null $company_code
 * @property string|null $dd_status
 * @property string|null $ai_dd_result
 * @property \Illuminate\Support\Carbon|null $ai_dd_completed_date
 * @property string|null $primary_dd_result
 * @property \App\Models\User|null $primaryDdUser
 * @property \Illuminate\Support\Carbon|null $primary_dd_completed_date
 * @property string|null $primary_dd_comment
 * @property string|null $final_dd_result
 * @property \App\Models\User|null $finalDdUser
 * @property \Illuminate\Support\Carbon|null $final_dd_completed_date
 * @property string|null $final_dd_comment
 */
class ShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     ddCode: string,
     *     companyName: string,
     *     companyCode: string|null,
     *     ddStatus: string|null,
     *     aiDdResult: string|null,
     *     aiDdCompletedDate: string|null,
     *     primaryDdResult: string|null,
     *     primaryDdUserCode: string|null,
     *     primaryDdUserName: string|null,
     *     primaryDdCompletedDate: string|null,
     *     primaryDdComment: string|null,
     *     finalDdResult: string|null,
     *     finalDdUserCode: string|null,
     *     finalDdUserName: string|null,
     *     finalDdCompletedDate: string|null,
     *     finalDdComment: string|null,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'ddCode' => $this->dd_code,
            'companyName' => $this->company_name,
            'companyCode' => $this->company_code,
            'ddStatus' => $this->dd_status,
            'aiDdResult' => $this->ai_dd_result,
            'aiDdCompletedDate' => $this->ai_dd_completed_date?->format('Y-m-d'),
            'primaryDdResult' => $this->primary_dd_result,
            'primaryDdUserCode' => $this->primaryDdUser?->user_code,
            'primaryDdUserName' => $this->primaryDdUser?->getFullNameEn(),
            'primaryDdCompletedDate' => $this->primary_dd_completed_date?->format('Y-m-d'),
            'primaryDdComment' => $this->primary_dd_comment,
            'finalDdResult' => $this->final_dd_result,
            'finalDdUserCode' => $this->finalDdUser?->user_code,
            'finalDdUserName' => $this->finalDdUser?->getFullNameEn(),
            'finalDdCompletedDate' => $this->final_dd_completed_date?->format('Y-m-d'),
            'finalDdComment' => $this->final_dd_comment,
        ];
    }
}
