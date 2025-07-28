<?php

declare(strict_types=1);

namespace App\Dto\Tenant\ServiceContract;

/**
 * Data Transfer Object for Service Contract
 */
final readonly class StoreInput
{
    /**
     * @see https://www.php.net/manual/ja/language.oop5.decon.php#language.oop5.decon.constructor.promotion
     */
    public function __construct(
        public string $servicePublicId,
        public string $servicePlanPublicId,
        public string $customerPublicId,
        public string $contractName,
        public ?string $contractLanguage,
        public string $serviceUsageStatusCode,
        public ?string $contractDate,
        public ?string $contractStartDate,
        public ?string $contractEndDate,
        public ?bool $contractAutoUpdate,
        public ?string $customerContactUserName,
        public ?string $customerContactUserDept,
        public ?string $customerContactUserTitle,
        public ?string $customerContactUserEmail,
        public ?string $customerContractUserName,
        public ?string $customerContractUserDept,
        public ?string $customerContractUserTitle,
        public ?string $customerContractUserEmail,
        public ?string $customerPaymentUserName,
        public ?string $customerPaymentUserDept,
        public ?string $customerPaymentUserTitle,
        public ?string $customerPaymentUserEmail,
        public ?string $serviceRepUserPublicId,
        public ?string $serviceMgrUserPublicId,
        public ?string $quotationName,
        public ?string $quotationNumber,
        public ?string $quotationDate,
        public ?string $proposalName,
        public ?string $proposalNumber,
        public ?string $proposalDate,
        public ?string $invoiceRemindDays,
        public ?string $billingCycleCode,
        public ?string $remarks,
    ) {}

    /**
     * リクエストデータから StoreInput オブジェクトを作成する
     *
     * @param array<string, mixed> $data リクエストから受け取ったデータ配列
     * @return self 新しい StoreInput インスタンス
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            servicePublicId: $data['servicePublicId'],
            servicePlanPublicId: $data['servicePlanPublicId'],
            customerPublicId: $data['customerPublicId'],
            contractName: $data['contractName'],
            contractLanguage: $data['contractLanguage'] ?? null,
            serviceUsageStatusCode: $data['serviceUsageStatusCode'],
            contractDate: $data['contractDate'] ?? null,
            contractStartDate: $data['contractStartDate'] ?? null,
            contractEndDate: $data['contractEndDate'] ?? null,
            contractAutoUpdate: $data['contractAutoUpdate'] ?? false,
            customerContactUserName: $data['customerContactUserName'] ?? null,
            customerContactUserDept: $data['customerContactUserDept'] ?? null,
            customerContactUserTitle: $data['customerContactUserTitle'] ?? null,
            customerContactUserEmail: $data['customerContactUserEmail'] ?? null,
            customerContractUserName: $data['customerContractUserName'] ?? null,
            customerContractUserDept: $data['customerContractUserDept'] ?? null,
            customerContractUserTitle: $data['customerContractUserTitle'] ?? null,
            customerContractUserEmail: $data['customerContractUserEmail'] ?? null,
            customerPaymentUserName: $data['customerPaymentUserName'] ?? null,
            customerPaymentUserDept: $data['customerPaymentUserDept'] ?? null,
            customerPaymentUserTitle: $data['customerPaymentUserTitle'] ?? null,
            customerPaymentUserEmail: $data['customerPaymentUserEmail'] ?? null,
            serviceRepUserPublicId: $data['serviceRepUserPublicId'] ?? null,
            serviceMgrUserPublicId: $data['serviceMgrUserPublicId'] ?? null,
            quotationName: $data['quotationName'] ?? null,
            quotationNumber: $data['quotationNumber'] ?? null,
            quotationDate: $data['quotationDate'] ?? null,
            proposalName: $data['proposalName'] ?? null,
            proposalNumber: $data['proposalNumber'] ?? null,
            proposalDate: $data['proposalDate'] ?? null,
            invoiceRemindDays: $data['invoiceRemindDays'] ?? null,
            billingCycleCode: $data['billingCycleCode'] ?? null,
            remarks: $data['remarks'] ?? null,
        );
    }
}
