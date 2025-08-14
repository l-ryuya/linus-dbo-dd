<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $company_name
 * @property string $shareholder_name
 * @property string $full_name
 * @property string|null $position
 */
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

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
            'is_confirmed' => 'boolean',
        ];
    }
}
