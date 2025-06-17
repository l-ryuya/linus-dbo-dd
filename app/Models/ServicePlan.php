<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServicePlan extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'service_plan_id';

    protected function casts(): array
    {
        return [
            'service_start_date' => 'date',
            'service_end_date' => 'date',
        ];
    }
}
