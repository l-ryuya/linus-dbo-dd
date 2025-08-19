<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DdEntity extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'dd_entity_id';

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
        ];
    }
}
