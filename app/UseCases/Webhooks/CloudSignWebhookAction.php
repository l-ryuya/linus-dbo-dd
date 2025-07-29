<?php

declare(strict_types=1);

namespace App\UseCases\Webhooks;

use App\Enums\ServiceContractStatus;
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
        $prettyJson = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        \Log::debug($prettyJson);

        if (!isset($payload['documentID'], $payload['status'])) {
            throw new \InvalidArgumentException('CloudSign webhook request does not contain documentID or status.');
        }

        DB::beginTransaction();

        try {
            $serviceContract = ServiceContract::where('contract_doc_id', $payload['documentID'])
                ->where('contract_status_code', ServiceContractStatus::ContractDocumentSent->value)
                ->lockForUpdate()
                ->firstOrFail();

            $contractStatus = $this->updateContractStatus($serviceContract, $payload['status']);

            $serviceContract->save();

            DB::commit();

            if ($payload['status'] == 2) {
                $invoiceRemindDays = [];
                if (!is_null($serviceContract->invoice_remind_days)) {
                    $invoiceRemindDays = array_map('intval', explode(',', $serviceContract->invoice_remind_days));
                }

                CustomerJob::dispatch(
                    $serviceContract->customer_payment_user_name,
                    $serviceContract->customer_payment_user_email,
                    $serviceContract->contract_language,
                    $serviceContract->service_contract_code,
                    $serviceContract->service->billing_service_id,
                    $serviceContract->public_id,
                    $invoiceRemindDays,
                );
            }

            if (in_array($payload['status'], [2, 3], true)) {
                $this->notifyRecipients($serviceContract, $contractStatus, $payload);
            }
        } catch (\Throwable $exception) {
            DB::rollBack();

            \Log::error('Error processing CloudSign webhook: ' . $exception->getMessage(), [
                'payload' => $payload,
                'exception' => $exception,
            ]);

            throw $exception;
        }
    }

    private function updateContractStatus(
        ServiceContract $serviceContract,
        int $status,
    ): string {
        switch ($status) {
            case 1:
                $contractStatus = '先方確認中';
                break;
            case 2:
                $contractStatus = '締結完了';
                $serviceContract->contract_status_code = ServiceContractStatus::ContractExecuted->value;
                $serviceContract->contract_executed_at = now();
                break;
            case 3:
                $contractStatus = '取り消し・却下';
                $serviceContract->contract_status_code = ServiceContractStatus::ContractCancelled->value;
                break;
            default:
                $contractStatus = '不明';
        }

        return $contractStatus;
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
