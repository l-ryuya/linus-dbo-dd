<?php

declare(strict_types=1);

namespace App\UseCases\Webhooks;

use App\Enums\CloudSignStatus;
use App\Enums\Service\ServiceContractStatusCode;
use App\Models\ServiceContract;
use App\Services\ServiceContract\ContractStatusService;
use Illuminate\Support\Facades\DB;

readonly class CloudSignWebhookAction
{
    public function __construct(
        private ContractStatusService $contractStatusService,
    ) {}

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
                ->where('contract_status_code', ServiceContractStatusCode::ContractDocumentSent->value)
                ->lockForUpdate()
                ->firstOrFail();

            $status = CloudSignStatus::tryFrom($payload['status']);
            if ($status === null) {
                throw new \InvalidArgumentException("Unknown CloudSign status: {$payload['status']}");
            }

            $this->contractStatusService->updateContractStatus($serviceContract, $status);
            $serviceContract->save();

            DB::commit();

            $this->contractStatusService->handlePostProcessing($serviceContract, $status, $payload['text'] ?? null);

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
}
