<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Customer;

use App\Models\Customer;
use Database\Seeders\base\CompaniesSeeder;
use Database\Seeders\base\CompanyNameTranslationsSeeder;
use Database\Seeders\base\CountryRegionsSeeder;
use Database\Seeders\base\CountryRegionsTranslationsSeeder;
use Database\Seeders\base\CustomersSeeder;
use Database\Seeders\base\SelectionItemsSeeder;
use Database\Seeders\base\SelectionItemTranslationsSeeder;
use Database\Seeders\base\ServiceContractsSeeder;
use Database\Seeders\base\ServicePlansSeeder;
use Database\Seeders\base\ServicePlanTranslationsSeeder;
use Database\Seeders\base\ServicesSeeder;
use Database\Seeders\base\ServiceTranslationsSeeder;
use Database\Seeders\base\TenantsSeeder;
use Database\Seeders\base\TimeZonesSeeder;
use Database\Seeders\base\UserOptionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            TimeZonesSeeder::class,
            SelectionItemsSeeder::class,
            SelectionItemTranslationsSeeder::class,
            CountryRegionsSeeder::class,
            CountryRegionsTranslationsSeeder::class,
            TenantsSeeder::class,
            CompaniesSeeder::class,
            CompanyNameTranslationsSeeder::class,
            CustomersSeeder::class,
            ServicesSeeder::class,
            ServicePlansSeeder::class,
            ServiceTranslationsSeeder::class,
            ServicePlanTranslationsSeeder::class,
            ServiceContractsSeeder::class,
            UserOptionsSeeder::class,
        ]);

        // テスト用の認証を設定
        $this->actingAs($this->createServiceManageUser());
    }

    /**
     * APIエンドポイントのベースURLを取得
     *
     * @return string
     */
    private function getBaseUrl(): string
    {
        return '/v1/tenant/customers';
    }

    /**
     * 顧客詳細APIが正常なレスポンスを返すことをテストする
     */
    public function test_show_returns_successful_response(): void
    {
        // テスト用のCustomerデータを取得
        $customer = Customer::first();

        $response = $this->getJson($this->getBaseUrl() . '/' . $customer->public_id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'customerPublicId',
                    'customerStatus',
                    'customerStatusCode',
                    'customerNameEn',
                    'customerName',
                    'websiteUrl',
                    'shareholdersUrl',
                    'executivesUrl',
                    'defaultLanguageCode',
                    'countryCodeAlpha3',
                    'postal',
                    'state',
                    'city',
                    'street',
                    'building',
                    'remarks',
                    'serviceContracts' => [
                        '*' => [
                            'publicId',
                            'serviceName',
                            'servicePlanName',
                            'serviceUsageStatus',
                            'serviceUsageStatusCode',
                            'contractStatus',
                            'contractStatusCode',
                        ],
                    ],
                ],
            ]);
    }

    /**
     * 存在しない顧客IDを指定した場合に404エラーが返されることをテストする
     */
    public function test_show_returns_404_for_non_existent_customer(): void
    {
        $response = $this->getJson($this->getBaseUrl() . '/non-existent-customer-id');

        $response->assertStatus(404);
    }

    /**
     * レスポンスに関連するサービス契約情報が含まれていることをテストする
     */
    public function test_show_includes_service_contracts(): void
    {
        // サービス契約が紐づいているカスタマーを取得
        $customerWithContracts = Customer::whereHas('serviceContracts')->first();

        if ($customerWithContracts) {
            $response = $this->getJson($this->getBaseUrl() . '/' . $customerWithContracts->public_id);

            $response->assertStatus(200)
                ->assertJsonPath('data.serviceContracts', fn($contracts) => count($contracts) > 0);
        } else {
            $this->markTestSkipped('サービス契約が紐づいた顧客データがありません。');
        }
    }
}
