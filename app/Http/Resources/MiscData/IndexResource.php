<?php

namespace App\Http\Resources\MiscData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'selection_item_type' => $this->selection_item_type,
            'selection_item_code' => $this->selection_item_code,
            'selection_item_name' => $this->selection_item_name,
            'selection_item_short_name' => $this->selection_item_short_name,
        ];
    }
}
