<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class DdCompany extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'dd_company_id';

    /**
     * @return HasOne<CountryRegion, $this>
     */
    public function locationCodeAlpha3(): HasOne
    {
        return $this->hasOne(CountryRegion::class, 'country_code_alpha3', 'location_code_alpha3');
    }

    protected function casts(): array
    {
        return [
            'public_id' => 'string',
            'is_listed' => 'boolean',
        ];
    }
}
