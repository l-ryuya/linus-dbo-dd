<?php

namespace App\Http\Resources\CountryRegion;

use App\Http\Resources\Traits\CamelCasePaginationMeta;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IndexCollection extends ResourceCollection
{
    use CamelCasePaginationMeta;

    public function toArray($request): array
    {
        return IndexResource::collection($this->collection)->toArray($request);
    }
}
