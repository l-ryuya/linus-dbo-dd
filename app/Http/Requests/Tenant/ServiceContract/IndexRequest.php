<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant\ServiceContract;

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
            'tenantName' => ['nullable', 'string', 'max:255'],
            'servicePublicId' => ['nullable', 'uuid'],
            'servicePlanPublicId' => ['nullable', 'uuid'],
            'customerName' => ['nullable', 'string', 'max:255'],
            'contractName' => ['nullable', 'string', 'max:255'],
            'contractStatusCode' => [
                'nullable',
                'string',
                'max:64',
                Rule::exists('selection_items', 'selection_item_code')
                    ->where('selection_item_type', 'service_contract_status'),
            ],
            'serviceUsageStatusCode' => [
                'nullable',
                'string',
                'max:64',
                Rule::exists('selection_items', 'selection_item_code')
                    ->where('selection_item_type', 'service_usage_status'),
            ],
            'contractDate' => ['nullable', 'date', 'date_format:Y-m-d'],
            'contractStartDate' => ['nullable', 'date', 'date_format:Y-m-d'],
        ]);
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->paginationDefaults());
    }
}
