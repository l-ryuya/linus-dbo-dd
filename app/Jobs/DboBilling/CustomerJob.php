<?php

declare(strict_types=1);

namespace App\Jobs\DboBilling;

use App\Services\DboBilling\CustomerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * DBO Billing Customer登録ジョブ
 */
class CustomerJob implements ShouldQueue, ShouldBeUnique
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
    public int $timeout = 30;

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
    public int $uniqueFor = 60;

    /**
     * モデルが存在しなくなった場合は、ジョブを削除
     *
     * @var bool
     */
    public bool $deleteWhenMissingModels = true;

    public string $name;

    public string $email;

    public string $language;

    public string $externalId;

    public string $serviceId;

    public string $serviceContractId;

    /** @var int[] */
    public array $invoiceRemindDays;

    /**
     * @param string $name service_contracts.customer_payment_user_name
     * @param string $email service_contracts.customer_payment_user_email
     * @param string $language 言語コード
     * @param string $externalId service_contracts.service_contract_code
     * @param string $serviceId services.billing_service_id
     * @param string $serviceContractId service_contracts.public_id
     * @param array<int> $invoiceRemindDays service_contracts.invoice_remind_days
     */
    public function __construct(
        string $name,
        string $email,
        string $language,
        string $externalId,
        string $serviceId,
        string $serviceContractId,
        array $invoiceRemindDays = [],
    ) {
        $this->onConnection('database');
        $this->onQueue('billing');

        $this->name = $name;
        $this->email = $email;
        $this->language = $language;
        $this->externalId = $externalId;
        $this->serviceId = $serviceId;
        $this->serviceContractId = $serviceContractId;
        $this->invoiceRemindDays = $invoiceRemindDays;
    }

    /**
     * ジョブの実行
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function handle(): void
    {
        $customerService = new CustomerService();
        $customerService->addCustomer(
            $this->name,
            $this->email,
            $this->language,
            $this->externalId,
            $this->serviceId,
            $this->serviceContractId,
            $this->invoiceRemindDays,
        );
    }

    /**
     * 失敗したジョブを処理
     */
    public function failed(?Throwable $exception): void
    {
        // 例外をログ出力
        \Log::error('Billing CustomerJob failed', [
            'service_contract_public_id' => $this->serviceContractId,
            'exception' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString(),
        ]);

        // 管理者にメール送信
        Mail::raw(
            "Billing CustomerJob failed for ServiceContractPublicID: {$this->serviceContractId}\n\nException: {$exception?->getMessage()}",
            function ($message) {
                $message->to(config('mail.system.admin_address'))
                    ->subject('【緊急】Billing CustomerJobでエラーが発生しました');
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
