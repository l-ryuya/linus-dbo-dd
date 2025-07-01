<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserOption extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'user_option_id';

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
            'platform_user' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'tenant_id');
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function isAdmin(): bool
    {
        return $this->platform_user;
    }

    public function isTenant(): bool
    {
        if (!empty($this->tenant_id) && empty($this->customer_id)) {
            return true;
        }

        return false;
    }

    public function isCustomer(): bool
    {
        if (!empty($this->tenant_id) && !empty($this->customer_id)) {
            return true;
        }

        return false;
    }
}
