<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyNameTranslation extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'company_name_translation_id';

    protected $fillable = [
        'company_id',
        'language_code',
        'company_legal_name',
    ];

    /**
     * @return BelongsTo<\App\Models\Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
