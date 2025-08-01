<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant\ServiceContract;

use App\Dto\Tenant\ServiceContract\StoreInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDraftRequest extends FormRequest
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
            'servicePlanPublicId' => ['nullable', 'uuid', Rule::exists('service_plans', 'public_id')],
            'customerPublicId' => ['required', 'uuid'],
            'contractName' => ['required', 'string', 'max:255'],
            'contractLanguage' => ['nullable', 'string', 'size:3'],
            'serviceUsageStatusCode' => [
                'required',
                'string',
                'min:3',
                'max:128',
                Rule::in(['awaiting_activation']),
                Rule::exists('selection_items', 'selection_item_code')
                    ->where('selection_item_type', 'service_usage_status'),
            ],
            'contractDate' => ['nullable', 'date', 'date_format:Y-m-d'],
            'contractStartDate' => ['nullable', 'date', 'date_format:Y-m-d'],
            'contractEndDate' => ['nullable', 'date', 'date_format:Y-m-d'],
            'contractAutoUpdate' => ['nullable', 'boolean'],
            'customerContactUserName' => ['nullable', 'string', 'max:255'],
            'customerContactUserDept' => ['nullable', 'string', 'max:255'],
            'customerContactUserTitle' => ['nullable', 'string', 'max:255'],
            'customerContactUserEmail' => ['nullable', 'email', 'max:255'],
            'customerContractUserName' => ['nullable', 'string', 'max:255'],
            'customerContractUserDept' => ['nullable', 'string', 'max:255'],
            'customerContractUserTitle' => ['nullable', 'string', 'max:255'],
            'customerContractUserEmail' => ['nullable', 'email', 'max:255'],
            'customerPaymentUserName' => ['nullable', 'string', 'max:255'],
            'customerPaymentUserDept' => ['nullable', 'string', 'max:255'],
            'customerPaymentUserTitle' => ['nullable', 'string', 'max:255'],
            'customerPaymentUserEmail' => ['nullable', 'email', 'max:255'],
            'serviceRepUserPublicId' => ['nullable', 'uuid'],
            'serviceMgrUserPublicId' => ['nullable', 'uuid'],
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

    public function toStoreInput(): StoreInput
    {
        return StoreInput::fromRequest($this->validated());
    }
}
