<?php

declare(strict_types=1);

namespace Tests\Feature\MasterData;

use App\Enums\Service\ServiceStatusCode;
use App\Models\Service;
use App\Models\Tenant;
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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceControllerTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

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
        ]);

        $authUser = $this->createServiceManageUser();
        $this->tenant = $authUser->getUserOption()->tenant;

        // テスト用の認証を設定
        $this->actingAs($authUser);
    }

    /**
     * APIエンドポイントのベースURLを取得
     *
     * @return string
     */
    private function getBaseUrl(): string
    {
        return '/v1/services';
    }

    /**
     * サービス一覧APIが正常なレスポンスを返すことをテストする
     */
    public function test_index_returns_successful_response(): void
    {
        $response = $this->getJson($this->getBaseUrl());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'servicePublicId',
                        'serviceStatus',
                        'serviceStatusCode',
                        'serviceStartDate',
                        'serviceEndDate',
                        'serviceCondition',
                        'ddPlan',
                        'serviceName',
                        'serviceDescription',
                    ],
                ],
            ]);
    }

    /**
     * テナントに関連するサービスのみが返されることをテストする
     */
    public function test_index_returns_only_tenant_related_services(): void
    {
        $response = $this->getJson($this->getBaseUrl());

        $response->assertStatus(200);
        $responseData = $response->json('data');

        // 返されたサービスがテナントに関連付けられたものであることを確認
        foreach ($responseData as $service) {
            $serviceModel = Service::where('public_id', $service['servicePublicId'])->first();
            $this->assertEquals($this->tenant->tenant_id, $serviceModel->tenant_id);
        }
    }

    /**
     * アクティブなサービスのみが返されることをテストする
     */
    public function test_index_returns_only_active_services_by_default(): void
    {
        $response = $this->getJson($this->getBaseUrl());

        $response->assertStatus(200);
        $responseData = $response->json('data');

        // 返されたサービスがすべてアクティブなステータスであることを確認
        foreach ($responseData as $service) {
            $this->assertEquals(ServiceStatusCode::Active->value, $service['serviceStatusCode']);
        }
    }

    /**
     * 未認証ユーザーがアクセスした場合に401エラーが返ることをテストする
     */
    public function test_index_returns_unauthorized_for_unauthenticated_user(): void
    {
        // 認証をリセット
        $this->app['auth']->forgetGuards();

        $response = $this->getJson($this->getBaseUrl());

        $response->assertStatus(401);
    }
}
