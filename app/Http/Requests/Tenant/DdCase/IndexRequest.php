<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant\DdCase;

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
            'tenantPublicId' => ['nullable', 'uuid'],
            'companyName' => ['nullable', 'string', 'max:255'],
            'ddCaseNo' => ['nullable', 'string', 'max:13'],
            'currentDdStepCode' => [
                'nullable',
                'string',
                'max:64',
                Rule::exists('selection_items', 'selection_item_code')
                    ->where('selection_item_type', 'dd_step'),
            ],
            'overallResult' => ['nullable', 'string', Rule::in(['OK', 'NG'])],
            'customerRiskLevel' => ['nullable', 'string', Rule::in(['HIGH', 'LOW'])],
            'startedAtFrom' => ['nullable', 'date', 'date_format:Y-m-d'],
            'startedAtTo' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:startedAtFrom'],
            'endedAtFrom' => ['nullable', 'date', 'date_format:Y-m-d'],
            'endedAtTo' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:endedAtFrom'],
        ]);
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->paginationDefaults());
    }
}
