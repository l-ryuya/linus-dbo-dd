<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string|null $company_code
 */
class DueDiligence extends Model
{
    use SoftDeletes;

    /**
     * @return BelongsTo<User, $this>
     */
    public function primaryDdUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'primary_dd_user_id', 'user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function finalDdUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'final_dd_user_id', 'user_id');
    }

    protected function casts(): array
    {
        return [
            'main_clients' => 'array',
            'main_banks' => 'array',
            'investment_sources' => 'array',
            'investment_targets' => 'array',
            'date_of_birth' => 'date',
            'dd_start_date' => 'date',
            'dd_end_date' => 'date',
            'next_dd_date' => 'date',
            'under_continuous_dd' => 'boolean',
            'ai_dd_completed_date' => 'date',
            'primary_dd_completed_date' => 'date',
            'final_dd_completed_date' => 'date',
        ];
    }
}
