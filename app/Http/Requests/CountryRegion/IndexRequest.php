<?php

declare(strict_types=1);

namespace App\Http\Requests\CountryRegion;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'countryCodeAlpha3' => ['nullable', 'string', 'min:3', 'max:3'],
            'countryCodeAlpha2' => ['nullable', 'string', 'min:2', 'max:2'],
            'countryCodeNumeric' => ['nullable', 'integer', 'min:1', 'max:3'],
        ];
    }
}
