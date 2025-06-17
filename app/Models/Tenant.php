<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'tenant_id';

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
        ];
    }
}
