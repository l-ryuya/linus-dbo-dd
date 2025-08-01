<?php

declare(strict_types=1);

namespace App\UseCases\Tenant\ServiceContract;

use App\Enums\CloudSignStatus;
use App\Enums\ServiceContractStatus;
use App\Enums\ServiceUsageStatus;
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

            $status = CloudSignStatus::tryFrom($getDocumentService->getStatus());
            if ($status === null) {
                DB::commit();
                return (object) [
                    'contractStatus' => __('cloudsign.status.unknown'),
                ];
            }

            $this->updateContractStatus($serviceContract, $status);
            $serviceContract->save();

            DB::commit();

            $this->handlePostProcessing($serviceContract, $status);

            return (object) [
                'contractStatus' => $status->getStatusText(),
            ];
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
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
     *
     * @return void
     */
    private function handlePostProcessing(ServiceContract $serviceContract, CloudSignStatus $status): void
    {
        if ($status === CloudSignStatus::Executed) {
            $this->dispatchCustomerJob($serviceContract);
        }

        if (in_array($status, [CloudSignStatus::Executed, CloudSignStatus::Cancelled], true)) {
            $this->notifyRecipients($serviceContract, $status->getStatusText());
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
     *
     * @return void
     */
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
