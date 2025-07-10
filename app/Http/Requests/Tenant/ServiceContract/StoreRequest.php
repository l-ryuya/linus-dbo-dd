<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant\ServiceContract;

use App\Dto\Tenant\ServiceContract\StoreInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'servicePublicId' => ['required', 'uuid', Rule::exists('services', 'public_id')],
            'servicePlanPublicId' => ['required', 'uuid', Rule::exists('service_plans', 'public_id')],
            'customerPublicId' => ['required', 'uuid'],
            'contractName' => ['required', 'string', 'max:255'],
            'contractLanguage' => ['required', 'string', 'size:3'],
            'contractStatusCode' => [
                'required',
                'string',
                'min:3',
                'max:128',
                Rule::exists('selection_items', 'selection_item_code')
                    ->where('selection_item_type', 'service_contract_status'),
            ],
            'serviceUsageStatusCode' => [
                'required',
                'string',
                'min:3',
                'max:128',
                Rule::exists('selection_items', 'selection_item_code')
                    ->where('selection_item_type', 'service_usage_status'),
            ],
            'contractDate' => ['required', 'date', 'date_format:Y-m-d'],
            'contractStartDate' => ['required', 'date', 'date_format:Y-m-d'],
            'contractEndDate' => ['nullable', 'date', 'date_format:Y-m-d'],
            'contractAutoUpdate' => ['required', 'boolean'],
            'customerContactUserName' => ['required', 'string', 'max:255'],
            'customerContactUserDept' => ['nullable', 'string', 'max:255'],
            'customerContactUserTitle' => ['nullable', 'string', 'max:255'],
            'customerContactUserMail' => ['required', 'email', 'max:255'],
            'customerContractUserName' => ['required', 'string', 'max:255'],
            'customerContractUserDept' => ['nullable', 'string', 'max:255'],
            'customerContractUserTitle' => ['nullable', 'string', 'max:255'],
            'customerContractUserMail' => ['required', 'email', 'max:255'],
            'customerPaymentUserName' => ['required', 'string', 'max:255'],
            'customerPaymentUserDept' => ['nullable', 'string', 'max:255'],
            'customerPaymentUserTitle' => ['nullable', 'string', 'max:255'],
            'customerPaymentUserMail' => ['required', 'email', 'max:255'],
            'serviceRepUserOptionPublicId' => ['required', 'uuid'],
            'serviceMgrUserOptionPublicId' => ['required', 'uuid'],
            'invoiceRemindDays' => ['nullable', 'string', 'max:255', 'regex:/^-?\d+(,-?\d+)*$/'],
            'billingCycleCode' => [
                'nullable',
                'string',
                'min:3',
                'max:128',
                Rule::exists('selection_items', 'selection_item_code')
                    ->where('selection_item_type', 'billing_cycle'),
            ],
            'remarks' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toStoreInput(): StoreInput
    {
        return StoreInput::fromRequest($this->validated());
    }
}
