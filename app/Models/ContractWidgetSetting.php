<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string|null $widget_source_value
 */
class ContractWidgetSetting extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
        ];
    }
}
