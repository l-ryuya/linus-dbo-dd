<?php

declare(strict_types=1);

namespace App\Services\ServiceContract;

use App\Enums\CloudSignStatus;
use App\Enums\Service\ServiceContractStatusCode;
use App\Enums\Service\ServiceUsageStatusCode;
use App\Jobs\DboBilling\CustomerJob;
use App\Mail\ContractStatusNotificationsMail;
use App\Models\ServiceContract;
use Illuminate\Support\Facades\Mail;

/**
 * サービス契約のステータスを管理するサービス
 *
 * クラウドサインの契約ステータスに応じて、サービス契約の契約ステータス・サービス利用ステータス・契約締結日時を更新し、
 * dbo_billingへ顧客登録を行う。
 */
class ContractStatusService
{
    /**
     * サービス契約のステータスを更新する
     *
     * @param ServiceContract $serviceContract
     * @param CloudSignStatus $status
     *
     * @return void
     */
    public function updateContractStatus(
        ServiceContract $serviceContract,
        CloudSignStatus $status,
    ): void {
        match ($status) {
            CloudSignStatus::Executed => $this->handleExecutedContract($serviceContract),
            CloudSignStatus::Cancelled => $this->handleCancelledContract($serviceContract),
            default => null,
        };
    }

    /**
     * クラウドサインのステータスに応じた後処理を行う
     *
     * @param ServiceContract $serviceContract
     * @param CloudSignStatus $status
     * @param string|null     $message
     *
     * @return void
     */
    public function handlePostProcessing(
        ServiceContract $serviceContract,
        CloudSignStatus $status,
        ?string $message = null,
    ): void {
        if ($status === CloudSignStatus::Executed) {
            $this->dispatchCustomerJob($serviceContract);
        }

        if (in_array(
            $status,
            [CloudSignStatus::Executed, CloudSignStatus::Cancelled],
            true,
        )) {
            $this->notifyRecipients(
                $serviceContract,
                $status->getStatusText(),
                $message,
            );
        }
    }

    private function handleExecutedContract(
        ServiceContract $serviceContract,
    ): void {
        $serviceContract->contract_status_code = ServiceContractStatusCode::ContractExecuted->value;
        $serviceContract->service_usage_status_code = ServiceUsageStatusCode::Active->value;
        $serviceContract->contract_executed_at = now();

        $customer = $serviceContract->customer;
        if ($customer && $customer->first_service_start_date === null) {
            $customer->first_service_start_date = now()->format('Y-m-d');
            $customer->save();
        }
    }

    private function handleCancelledContract(
        ServiceContract $serviceContract,
    ): void {
        $serviceContract->contract_status_code = ServiceContractStatusCode::ContractCancelled->value;
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
        $invoiceRemindDays = $this->parseInvoiceRemindDays(
            $serviceContract->invoice_remind_days,
        );

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
     * @param ServiceContract $serviceContract
     * @param string          $contractStatus
     * @param string|null     $message
     *
     * @return void
     */
    private function notifyRecipients(
        ServiceContract $serviceContract,
        string $contractStatus,
        ?string $message,
    ): void {
        $recipients = [
            $serviceContract->service->service_dept_group_email,
            $serviceContract->service->backoffice_group_email,
        ];

        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(
                new ContractStatusNotificationsMail(
                    serviceContract: $serviceContract,
                    contractStatus: $contractStatus,
                    contractMessage: $message ?? '',
                ),
            );
        }
    }
}
