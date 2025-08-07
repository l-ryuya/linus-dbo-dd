<?php

declare(strict_types=1);

namespace App\UseCases\Tenant\ServiceContract;

use App\Enums\CloudSignStatus;
use App\Enums\Service\ServiceContractStatusCode;
use App\Exceptions\LogicValidationException;
use App\Models\ServiceContract;
use App\Services\CloudSign\GetDocumentService;
use App\Services\ServiceContract\ContractStatusService;
use Illuminate\Support\Facades\DB;

readonly class CloudsignStatusSyncAction
{
    public function __construct(
        private ContractStatusService $contractStatusService,
    ) {}

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
                ServiceContractStatusCode::ContractDocumentSent->isEqualValue($serviceContract->contract_status_code),
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

            $this->contractStatusService->updateContractStatus($serviceContract, $status);
            $serviceContract->save();

            DB::commit();

            $this->contractStatusService->handlePostProcessing(
                $serviceContract,
                $status,
                '<' . config('services.cloudsign.host') . '/documents/' . $serviceContract->contract_doc_id . '/summary>',
            );

            return (object) [
                'contractStatus' => $status->getStatusText(),
            ];
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
