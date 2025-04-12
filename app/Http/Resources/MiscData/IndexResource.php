<?php
declare(strict_types = 1);

namespace App\Http\Resources\MiscData;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $selection_item_type
 * @property string $selection_item_code
 * @property string $selection_item_name
 * @property string $selection_item_short_name
 */
class IndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array{selectionItemType: string, selectionItemCode: string, selectionItemName: string, selectionItemShortName: string}
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

