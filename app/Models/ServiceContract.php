<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \Illuminate\Support\Carbon|string $contract_date
 * @property \Illuminate\Support\Carbon|string $contract_start_date
 * @property \Illuminate\Support\Carbon|string $contract_end_date
 * @property string|null $invoice_remind_days
 */
class ServiceContract extends Model
{
    use SoftDeletes;

    /** @use HasFactory<\Database\Factories\ServiceContractFactory> */
    use HasFactory;

    protected $primaryKey = 'service_contract_id';

    protected $fillable = [
        'public_id',
        'tenant_id',
        'customer_id',
        'service_id',
        'service_plan_id',
        'contract_name',
        'contract_language',
        'contract_status_type',
        'contract_status_code',
        'service_usage_status_type',
        'service_usage_status_code',
        'contract_date',
        'contract_start_date',
        'contract_end_date',
        'contract_auto_update',
        'customer_contact_user_name',
        'customer_contact_user_dept',
        'customer_contact_user_title',
        'customer_contact_user_email',
        'customer_contract_user_name',
        'customer_contract_user_dept',
        'customer_contract_user_title',
        'customer_contract_user_email',
        'customer_payment_user_name',
        'customer_payment_user_dept',
        'customer_payment_user_title',
        'customer_payment_user_email',
        'service_rep_user_option_id',
        'service_mgr_user_option_id',
        'billing_cycle_type',
        'billing_cycle_code',
        'invoice_remind_days',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'contract_date' => 'date:Y-m-d',
            'contract_start_date' => 'date:Y-m-d',
            'contract_end_date' => 'date:Y-m-d',
            'invoice_remind_days' => 'string',
        ];
    }
}
