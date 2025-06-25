<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant\Customer;

use App\Dto\Tenant\Customer\UpdateInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customerName' => ['required', 'string', 'max:255'],
            'customerNameEn' => ['required', 'string', 'max:255', "regex:/^[a-zA-Z0-9&'.,\- ]+$/"],
            'websiteUrl' => ['required', 'url', 'max:2048'],
            'shareholdersUrl' => ['required', 'url', 'max:2048'],
            'executivesUrl' => ['required', 'url', 'max:2048'],
            'customerStatusCode' => [
                'required',
                'string',
                'max:64',
                Rule::exists('selection_items', 'selection_item_code')
                    ->where('selection_item_type', 'customer_status'),
            ],
            'countryCodeAlpha3' => [
                'required',
                'string',
                'size:3',
                Rule::exists('country_regions', 'country_code_alpha3')
                    ->where('world_region_type', 'world_region'),
            ],
            'languageCode' => [
                'required',
                'string',
                'size:3',
                Rule::exists('selection_items', 'selection_item_code')
                    ->where('selection_item_type', 'language_code'),
            ],
            'postalCode' => ['nullable', 'string', 'max:20'],
            'state' => ['nullable', 'string', 'max:128'],
            'city' => ['nullable', 'string', 'max:128'],
            'street' => ['nullable', 'string', 'max:128'],
            'building' => ['nullable', 'string', 'max:128'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toUpdateInput(): UpdateInput
    {
        return UpdateInput::fromRequest($this->validated());
    }
}
