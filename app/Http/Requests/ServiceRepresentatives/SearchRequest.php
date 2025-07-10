<?php

declare(strict_types=1);

namespace App\Http\Requests\ServiceRepresentatives;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'userName' => ['nullable', 'string', 'min:2', 'max:64'],
        ];
    }
}
