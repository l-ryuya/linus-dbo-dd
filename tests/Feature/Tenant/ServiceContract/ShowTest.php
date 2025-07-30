<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\ServiceContract;

use App\Models\ServiceContract;
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
        return '/v1/tenant/service-contracts';
    }

    /**
     * サービス契約詳細APIが正常なレスポンスを返すことをテストする
     */
    public function test_show_returns_successful_response(): void
    {
        // テスト用のServiceContractデータを取得
        $serviceContract = ServiceContract::first();

        $response = $this->getJson($this->getBaseUrl() . '/' . $serviceContract->public_id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'serviceContractPublicId',
                    'tenantName',
                    'servicePublicId',
                    'serviceName',
                    'servicePlanPublicId',
                    'servicePlanName',
                    'customerPublicId',
                    'customerName',
                    'customerNameEn',
                    'contractName',
                    'contractLanguageName',
                    'contractLanguage',
                    'contractStatus',
                    'contractStatusCode',
                    'serviceUsageStatus',
                    'serviceUsageStatusCode',
                    'contractDate',
                    'contractStartDate',
                    'contractEndDate',
                    'contractAutoUpdate',
                    'customerContactUserName',
                    'customerContactUserDept',
                    'customerContactUserTitle',
                    'customerContactUserEmail',
                    'customerContractUserName',
                    'customerContractUserDept',
                    'customerContractUserTitle',
                    'customerContractUserEmail',
                    'customerPaymentUserName',
                    'customerPaymentUserDept',
                    'customerPaymentUserTitle',
                    'customerPaymentUserEmail',
                    'serviceRepUserName',
                    'serviceRepUserPublicId',
                    'serviceMgrUserName',
                    'serviceMgrUserPublicId',
                    'quotationName',
                    'quotationNumber',
                    'quotationDate',
                    'proposalName',
                    'proposalNumber',
                    'proposalDate',
                    'invoiceRemindDays',
                    'billingCycle',
                    'billingCycleCode',
                    'remarks',
                ],
            ]);
    }

    /**
     * 存在しないサービス契約IDを指定した場合に404エラーが返されることをテストする
     */
    public function test_show_returns_404_for_non_existent_service_contract(): void
    {
        $response = $this->getJson($this->getBaseUrl() . '/non-existent-service-contract-id');

        $response->assertStatus(404);
    }

    /**
     * 権限のないユーザーがアクセスした場合のテスト
     */
    public function test_show_requires_authentication(): void
    {
        // 認証なしでアクセス
        $this->app['auth']->forgetGuards();

        $serviceContract = ServiceContract::first();
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

        $serviceContract = ServiceContract::first();
        $response = $this->getJson($this->getBaseUrl() . '/' . $serviceContract->public_id);

        $response->assertStatus(200);
    }
}
