<?php

namespace App\Http\Requests\ServiceSignup;

use App\Dto\ServiceSignup\StoreInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'companyName' => ['required', 'string', 'max:255', Rule::unique('companies', 'company_name_en')],
            'departmentName' => ['nullable', 'string', 'max:255'],

            // サービスコード プロトタイプでは固定値
            'serviceCode' => ['required', 'string', Rule::in(['SV-00003'])],
            'servicePlan' => [
                'required',
                'string',
                'size:9',
                Rule::exists('service_plans', 'service_plan_code')
                    ->where('service_code', $this->input('serviceCode')),
            ],
            'paymentCycle' => [
                'required',
                'string',
                'max:32',
                Rule::exists('selection_items', 'selection_item_code')
                    ->where('selection_item_type', 'payment_cycle'),
            ],
            'paymentMethod' => [
                'required',
                'string',
                'max:32',
                Rule::exists('selection_items', 'selection_item_code')
                    ->where('selection_item_type', 'payment_method'),
            ],
            'secondLanguage' => [
                'required',
                'string',
                'size:3',
                Rule::exists('selection_items', 'selection_item_code')
                    ->where('selection_item_type', 'language_code'),
            ],

            // 担当者情報
            'contactPersonLastName' => ['required', 'string', 'max:255'],
            'contactPersonFirstName' => ['required', 'string', 'max:255'],
            'contactPersonMiddleName' => ['nullable', 'string', 'max:255'],
            'contactPersonPosition' => ['nullable', 'string', 'max:255'],
            'contactPersonEmail' => ['required', 'string', 'email', 'max:255'],

            // 契約担当者情報
            'contractPersonLastName' => ['required', 'string', 'max:255'],
            'contractPersonFirstName' => ['required', 'string', 'max:255'],
            'contractPersonMiddleName' => ['nullable', 'string', 'max:255'],
            'contractPersonPosition' => ['nullable', 'string', 'max:255'],
            'contractPersonEmail' => ['required', 'string', 'email', 'max:255'],

            // （法人）所在地情報
            'country' => [
                'required',
                'string',
                'size:3',
                Rule::exists('country_regions', 'country_code_alpha3')
                    ->where('world_region_type', 'world_region'),
            ],
            'postalCode' => ['required', 'string', 'max:20'],
            'state' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'addressLine1' => ['required', 'string', 'max:255'],
            'addressLine2' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toStoreInput(): StoreInput
    {
        return StoreInput::fromRequest($this->validated());
    }
}
