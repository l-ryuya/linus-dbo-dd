<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServicePlan extends Model
{
    use SoftDeletes;

    // 複合主キーをサポートしていない為、無効化させる
    protected $primaryKey = 'Not supported Composite Primary Key';

    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'service_start_date' => 'date',
            'service_end_date' => 'date',
        ];
    }
}
