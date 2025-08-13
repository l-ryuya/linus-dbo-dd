<?php

declare(strict_types=1);

namespace App\Services\DboBilling;

class CustomerService extends BaseService
{
    /**
     * 顧客を追加する
     *
     * @param string $name 顧客名
     * @param string $email メールアドレス
     * @param string $language 言語コード
     * @param string $externalId 外部ID
     * @param string $serviceId サービスID
     * @param string $serviceContractId サービス契約ID
     * @param array<int> $invoiceRemindDays 請求リマインド日数の配列
     *
     * @return array<string, mixed> レスポンスデータ
     *
     * @throws \RuntimeException|\Illuminate\Http\Client\ConnectionException API呼び出しが失敗した場合
     */
    public function addCustomer(
        string $name,
        string $email,
        string $language,
        string $externalId,
        string $serviceId,
        string $serviceContractId,
        array $invoiceRemindDays = [],
    ): array {
        $response = $this->postApi(
            '/customers/dd',
            [
                'name' => $name,
                'email' => $email,
                'language' => $language == 'jpn' ? 'JAPANESE' : 'ENGLISH',
                'externalId' => $externalId,
                'serviceId' => $serviceId,
                'serviceContractId' => $serviceContractId,
                'invoiceRemindDays' => $invoiceRemindDays,
            ],
        );
        if (!$response->successful()) {
            throw new \RuntimeException(
                'Failed to send contract. Status: ' . $response->status() .
                ' Body: ' . $response->body(),
            );
        }

        return $response->json();
    }
}
