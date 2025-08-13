<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class DdStep extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'dd_step_id';

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
     * @return HasOne<UserOption, $this>
     */
    public function stepUserOption(): HasOne
    {
        return $this->hasOne(
            UserOption::class,
            'step_user_option_id',
            'user_option_id',
        );
    }

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
            'step_completed_at' => 'timestamp',
            'is_updated' => 'boolean',
            'rerun_required' => 'boolean',
            'dd_evidence_blob' => 'array',
            'dd_relations_snapshot' => 'array',
        ];
    }
}
