<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CountryRegion extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'country_code_alpha3';

    protected $keyType = 'string';

    public $incrementing = false;
}
