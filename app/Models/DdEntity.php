<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class DdEntity extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'dd_entity_id';

    /**
     * @return HasOne<Tenant, $this>
     */
    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class);
    }

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
        ];
    }
}
