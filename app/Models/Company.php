<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string|null $service_code
 * @property string|null $service_plan_code
 * @property string|null $service_contract_code
 */
class Company extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'company_id';

    /**
     * @return HasMany<ServiceContract, $this>
     */
    public function serviceContracts(): HasMany
    {
        return $this->hasMany(ServiceContract::class, 'company_id', 'company_id');
    }

    /**
     * @return HasOne<DueDiligence, $this>
     */
    public function latestDd(): HasOne
    {
        return $this->hasOne(DueDiligence::class, 'dd_id', 'latest_dd_id');
    }

    /**
     * C-000001 形式の新しい法人IDを生成
     *
     * @return string
     */
    public static function generateNewCompanyId(): string
    {
        $lastCompany = self::select('company_code')
            ->orderBy('company_code', 'desc')
            ->withTrashed()
            ->first();
        if (empty($lastCompany)) {
            $lastNumber = 0;
        } else {
            $lastNumber = (int) substr($lastCompany->company_code, 2); // "C-" を除く
        }

        $newNumber = $lastNumber + 1;

        return 'C-' . str_pad((string) $newNumber, 6, '0', STR_PAD_LEFT);
    }
}
