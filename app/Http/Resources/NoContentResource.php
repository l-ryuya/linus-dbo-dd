<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 204を返す
 */
class NoContentResource extends JsonResource
{
    public function __construct()
    {
        parent::__construct([]);
    }

    public function toResponse($request): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    {
        return response()->noContent();
    }
}
