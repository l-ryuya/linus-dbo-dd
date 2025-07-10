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

    protected function casts(): array
    {
        return [
            'contract_date' => 'date',
            'contract_start_date' => 'date',
            'contract_end_date' => 'date',
            'invoice_remind_days' => 'string',
        ];
    }
}
