<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SelectionItem extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'selection_item_id';
}
