<?php

declare(strict_types=1);

namespace App\UseCases\Tenant\ServiceContract;

use App\Enums\ServiceContractStatus;
use App\Exceptions\LogicValidationException;
use App\Jobs\DboBilling\CustomerJob;
use App\Mail\ContractStatusNotificationsMail;
use App\Models\ServiceContract;
use App\Services\CloudSign\GetDocumentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CloudsignStatusSyncAction
{
    /**
     * 顧客サービス契約ステータス同期
     *
     * @param string   $languageCode
     * @param int|null $tenantId
     * @param string   $publicId
     *
     * @return object
     *
     * @throws LogicValidationException
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Throwable
     */
    public function __invoke(
        string $languageCode,
        ?int $tenantId,
        string $publicId,
    ): object {
        DB::beginTransaction();

        try {
            $serviceContract = ServiceContract::where('public_id', $publicId)
                ->when($tenantId, function ($query) use ($tenantId) {
                    $query->where('service_contracts.tenant_id', $tenantId);
                })
                ->lockForUpdate()
                ->firstOrFail();
            throw_unless(
                ServiceContractStatus::ContractDocumentSent->isEqualValue($serviceContract->contract_status_code),
                new LogicValidationException(
                    errors: ['contractStatusCode' => [__('logic.contract_status_not_sent')]],
                ),
            );

            throw_if(
                is_null($serviceContract->contract_doc_id),
                new LogicValidationException(
                    errors: ['contractDocId' => [__('logic.contract_doc_id_not_set')]],
                ),
            );

            $getDocumentService = app(GetDocumentService::class, ['documentId' => $serviceContract->contract_doc_id]);

            [$contractStatus, $status] = $this->updateContractStatus($serviceContract, $getDocumentService->getStatus());

            $serviceContract->save();

            DB::commit();

            if ($status == 2) {
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

            if (in_array($status, [2, 3], true)) {
                $this->notifyRecipients($serviceContract, $contractStatus);
            }

            return (object) [
                'contractStatus' => $contractStatus,
            ];
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * @param ServiceContract $serviceContract
     * @param int             $status
     *
     * @return array{0: string, 1: int}
     */
    private function updateContractStatus(
        ServiceContract $serviceContract,
        int $status,
    ): array {
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

        return [$contractStatus, $status];
    }

    private function notifyRecipients(
        ServiceContract $serviceContract,
        string $contractStatus,
    ): void {
        // サービス・DBOグループへ通知
        $recipients = [
            $serviceContract->service->service_dept_group_email,
            $serviceContract->service->backoffice_group_email,
        ];

        $message = '<' . config('services.cloudsign.host') . '/documents/' . $serviceContract->contract_doc_id . '/summary>';

        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(new ContractStatusNotificationsMail(
                serviceContract: $serviceContract,
                contractStatus: $contractStatus,
                contractMessage: $message,
            ));
        }
    }
}
