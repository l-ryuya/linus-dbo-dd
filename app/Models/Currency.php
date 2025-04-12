<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $primaryKey = 'currency_code_alpha';
    protected $keyType = 'string';
}
