<?php

declare(strict_types=1);

namespace App\UseCases\Webhooks;

use App\Enums\CloudSignStatus;
use App\Enums\ServiceContractStatus;
use App\Enums\ServiceUsageStatus;
use App\Jobs\DboBilling\CustomerJob;
use App\Mail\ContractStatusNotificationsMail;
use App\Models\ServiceContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CloudSignWebhookAction
{
    /**
     * CloudSign Webhookからの通知を処理する
     *
     * @param array<string, mixed> $payload
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function __invoke(
        array $payload,
    ): void {
        $this->logPayload($payload);
        $this->validatePayload($payload);

        DB::beginTransaction();

        try {
            $serviceContract = ServiceContract::where('contract_doc_id', $payload['documentID'])
                ->where('contract_status_code', ServiceContractStatus::ContractDocumentSent->value)
                ->lockForUpdate()
                ->firstOrFail();

            $status = CloudSignStatus::tryFrom($payload['status']);
            if ($status === null) {
                throw new \InvalidArgumentException("Unknown CloudSign status: {$payload['status']}");
            }

            $this->updateContractStatus($serviceContract, $status);
            $serviceContract->save();

            DB::commit();

            $this->handlePostProcessing($serviceContract, $status, $payload);

        } catch (\Throwable $exception) {
            DB::rollBack();

            \Log::error('Error processing CloudSign webhook: ' . $exception->getMessage(), [
                'payload' => $payload,
                'exception' => $exception,
            ]);

            throw $exception;
        }
    }

    /**
     * ログにペイロードを出力する
     *
     * @param array<string, mixed> $payload
     *
     * @return void
     */
    private function logPayload(array $payload): void
    {
        $prettyJson = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        \Log::debug($prettyJson);
    }

    /**
     * ペイロードの検証
     *
     * @param array<string, mixed> $payload
     *
     * @throws \InvalidArgumentException
     */
    private function validatePayload(array $payload): void
    {
        if (!isset($payload['documentID'], $payload['status'])) {
            throw new \InvalidArgumentException('CloudSign webhook request does not contain documentID or status.');
        }
    }

    /**
     * サービス契約のステータスを更新する
     *
     * @param ServiceContract $serviceContract
     * @param CloudSignStatus $status
     *
     * @return void
     */
    private function updateContractStatus(ServiceContract $serviceContract, CloudSignStatus $status): void
    {
        match ($status) {
            CloudSignStatus::Executed => $this->handleExecutedContract($serviceContract),
            CloudSignStatus::Cancelled => $this->handleCancelledContract($serviceContract),
            default => null,
        };
    }

    private function handleExecutedContract(ServiceContract $serviceContract): void
    {
        $serviceContract->contract_status_code = ServiceContractStatus::ContractExecuted->value;
        $serviceContract->service_usage_status_code = ServiceUsageStatus::Active->value;
        $serviceContract->contract_executed_at = now();
    }

    private function handleCancelledContract(ServiceContract $serviceContract): void
    {
        $serviceContract->contract_status_code = ServiceContractStatus::ContractCancelled->value;
    }

    /**
     * クラウドサインのステータスに応じた後処理を行う
     *
     * @param ServiceContract $serviceContract
     * @param CloudSignStatus $status
     * @param array<string, mixed> $payload
     *
     * @return void
     */
    private function handlePostProcessing(ServiceContract $serviceContract, CloudSignStatus $status, array $payload): void
    {
        if ($status === CloudSignStatus::Executed) {
            $this->dispatchCustomerJob($serviceContract);
        }

        if (in_array($status, [CloudSignStatus::Executed, CloudSignStatus::Cancelled], true)) {
            $this->notifyRecipients($serviceContract, $status->getStatusText(), $payload);
        }
    }

    /**
     * dbo_billing顧客登録のジョブをディスパッチする
     *
     * @param ServiceContract $serviceContract
     *
     * @return void
     */
    private function dispatchCustomerJob(ServiceContract $serviceContract): void
    {
        $invoiceRemindDays = $this->parseInvoiceRemindDays($serviceContract->invoice_remind_days);

        CustomerJob::dispatch(
            $serviceContract->customer->company->company_name_en,
            $serviceContract->customer_payment_user_email,
            $serviceContract->contract_language,
            $serviceContract->service_contract_code,
            $serviceContract->service->billing_service_id,
            $serviceContract->public_id,
            $invoiceRemindDays,
        );
    }

    /**
     * 請求リマインド日数をパースする
     *
     * @param string|null $invoiceRemindDays
     *
     * @return array<int>
     */
    private function parseInvoiceRemindDays(?string $invoiceRemindDays): array
    {
        if (is_null($invoiceRemindDays)) {
            return [];
        }

        return array_map('intval', explode(',', $invoiceRemindDays));
    }

    /**
     * 通知を送信する
     *
     * @param ServiceContract      $serviceContract
     * @param string               $contractStatus
     * @param array<string, mixed> $payload
     *
     * @return void
     */
    private function notifyRecipients(
        ServiceContract $serviceContract,
        string $contractStatus,
        array $payload,
    ): void {
        // サービス・DBOグループへ通知
        $recipients = [
            $serviceContract->service->service_dept_group_email,
            $serviceContract->service->backoffice_group_email,
        ];

        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(new ContractStatusNotificationsMail(
                serviceContract: $serviceContract,
                contractStatus: $contractStatus,
                contractMessage: $payload['text'] ?? '',
            ));
        }
    }
}
