<?php

declare(strict_types=1);

namespace App\Http\Requests\CountryRegion;

use App\Http\Requests\Traits\PaginationRules;
use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    use PaginationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'countryCodeAlpha3' => ['nullable', 'string', 'min:3', 'max:3'],
            'countryCodeAlpha2' => ['nullable', 'string', 'min:2', 'max:2'],
            'countryCodeNumeric' => ['nullable', 'integer', 'min:1', 'max:3'],
        ]);
    }
}
