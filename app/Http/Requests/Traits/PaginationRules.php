<?php

declare(strict_types=1);

namespace App\Http\Requests\Traits;

trait PaginationRules
{
    /**
     * @return string[]
     */
    public function paginationRules(): array
    {
        return [
            'page' => 'required|integer|min:1|max:1000',
            'displayed' => 'nullable|integer|min:10|max:100',
        ];
    }
}
