<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SelectionItemTranslation extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $primaryKey = ['selection_item_type', 'selection_item_code', 'language_code'];
    protected $keyType = 'string';
}
