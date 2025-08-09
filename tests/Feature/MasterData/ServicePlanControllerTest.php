<?php

declare(strict_types=1);

namespace Tests\Feature\MasterData;

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

class ServicePlanControllerTest extends TestCase
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
        return '/v1/service-plans';
    }

    /**
     * サービスプラン一覧APIが正常なレスポンスを返すことをテストする
     */
    public function test_index_returns_successful_response(): void
    {
        $service = Service::where('tenant_id', $this->tenant->tenant_id)
            ->where('service_code', 'Securate')
            ->first();

        $response = $this->getJson($this->getBaseUrl() . "?servicePublicId={$service->public_id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'servicePlanPublicId',
                        'servicePlanStatus',
                        'servicePlanStatusCode',
                        'billingCycle',
                        'unitPrice',
                        'servicePlanStartDate',
                        'servicePlanEndDate',
                        'servicePlanName',
                        'servicePlanDescription',
                    ],
                ],
            ]);
    }

    /**
     * バリデーションエラーが適切に処理されることをテストする
     */
    public function test_index_validates_input(): void
    {
        // servicePublicIdパラメータが無い場合
        $response = $this->getJson($this->getBaseUrl());
        $response->assertStatus(422);

        // servicePublicIdパラメータが短すぎる場合
        $response = $this->getJson($this->getBaseUrl() . '?servicePublicId=1234567');
        $response->assertStatus(422);

        // servicePublicIdパラメータが長すぎる場合
        $tooLongCode = str_repeat('1', 37); // 36文字を超える
        $response = $this->getJson($this->getBaseUrl() . '?servicePublicId=' . $tooLongCode);
        $response->assertStatus(422);
    }

    /**
     * 存在しないサービスコードでも正常に空の配列が返ることをテストする
     */
    public function test_index_returns_empty_array_for_nonexistent_service_code(): void
    {
        // 存在しないサービスコードを指定
        $response = $this->getJson($this->getBaseUrl() . '?servicePublicId=270b1f55-3181-41be-98f3-a804836e34c8');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }
}
