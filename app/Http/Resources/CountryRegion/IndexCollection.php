<?php
declare(strict_types = 1);

namespace App\Http\Resources\CountryRegion;

use App\Http\Resources\Traits\CamelCasePaginationMeta;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IndexCollection extends ResourceCollection
{
    use CamelCasePaginationMeta;

    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return IndexResource::collection($this->collection)->toArray($request);
    }
}
