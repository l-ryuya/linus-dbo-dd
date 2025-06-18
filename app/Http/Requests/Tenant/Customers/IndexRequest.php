<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant\Customers;

use App\Http\Requests\Traits\PaginationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'organizationCode' => ['nullable', 'string', 'max:12'],
            'customerName' => ['nullable', 'string', 'max:255'],
            'customerStatusCode' => [
                'nullable',
                'string',
                'max:64',
                Rule::exists('selection_items', 'selection_item_code')
                    ->where('selection_item_type', 'customer_status'),
            ],
            'servicePublicId' => ['nullable', 'uuid'],
            'servicePlanPublicId' => ['nullable', 'uuid'],
        ]);
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->paginationDefaults());
    }
}
