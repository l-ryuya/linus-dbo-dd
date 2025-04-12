<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SelectionItemTranslation extends Model
{
    use SoftDeletes;

    // 複合主キーをサポートしていない為、無効化させる
    protected $primaryKey = 'Not supported Composite Primary Key';

    public $incrementing = false;
}
