<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $ended_at
 * @property \Illuminate\Support\Carbon|null $last_process_datetime
 */
class DdCase extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'dd_case_id';

    /**
     * @return HasOne<Tenant, $this>
     */
    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class);
    }

    /**
     * @return HasOne<Customer, $this>
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * @return HasOne<UserOption, $this>
     */
    public function caseUserOption(): HasOne
    {
        return $this->hasOne(UserOption::class, 'user_option_id', 'case_user_option_id');
    }

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
            'started_at' => 'timestamp',
            'ended_at' => 'timestamp',
            'last_process_datetime' => 'timestamp',
        ];
    }
}
