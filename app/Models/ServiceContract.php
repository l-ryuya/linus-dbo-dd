<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \Illuminate\Support\Carbon|string $contract_date
 * @property \Illuminate\Support\Carbon|string $contract_start_date
 * @property \Illuminate\Support\Carbon|string $contract_end_date
 * @property string|null $invoice_remind_days
 */
class ServiceContract extends Model
{
    use SoftDeletes;

    /** @use HasFactory<\Database\Factories\ServiceContractFactory> */
    use HasFactory;

    protected $primaryKey = 'service_contract_id';

    protected function casts(): array
    {
        return [
            'contract_date' => 'date',
            'contract_start_date' => 'date',
            'contract_end_date' => 'date',
            'invoice_remind_days' => 'string',
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

        return 'SC-' . str_pad((string) $newNumber, 6, '0', STR_PAD_LEFT);
    }
}
