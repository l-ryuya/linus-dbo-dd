<?php

namespace App\Http\Requests\MiscData;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'min:3', 'max:64'],
        ];
    }
}
