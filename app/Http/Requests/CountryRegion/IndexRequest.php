<?php

namespace App\Http\Requests\CountryRegion;

use App\Http\Requests\Traits\PaginationRules;
use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    use PaginationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'country_code_alpha3' => ['nullable', 'string', 'min:3', 'max:3'],
            'country_code_alpha2' => ['nullable', 'string', 'min:2', 'max:2'],
            'country_code_numeric' => ['nullable', 'integer', 'min:1', 'max:3'],
        ]);
    }
}
