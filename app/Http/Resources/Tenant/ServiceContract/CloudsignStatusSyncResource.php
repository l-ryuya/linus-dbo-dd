<?php

declare(strict_types=1);

namespace App\Http\Resources\Tenant\ServiceContract;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $contractStatus
 */
class CloudsignStatusSyncResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     contractStatus: string,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'contractStatus' => $this->contractStatus,
        ];
    }
}
