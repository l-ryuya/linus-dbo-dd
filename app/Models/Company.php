<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'company_id';

    protected $fillable = [
        'public_id',
        'tenant_id',
        'company_name_en',
        'country_code_alpha3',
        'website_url',
        'shareholders_url',
        'executives_url',
        'postal',
        'state',
        'city',
        'street',
        'building',
        'remarks',
    ];
}
