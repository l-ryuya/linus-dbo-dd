<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeResource extends JsonResource
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
            'userCode' => $this->user_code,
            'lastName' => $this->last_name_en,
            'middleName' => $this->middle_name_en,
            'firstName' => $this->first_name_en,
            'email' => $this->email,
            'roles' => $this->roles,
        ];
    }
}
