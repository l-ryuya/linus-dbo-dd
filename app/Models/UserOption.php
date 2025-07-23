<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    public function isAdmin(): bool
    {
        if ($this->platform_user && !empty($this->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * @return HasOne<Tenant, $this>
     */
    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class, 'tenant_id', 'tenant_id');
    }

    /**
     * @return HasOne<Customer, $this>
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * @return HasOne<Service, $this>
     */
    public function service(): HasOne
    {
        return $this->hasOne(Service::class, 'service_id', 'service_id');
    }

    public function isTenant(): bool
    {
        if (!$this->platform_user && !empty($this->tenant_id) && empty($this->customer_id)) {
            return true;
        }

        return false;
    }

    public function isCustomer(): bool
    {
        if (!$this->platform_user && !empty($this->tenant_id) && !empty($this->customer_id)) {
            return true;
        }

        return false;
    }
}
