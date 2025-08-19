<?php

declare(strict_types=1);

namespace App\Services\CloudSign;

use App\Enums\Service\ServiceContractStatusCode;
use App\Models\ContractWidgetSetting;
use App\Models\ServiceContract;
use App\Services\CloudSign\Traits\Documents;
use App\Services\CloudSign\Traits\DocumentsAttribute;
use App\Services\CloudSign\Traits\DocumentsFilesWidgets;
use App\Services\CloudSign\Traits\DocumentsParticipants;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;

class ContractService extends BaseService
{
    use Documents;
    use DocumentsAttribute;
    use DocumentsFilesWidgets;
    use DocumentsParticipants;

    /**
     * 契約書を送信
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \RuntimeException
     */
    public function sendContract(
        int $serviceContractId,
    ): void {
        $serviceContract = ServiceContract::findOrFail($serviceContractId);
        throw_if(
            $serviceContract->contract_status_code != ServiceContractStatusCode::ContractInfoRegistered->value,
            new \RuntimeException('The contract has already been sent.'),
        );

        $contractLanguage = $serviceContract->contract_language;
        if ($contractLanguage != 'jpn') {
            $contractLanguage = 'eng';
        }

        $documentId = $this->createAndSendCloudSignDocument(
            $serviceContract,
            $contractLanguage,
        );

        $serviceContract->contract_doc_id = $documentId;
        $serviceContract->contract_sent_at = now();
        $serviceContract->contract_status_code = ServiceContractStatusCode::ContractDocumentSent->value;
        $serviceContract->save();
    }

    /**
     * テンプレートから契約書を作成し、クラウドサインから送信
     *
     * @param \App\Models\ServiceContract $serviceContract
     * @param string                      $contractLanguage
     *
     * @return string
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Throwable
     */
    private function createAndSendCloudSignDocument(
        ServiceContract $serviceContract,
        string $contractLanguage,
    ): string {
        $servicePlan = $serviceContract->servicePlan;
        $templateId = $contractLanguage === 'jpn'
            ? $servicePlan->contract_template_jp_id
            : $servicePlan->contract_template_en_id;
        throw_if(
            !$templateId,
            new \RuntimeException('Contract template ID is not set for the service plan.'),
        );

        $parameterMappingService = new ParameterMappingService();
        $contractWidgetSettings = $parameterMappingService->buildToWidget($serviceContract, $contractLanguage);

        try {
            // テンプレートから契約書を作成
            $response = $this->postDocuments(
                null,
                [
                    'title' => $serviceContract->contract_name,
                    'template_id' => $templateId,
                ],
            );
            $this->throwIfNotSuccessful($response);

            $documentId = $response->json('id');
            $fileId = $response->json('files.0.id');
            $participants = collect((array) $response->json('participants'));
            $senderParticipant = $participants->firstWhere('order', 0);
            $recipientParticipant = $participants->firstWhere('order', 1);

            // 管理情報としてドキュメント属性を設定
            $this->setDocumentAttribute(
                $documentId,
                $serviceContract->contract_name,
                $serviceContract->service->service_code,
            );

            // ドキュメントの入力項目を設定
            $this->setDocumentWidgets($documentId, $fileId, $senderParticipant['id'], $contractWidgetSettings);

            // 送信先を設定
            $response = $this->putParticipants(
                $documentId,
                $recipientParticipant['id'],
                [
                    'email' => $serviceContract->customer_contract_user_email,
                    'name' => $serviceContract->customer_contract_user_name,
                    'language_code' => $contractLanguage == 'jpn' ? 'ja' : 'en',
                ],
            );
            $this->throwIfNotSuccessful($response);

            // ドキュメントを送信
            $response = $this->postDocuments(
                $documentId,
            );
            $this->throwIfNotSuccessful($response);

            return $documentId;

        } catch (ConnectionException $e) {
            \Log::error($e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * ドキュメントの属性を設定
     *
     * @param string $documentId
     * @param string $title
     * @param string $content
     *
     * @throws \RuntimeException|\Illuminate\Http\Client\ConnectionException
     */
    private function setDocumentAttribute(
        string $documentId,
        string $title,
        string $content,
    ): void {
        $data = [
            'title' => $title,
            'options' => [
                [
                    'order' => (int) config('services.cloudsign.attribute_order', 0),
                    'content' => $content,
                ],
            ],
        ];

        $response = $this->putAttribute($documentId, $data);
        $this->throwIfNotSuccessful($response);
    }

    /**
     * ドキュメントのウィジェットを設定
     *
     * @param string $documentId
     * @param string $fileId
     * @param string $participantId
     * @param Collection<int, ContractWidgetSetting> $widgetSettings
     *
     * @throws \RuntimeException|\Illuminate\Http\Client\ConnectionException
     */
    private function setDocumentWidgets(
        string $documentId,
        string $fileId,
        string $participantId,
        Collection $widgetSettings,
    ): void {
        foreach ($widgetSettings as $widgetSetting) {
            $widgetData = [
                'participant_id' => $participantId,
                'type' => 1,
                'page' => 0,
                'x' => $widgetSetting->widget_x_coord,
                'y' => $widgetSetting->widget_y_coord,
                'text' => $widgetSetting->widget_source_value,
            ];
            $response = $this->postWidgets($documentId, $fileId, $widgetData);
            $this->throwIfNotSuccessful($response);
        }
    }

    private function throwIfNotSuccessful(Response $response): void
    {
        if (!$response->successful()) {
            throw new \RuntimeException(
                'Failed to send contract. Status: ' . $response->status() .
                ' Body: ' . $response->body(),
            );
        }
    }
}
