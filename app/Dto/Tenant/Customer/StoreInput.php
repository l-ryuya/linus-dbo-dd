<?php

declare(strict_types=1);

namespace App\Dto\Tenant\Customer;

/**
 * Data Transfer Object
 */
final readonly class StoreInput
{
    /**
     * @see https://www.php.net/manual/ja/language.oop5.decon.php#language.oop5.decon.constructor.promotion
     */
    public function __construct(
        public string $customerName,
        public string $customerNameEn,
        public string $websiteUrl,
        public string $shareholdersUrl,
        public string $executivesUrl,
        public string $countryCodeAlpha3,
        public string $languageCode,
        public ?string $postalCode,
        public ?string $state,
        public ?string $city,
        public ?string $street,
        public ?string $building,
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
            customerName: $data['customerName'],
            customerNameEn: $data['customerNameEn'],
            websiteUrl: $data['websiteUrl'],
            shareholdersUrl: $data['shareholdersUrl'],
            executivesUrl: $data['executivesUrl'],
            countryCodeAlpha3: $data['countryCodeAlpha3'],
            languageCode: $data['languageCode'],
            postalCode: $data['postalCode'] ?? null,
            state: $data['state'] ?? null,
            city: $data['city'] ?? null,
            street: $data['street'] ?? null,
            building: $data['building'] ?? null,
            remarks: $data['remarks'] ?? null,
        );
    }
}
