<?php

declare(strict_types=1);

namespace App\Http\Resources\Tenant\DdCase;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $stepNumber
 * @property string $stepName
 * @property string $ddStepCode
 * @property string $stepInfo
 */
class StepResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     stepNumber: int,
     *     stepName: string,
     *     ddStepCode: string,
     *     stepInfo: string,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'stepNumber' => $this->stepNumber,
            'stepName' => $this->stepName,
            'ddStepCode' => $this->ddStepCode,
            'stepInfo' => $this->stepInfo,
        ];
    }
}
