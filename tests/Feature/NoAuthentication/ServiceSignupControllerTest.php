<?php

declare(strict_types=1);

namespace Tests\Feature\NoAuthentication;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceSignupControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    /**
     * APIエンドポイントのベースURLを取得
     *
     * @return string
     */
    private function getBaseUrl(): string
    {
        return '/v1/service-signup';
    }

    /**
     * サービス申込APIが正常なレスポンスを返すことをテストする
     */
    public function test_store_returns_successful_response(): void
    {
        $payload = [
            'companyName' => 'Test Company ' . uniqid(),
            'departmentName' => 'Test Department',
            'serviceCode' => 'SV-00003',
            'servicePlan' => 'SP-000001',
            'paymentCycle' => 'Monthly',
            'paymentMethod' => 'Card',
            'secondLanguage' => 'jpn',
            'contactPersonLastName' => 'Test',
            'contactPersonFirstName' => 'User',
            'contactPersonMiddleName' => null,
            'contactPersonPosition' => 'Manager',
            'contactPersonEmail' => 'test.user@example.com',
            'contractPersonLastName' => 'Contract',
            'contractPersonFirstName' => 'Person',
            'contractPersonMiddleName' => null,
            'contractPersonPosition' => 'Director',
            'contractPersonEmail' => 'contract.person@example.com',
            'country' => 'JPN',
            'postalCode' => '123-4567',
            'state' => 'Tokyo',
            'city' => 'Chiyoda',
            'addressLine1' => '1-1-1 Test Address',
            'addressLine2' => 'Building 1',
        ];

        $response = $this->postJson($this->getBaseUrl(), $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'contactPersonUserCode',
                    'contractPersonUserCode',
                    'companyCode',
                    'serviceContractCode',
                ],
            ]);

        // データベースにレコードが作成されたことを確認
        $responseData = $response->json('data');

        // 会社データが正しく保存されていることを確認
        $this->assertDatabaseHas('companies', [
            'company_code' => $responseData['companyCode'],
            'company_name_en' => $payload['companyName'],
            'country_region_code' => $payload['country'],
            'postal_code_en' => $payload['postalCode'],
            'prefecture_en' => $payload['state'],
            'city_en' => $payload['city'],
            'street_en' => $payload['addressLine1'],
            'building_room_en' => $payload['addressLine2'],
            'second_language_type' => 'language_code',
            'second_language_code' => $payload['secondLanguage'],
            'company_status_code' => 'Under DD',
        ]);

        // 担当者ユーザーが正しく保存されていることを確認
        $this->assertDatabaseHas('users', [
            'user_code' => $responseData['contactPersonUserCode'],
            'last_name_en' => $payload['contactPersonLastName'],
            'first_name_en' => $payload['contactPersonFirstName'],
            'middle_name_en' => $payload['contactPersonMiddleName'],
            'position_en' => $payload['contactPersonPosition'],
            'email' => $payload['contactPersonEmail'],
            'user_status' => 'Under DD',
        ]);

        // 契約担当者ユーザーが正しく保存されていることを確認
        $this->assertDatabaseHas('users', [
            'user_code' => $responseData['contractPersonUserCode'],
            'last_name_en' => $payload['contractPersonLastName'],
            'first_name_en' => $payload['contractPersonFirstName'],
            'middle_name_en' => $payload['contractPersonMiddleName'],
            'position_en' => $payload['contractPersonPosition'],
            'email' => $payload['contractPersonEmail'],
            'user_status' => 'Under DD',
        ]);

        // サービス契約が正しく保存されていることを確認
        $this->assertDatabaseHas('service_contracts', [
            'service_contract_code' => $responseData['serviceContractCode'],
            'department_name_en' => $payload['departmentName'],
            'service_code' => $payload['serviceCode'],
            'service_plan_code' => $payload['servicePlan'],
            'payment_cycle_code' => $payload['paymentCycle'],
            'payment_method_code' => $payload['paymentMethod'],
            'service_usage_status_code' => 'Under DD',
            'service_contract_status_code' => 'Not Requested',
        ]);
    }
}
