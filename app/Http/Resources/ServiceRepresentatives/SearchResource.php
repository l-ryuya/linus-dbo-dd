<?php

declare(strict_types=1);

namespace App\Http\Resources\ServiceRepresentatives;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $public_id
 * @property string $user_name
 */
class SearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array{userPublicId: string, userName: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'userPublicId' => $this->public_id,
            'userName' => $this->user_name,
        ];
    }
}
