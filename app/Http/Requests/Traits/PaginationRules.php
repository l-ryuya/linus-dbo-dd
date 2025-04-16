<?php

declare(strict_types=1);

namespace App\Http\Requests\Traits;

trait PaginationRules
{
    protected int $defaultDisplayed = 10;

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

    /**
     * @return array<string, mixed>
     */
    protected function paginationDefaults(): array
    {
        return [
            'page'      => (int) $this->input('page'),
            'displayed' => (int) $this->input('displayed', $this->defaultDisplayed),
        ];
    }
}
