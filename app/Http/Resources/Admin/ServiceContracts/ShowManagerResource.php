<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin\ServiceContracts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $last_name_en
 * @property string $middle_name_en
 * @property string $first_name_en
 * @property string $position_en
 * @property string $email
 */
class ShowManagerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array{
     *     lastName: string,
     *     middleName: string,
     *     firstName: string,
     *     position: string,
     *     email: string,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'lastName' => $this->last_name_en,
            'middleName' => $this->middle_name_en,
            'firstName' => $this->first_name_en,
            'position' => $this->position_en,
            'email' => $this->email,
        ];
    }
}
