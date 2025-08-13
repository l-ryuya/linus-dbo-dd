<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Customer;

use App\Models\Customer;
use Database\Seeders\Base\CompaniesSeeder;
use Database\Seeders\Base\CompanyNameTranslationsSeeder;
use Database\Seeders\Base\CountryRegionsSeeder;
use Database\Seeders\Base\CountryRegionsTranslationsSeeder;
use Database\Seeders\Base\SelectionItemsSeeder;
use Database\Seeders\Base\SelectionItemTranslationsSeeder;
use Database\Seeders\Base\ServicePlansSeeder;
use Database\Seeders\Base\ServicePlanTranslationsSeeder;
use Database\Seeders\Base\ServicesSeeder;
use Database\Seeders\Base\ServiceTranslationsSeeder;
use Database\Seeders\Base\TenantsSeeder;
use Database\Seeders\Base\TimeZonesSeeder;
use Database\Seeders\Base\UserOptionsSeeder;
use Database\Seeders\TestDatabaseSeeder;
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
            ServicesSeeder::class,
            ServicePlansSeeder::class,
            ServiceTranslationsSeeder::class,
            ServicePlanTranslationsSeeder::class,
            UserOptionsSeeder::class,
            TestDatabaseSeeder::class,
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
                    'firstServiceStartDate',
                    'lastServiceEndDate',
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

    /**
     * 権限のないユーザーがアクセスした場合のテスト
     */
    public function test_show_requires_authentication(): void
    {
        // 認証なしでアクセス
        $this->app['auth']->forgetGuards();

        $serviceContract = Customer::first();
        $response = $this->getJson($this->getBaseUrl() . '/' . $serviceContract->public_id);

        $response->assertStatus(401);
    }

    /**
     * テナント権限でアクセスできることをテストする
     */
    public function test_show_accessible_by_tenant_role(): void
    {
        // テナント権限のユーザーでテスト
        $this->actingAs($this->createTenantManageUser());

        $serviceContract = Customer::first();
        $response = $this->getJson($this->getBaseUrl() . '/' . $serviceContract->public_id);

        $response->assertStatus(200);
    }
}
