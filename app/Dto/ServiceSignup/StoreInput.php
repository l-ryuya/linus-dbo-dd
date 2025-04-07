<?php

namespace App\Dto\ServiceSignup;

/**
 * Data Transfer Object
 */
final readonly class StoreInput
{
    /**
     * @see https://www.php.net/manual/ja/language.oop5.decon.php#language.oop5.decon.constructor.promotion
     */
    public function __construct(
        public string $companyName,
        public ?string $departmentName,

        public string $serviceCode,
        public string $servicePlan,
        public string $paymentCycle,
        public string $paymentMethod,
        public string $secondLanguage,

        // 担当者情報
        public string $contactPersonLastName,
        public string $contactPersonFirstName,
        public ?string $contactPersonMiddleName,
        public ?string $contactPersonPosition,
        public string $contactPersonEmail,

        // 契約担当者情報
        public string $contractPersonLastName,
        public string $contractPersonFirstName,
        public ?string $contractPersonMiddleName,
        public ?string $contractPersonPosition,
        public string $contractPersonEmail,

        // 所在地情報
        public string $country,
        public string $postalCode,
        public string $state,
        public string $city,
        public string $addressLine1,
        public ?string $addressLine2,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            companyName: $data['companyName'],
            departmentName: $data['departmentName'] ?? null,

            serviceCode: $data['serviceCode'],
            servicePlan: $data['servicePlan'],
            paymentCycle: $data['paymentCycle'],
            paymentMethod: $data['paymentMethod'],
            secondLanguage: $data['secondLanguage'],

            contactPersonLastName: $data['contactPersonLastName'],
            contactPersonFirstName: $data['contactPersonFirstName'],
            contactPersonMiddleName: $data['contactPersonMiddleName'] ?? null,
            contactPersonPosition: $data['contactPersonPosition'] ?? null,
            contactPersonEmail: $data['contactPersonEmail'],

            contractPersonLastName: $data['contractPersonLastName'],
            contractPersonFirstName: $data['contractPersonFirstName'],
            contractPersonMiddleName: $data['contractPersonMiddleName'] ?? null,
            contractPersonPosition: $data['contractPersonPosition'] ?? null,
            contractPersonEmail: $data['contractPersonEmail'],

            country: $data['country'],
            postalCode: $data['postalCode'],
            state: $data['state'],
            city: $data['city'],
            addressLine1: $data['addressLine1'],
            addressLine2: $data['addressLine2'] ?? null,
        );
    }
}
