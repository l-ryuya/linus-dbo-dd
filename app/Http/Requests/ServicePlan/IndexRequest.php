<?php

namespace App\Http\Requests\ServicePlan;

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
            'service_code' => ['required', 'string', 'min:8', 'max:16'],
        ];
    }
}
