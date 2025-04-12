<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read \App\Models\User $serviceAdminUser
 */
class Service extends Model
{
    use SoftDeletes;

    /**
     * @return BelongsTo<User, $this>
     */
    public function serviceAdminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'service_admin_user_id');
    }

    protected function casts(): array
    {
        return [
            'service_start_date' => 'date',
            'service_end_date' => 'date',
        ];
    }
}
