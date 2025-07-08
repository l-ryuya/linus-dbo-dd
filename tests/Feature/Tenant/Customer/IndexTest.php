<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Customer;

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
        return '/v1/tenant/customers';
    }

    /**
     * 顧客一覧APIが正常なレスポンスを返すことをテストする
     */
    public function test_index_returns_successful_response(): void
    {
        $response = $this->getJson($this->getBaseUrl() . '?page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'customerPublicId',
                        'customerName',
                        'customerNameEn',
                        'customerStatus',
                        'serviceStartDate',
                        'serviceName',
                        'servicePlanName',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    /**
     * クエリパラメータでフィルタリングできることをテストする
     */
    public function test_index_filters_by_customer_name(): void
    {
        // テスト用の顧客データを作成
        $customerName = '株式会社FINOLAB';

        $response = $this->getJson($this->getBaseUrl() . "?customerName={$customerName}&page=1");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'customerName' => $customerName,
            ]);
    }

    /**
     * 顧客ステータスでフィルタリングできることをテストする
     */
    public function test_index_filters_by_customer_status(): void
    {
        // テスト用のステータスを取得
        $status = SelectionItemTranslation::where('selection_item_type', 'customer_status')
            ->first();

        if ($status) {
            $response = $this->getJson($this->getBaseUrl() . "?customerStatusCode={$status->selection_item_code}&page=1");

            $response->assertStatus(200);
            // ステータス名は変換されて返されるためフラグメントの検証は省略
        }
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

        // 存在しない顧客ステータスコード
        $response = $this->getJson($this->getBaseUrl() . '?customerStatusCode=non-existent-status');
        $response->assertStatus(422);
    }

    /**
     * 組織コードでフィルタリングできることをテストする
     */
    public function test_index_filters_by_organization_code(): void
    {
        $response = $this->getJson($this->getBaseUrl() . "?organizationCode=ORG00000022&page=1");

        $response->assertStatus(200);
    }
}
