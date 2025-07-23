<?php

declare(strict_types=1);

namespace App\Jobs\CloudSign;

use App\Models\ServiceContract;
use App\Services\CloudSign\ContractService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContractJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * タイムアウトになる前にジョブを実行できる秒数
     *
     * @var int
     */
    public int $timeout = 180;

    /**
     * ジョブを試行する回数を決定
     *
     * @var int
     */
    public int $tries = 2;

    /**
     * ジョブを再試行する前に待機する秒数
     *
     * @var int
     */
    public int $backoff = 600;

    /**
     * ジョブの一意のロックが解放されるまでの秒数
     *
     * @var int
     */
    public int $uniqueFor = 200;

    /**
     * モデルが存在しなくなった場合は、ジョブを削除
     *
     * @var bool
     */
    public bool $deleteWhenMissingModels = true;

    public int $serviceContractId;
    public ?ServiceContract $serviceContract = null;

    public function __construct(int $serviceContractId)
    {
        $this->onConnection('database');
        $this->onQueue('cloudsign');

        $this->serviceContractId = $serviceContractId;
    }

    /**
     * ジョブの実行
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function handle(): void
    {
        $this->serviceContract = ServiceContract::findOrFail($this->serviceContractId);
        $contractService = new ContractService();
        $contractService->sendContract($this->serviceContract->service_contract_id);
    }

    /**
     * 失敗したジョブを処理
     */
    public function failed(?Throwable $exception): void
    {
        // 例外をログ出力
        \Log::error('ContractJob failed', [
            'service_contract_id' => $this->serviceContractId,
            'exception' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString(),
        ]);

        // 管理者にメール送信
        Mail::raw(
            "ContractJob failed for ServiceContract ID: {$this->serviceContractId}\n\nException: {$exception?->getMessage()}",
            function ($message) {
                $message->to(config('mail.system.admin_address'))
                    ->subject('【緊急】ContractJobでエラーが発生しました');
            },
        );
    }

    /**
     * ジョブの一意なIDの取得
     */
    public function uniqueId(): string
    {
        return (string) $this->serviceContractId;
    }
}
