<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Companies;

use App\Http\Requests\Traits\PaginationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class IndexRequest extends FormRequest
{
    use PaginationRules;

    public ?Carbon $serviceSignupStartDate = null;

    public ?Carbon $serviceSignupEndDate = null;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'companyName' => ['nullable', 'string', 'max:255'],
            'companyStatusCode' => [
                'nullable',
                'string',
                'max:64',
                Rule::exists('selection_items', 'selection_item_code')
                    ->where('selection_item_type', 'company_status'),
            ],
            'serviceSignupStartDate' => ['nullable', 'date', 'before_or_equal:serviceSignupEndDate'],
            'serviceSignupEndDate'   => ['nullable', 'date', 'after_or_equal:serviceSignupStartDate'],
        ]);
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->paginationDefaults());
    }

    protected function passedValidation(): void
    {
        $this->serviceSignupStartDate = convertToCarbon($this->validated('serviceSignupStartDate'));
        $this->serviceSignupEndDate   = convertToCarbon($this->validated('serviceSignupEndDate'));
    }
}
