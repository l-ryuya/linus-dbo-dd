<?php

declare(strict_types=1);

namespace App\Http\Requests\ServicePlan;

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
            'servicePublicId' => ['required', 'uuid'],
        ];
    }
}
