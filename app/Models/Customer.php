<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'public_id',
        'tenant_id',
        'company_id',
        'sys_organization_code',
        'customer_status_type',
        'customer_status_code',
    ];

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
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
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    /**
     * @return HasMany<ServiceContract, $this>
     */
    public function serviceContracts(): HasMany
    {
        return $this->hasMany(ServiceContract::class, 'customer_id');
    }
}
