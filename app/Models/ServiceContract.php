<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \Illuminate\Support\Carbon|string $service_application_date
 * @property \Illuminate\Support\Carbon|string $service_start_date
 * @property \Illuminate\Support\Carbon|string $service_end_date
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
            'service_application_date' => 'date',
            'service_start_date' => 'date',
            'service_end_date' => 'date',
        ];
    }
}
