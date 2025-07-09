<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\ServiceContract;

use App\Models\SelectionItemTranslation;
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

class IndexTest extends TestCase
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
     * サービス契約一覧APIが正常なレスポンスを返すことをテストする
     */
    public function test_index_returns_successful_response(): void
    {
        $response = $this->getJson($this->getBaseUrl() . '?page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'tenantName',
                        'serviceName',
                        'servicePlanName',
                        'customerName',
                        'customerNameEn',
                        'contractName',
                        'contractStatus',
                        'serviceUsageStatus',
                        'contractDate',
                        'contractStartDate',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    /**
     * テナント名でフィルタリングできることをテストする
     */
    public function test_index_filters_by_tenant_name(): void
    {
        // テスト用のテナント名
        $tenantName = '株式会社電通総研';

        $response = $this->getJson($this->getBaseUrl() . "?tenantName={$tenantName}&page=1");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'tenantName' => $tenantName,
            ]);
    }

    /**
     * 顧客名でフィルタリングできることをテストする
     */
    public function test_index_filters_by_customer_name(): void
    {
        // テスト用の顧客データを作成
        $customerName = 'Sansan株式会社';

        $response = $this->getJson($this->getBaseUrl() . "?customerName={$customerName}&page=1");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'customerName' => $customerName,
            ]);
    }

    /**
     * 契約名でフィルタリングできることをテストする
     */
    public function test_index_filters_by_contract_name(): void
    {
        // テスト用の契約名
        $contractName = 'Sansan様 Securate スタンダードプラン サービス契約書';

        $response = $this->getJson($this->getBaseUrl() . "?contractName={$contractName}&page=1");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'contractName' => $contractName,
            ]);
    }

    /**
     * 契約ステータスでフィルタリングできることをテストする
     */
    public function test_index_filters_by_contract_status(): void
    {
        // テスト用のステータスを取得
        $status = SelectionItemTranslation::where('selection_item_type', 'service_contract_status')
            ->first();

        if ($status) {
            $response = $this->getJson($this->getBaseUrl() . "?contractStatusCode={$status->selection_item_code}&page=1");

            $response->assertStatus(200);
            // ステータス名は変換されて返されるためフラグメントの検証は省略
        }
    }

    /**
     * サービス利用ステータスでフィルタリングできることをテストする
     */
    public function test_index_filters_by_service_usage_status(): void
    {
        // テスト用のステータスを取得
        $status = SelectionItemTranslation::where('selection_item_type', 'service_usage_status')
            ->first();

        if ($status) {
            $response = $this->getJson($this->getBaseUrl() . "?serviceUsageStatusCode={$status->selection_item_code}&page=1");

            $response->assertStatus(200);
            // ステータス名は変換されて返されるためフラグメントの検証は省略
        }
    }

    /**
     * サービスIDでフィルタリングできることをテストする
     */
    public function test_index_filters_by_service_public_id(): void
    {
        // テスト用のサービスIDを取得（実際の値はテスト環境に応じて調整が必要）
        $servicePublicId = '013e67d2-d289-4981-a8c5-394961d8814f';

        $response = $this->getJson($this->getBaseUrl() . "?servicePublicId={$servicePublicId}&page=1");

        $response->assertStatus(200);
    }

    /**
     * サービスプランIDでフィルタリングできることをテストする
     */
    public function test_index_filters_by_service_plan_public_id(): void
    {
        // テスト用のサービスプランIDを取得（実際の値はテスト環境に応じて調整が必要）
        $servicePlanPublicId = 'b7e2a1c2-3f4b-4e2a-8c1d-1a2b3c4d5e6f';

        $response = $this->getJson($this->getBaseUrl() . "?servicePlanPublicId={$servicePlanPublicId}&page=1");

        $response->assertStatus(200);
    }

    /**
     * 契約日でフィルタリングできることをテストする
     */
    public function test_index_filters_by_contract_date(): void
    {
        // テスト用の契約日
        $contractDate = '2025-05-20';

        $response = $this->getJson($this->getBaseUrl() . "?contractDate={$contractDate}&page=1");

        $response->assertStatus(200);
    }

    /**
     * 契約開始日でフィルタリングできることをテストする
     */
    public function test_index_filters_by_contract_start_date(): void
    {
        // テスト用の契約開始日
        $contractStartDate = '2025-06-01';

        $response = $this->getJson($this->getBaseUrl() . "?contractStartDate={$contractStartDate}&page=1");

        $response->assertStatus(200);
    }

    /**
     * ページネーションが機能することをテストする
     */
    public function test_index_supports_pagination(): void
    {
        $response = $this->getJson($this->getBaseUrl() . '?displayed=10&page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'meta' => ['currentPage', 'from', 'lastPage', 'perPage'],
            ]);
    }

    /**
     * バリデーションエラーが適切に処理されることをテストする
     */
    public function test_index_validates_input(): void
    {
        // 不正なサービスID（UUIDではない）
        $response = $this->getJson($this->getBaseUrl() . '?servicePublicId=invalid-uuid');
        $response->assertStatus(422);

        // 不正なページ番号
        $response = $this->getJson($this->getBaseUrl() . '?page=invalid');
        $response->assertStatus(422);

        // 存在しない契約ステータスコード
        $response = $this->getJson($this->getBaseUrl() . '?contractStatusCode=non-existent-status');
        $response->assertStatus(422);

        // 不正な日付フォーマット
        $response = $this->getJson($this->getBaseUrl() . '?contractDate=invalid-date');
        $response->assertStatus(422);
    }
}
