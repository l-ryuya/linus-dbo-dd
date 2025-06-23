<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyNameTranslation extends Model
{
    use SoftDeletes;

    // 複合主キーをサポートしていない為、無効化させる
    protected $primaryKey = 'Not supported Composite Primary Key';

    public $incrementing = false;

    /**
     * @return BelongsTo<\App\Models\Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
