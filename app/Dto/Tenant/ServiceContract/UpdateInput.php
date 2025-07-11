<?php

declare(strict_types=1);

namespace App\Dto\Tenant\ServiceContract;

/**
 * Data Transfer Object for Service Contract
 */
final readonly class UpdateInput
{
    /**
     * @see https://www.php.net/manual/ja/language.oop5.decon.php#language.oop5.decon.constructor.promotion
     */
    public function __construct(
        public string $servicePublicId,
        public string $servicePlanPublicId,
        public string $customerPublicId,
        public string $contractName,
        public string $contractLanguage,
        public string $contractStatusCode,
        public string $serviceUsageStatusCode,
        public string $contractDate,
        public string $contractStartDate,
        public ?string $contractEndDate,
        public bool $contractAutoUpdate,
        public string $customerContactUserName,
        public ?string $customerContactUserDept,
        public ?string $customerContactUserTitle,
        public string $customerContactUserMail,
        public string $customerContractUserName,
        public ?string $customerContractUserDept,
        public ?string $customerContractUserTitle,
        public string $customerContractUserMail,
        public string $customerPaymentUserName,
        public ?string $customerPaymentUserDept,
        public ?string $customerPaymentUserTitle,
        public string $customerPaymentUserMail,
        public string $serviceRepUserOptionPublicId,
        public string $serviceMgrUserOptionPublicId,
        public ?string $invoiceRemindDays,
        public ?string $billingCycleCode,
        public ?string $remarks,
    ) {}

    /**
     * リクエストデータから UpdateInput オブジェクトを作成する
     *
     * @param array<string, mixed> $data リクエストから受け取ったデータ配列
     * @return self 新しい UpdateInput インスタンス
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            servicePublicId: $data['servicePublicId'],
            servicePlanPublicId: $data['servicePlanPublicId'],
            customerPublicId: $data['customerPublicId'],
            contractName: $data['contractName'],
            contractLanguage: $data['contractLanguage'],
            contractStatusCode: $data['contractStatusCode'],
            serviceUsageStatusCode: $data['serviceUsageStatusCode'],
            contractDate: $data['contractDate'],
            contractStartDate: $data['contractStartDate'],
            contractEndDate: $data['contractEndDate'] ?? null,
            contractAutoUpdate: $data['contractAutoUpdate'],
            customerContactUserName: $data['customerContactUserName'],
            customerContactUserDept: $data['customerContactUserDept'] ?? null,
            customerContactUserTitle: $data['customerContactUserTitle'] ?? null,
            customerContactUserMail: $data['customerContactUserMail'],
            customerContractUserName: $data['customerContractUserName'],
            customerContractUserDept: $data['customerContractUserDept'] ?? null,
            customerContractUserTitle: $data['customerContractUserTitle'] ?? null,
            customerContractUserMail: $data['customerContractUserMail'],
            customerPaymentUserName: $data['customerPaymentUserName'],
            customerPaymentUserDept: $data['customerPaymentUserDept'] ?? null,
            customerPaymentUserTitle: $data['customerPaymentUserTitle'] ?? null,
            customerPaymentUserMail: $data['customerPaymentUserMail'],
            serviceRepUserOptionPublicId: $data['serviceRepUserOptionPublicId'],
            serviceMgrUserOptionPublicId: $data['serviceMgrUserOptionPublicId'],
            invoiceRemindDays: $data['invoiceRemindDays'] ?? null,
            billingCycleCode: $data['billingCycleCode'] ?? null,
            remarks: $data['remarks'] ?? null,
        );
    }
}
