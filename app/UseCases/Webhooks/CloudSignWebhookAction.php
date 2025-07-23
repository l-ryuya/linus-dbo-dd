<?php

declare(strict_types=1);

namespace App\UseCases\Webhooks;

use App\Enums\ServiceContractStatus;
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
                ->lockForUpdate()
                ->first();
            if (!$serviceContract) {
                \Log::warning('ServiceContract not found for documentID: ' . $payload['documentID']);
                return;
            }

            $contractStatus = '';
            switch ($payload['status']) {
                case 1:
                    // 先方確認中
                    break;
                case 2:
                    // 締結完了
                    $contractStatus = '締結完了';
                    $serviceContract->contract_status_code = ServiceContractStatus::ContractExecuted->value;
                    $serviceContract->contract_executed_at = now();
                    break;
                case 3:
                    // 取り消し・却下
                    $contractStatus = '取り消し・却下';
                    $serviceContract->contract_status_code = ServiceContractStatus::ContractCancelled->value;
                    break;
                default:
                    \Log::warning('Unknown status received: ' . $payload['status']);
                    return;
            }

            $serviceContract->save();

            DB::commit();

            // サービス・DBOグループへ通知
            $recipients[] = $serviceContract->service->service_dept_group_email;
            $recipients[] = $serviceContract->service->backoffice_group_email;

            foreach ($recipients as $recipient) {
                Mail::to($recipient)->send(new ContractStatusNotificationsMail(
                    serviceContract: $serviceContract,
                    contractStatus: $contractStatus,
                    contractMessage: $payload['text'] ?? '',
                ));
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
}
