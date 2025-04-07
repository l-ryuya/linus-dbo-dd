<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceContract extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'service_contract_id';

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function serviceCode(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_code');
    }

    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function contractManagerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contract_manager_user_id');
    }

    protected function casts(): array
    {
        return [
            'service_application_date' => 'date',
            'service_start_date' => 'date',
            'service_end_date' => 'date',
        ];
    }

    /**
     * SC-000001 形式の新しいサービス契約IDを生成
     *
     * @return string
     */
    public static function generateNewServiceContractId(): string
    {
        $lastContract = self::select('service_contract_code')
            ->orderBy('service_contract_code', 'desc')
            ->withTrashed()
            ->first();
        if (empty($lastContract)) {
            $lastNumber = 0;
        } else {
            $lastNumber = (int) substr($lastContract->service_contract_code, 3); // "SC-" を除く
        }

        $newNumber = $lastNumber + 1;

        return 'SC-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
