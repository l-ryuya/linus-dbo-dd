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
            'selectionItemType' => $this->selection_item_type,
            'selectionItemCode' => $this->selection_item_code,
            'selectionItemName' => $this->selection_item_name,
            'selectionItemShortName' => $this->selection_item_short_name,
        ];
    }
}
