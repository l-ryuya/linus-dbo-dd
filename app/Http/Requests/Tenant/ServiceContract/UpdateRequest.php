<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant\ServiceContract;

use App\Dto\Tenant\ServiceContract\UpdateInput;
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
            'servicePublicId' => ['required', 'uuid', Rule::exists('services', 'public_id')],
            'servicePlanPublicId' => ['required', 'uuid', Rule::exists('service_plans', 'public_id')],
            'customerPublicId' => ['required', 'uuid'],
            'contractName' => ['required', 'string', 'max:255'],
            'contractLanguage' => ['required', 'string', 'size:3'],
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
            'customerContactUserEmail' => ['required', 'email', 'max:255'],
            'customerContractUserName' => ['required', 'string', 'max:255'],
            'customerContractUserDept' => ['nullable', 'string', 'max:255'],
            'customerContractUserTitle' => ['nullable', 'string', 'max:255'],
            'customerContractUserEmail' => ['required', 'email', 'max:255'],
            'customerPaymentUserName' => ['required', 'string', 'max:255'],
            'customerPaymentUserDept' => ['nullable', 'string', 'max:255'],
            'customerPaymentUserTitle' => ['nullable', 'string', 'max:255'],
            'customerPaymentUserEmail' => ['required', 'email', 'max:255'],
            'serviceRepUserPublicId' => ['required', 'uuid', Rule::exists('user_options', 'public_id')],
            'serviceMgrUserPublicId' => ['required', 'uuid', Rule::exists('user_options', 'public_id')],
            'quotationName' => ['nullable', 'string', 'max:255'],
            'quotationNumber' => ['nullable','string','max:255'],
            'quotationDate' => ['nullable','date', 'date_format:Y-m-d'],
            'proposalName' => ['nullable', 'string', 'max:255'],
            'proposalNumber' => ['nullable','string','max:255'],
            'proposalDate' => ['nullable','date', 'date_format:Y-m-d'],
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

    public function toUpdateInput(): UpdateInput
    {
        return UpdateInput::fromRequest($this->validated());
    }
}
