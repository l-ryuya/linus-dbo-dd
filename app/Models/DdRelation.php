<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class DdRelation extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'dd_relation_id';

    protected $fillable = [
        'tenant_id',
        'dd_case_id',
        'dd_entity_id',
        'dd_relation_type',
        'dd_relation_code',
        'dd_relation_status',
        'public_id',
        'is_confirmed',
    ];

    /**
     * @return HasOne<Tenant, $this>
     */
    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class);
    }

    /**
     * @return HasOne<DdCase, $this>
     */
    public function ddCase(): HasOne
    {
        return $this->hasOne(DdCase::class);
    }

    /**
     * @return HasOne<DdEntity, $this>
     */
    public function ddEntity(): HasOne
    {
        return $this->hasOne(DdEntity::class);
    }

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
            'is_confirmed' => 'boolean',
        ];
    }
}
