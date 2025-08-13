<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\DdCase;

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
        return '/v1/tenant/dd/case';
    }

    /**
     * デューデリジェンスケース一覧APIが正常なレスポンスを返すことをテストする
     */
    public function test_index_returns_successful_response(): void
    {
        $response = $this->getJson($this->getBaseUrl() . '?page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'ddCasePublicId',
                        'tenantName',
                        'companyName',
                        'ddCaseNo',
                        'currentDdStep',
                        'overallResult',
                        'customerRiskLevel',
                        'startedAt',
                        'endedAt',
                    ],
                ],
                'links',
                'meta',
            ]);
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
        // 不正なテナントパブリックID（UUIDではない）
        $response = $this->getJson($this->getBaseUrl() . '?tenantPublicId=invalid-uuid');
        $response->assertStatus(422);

        // 不正なページ番号
        $response = $this->getJson($this->getBaseUrl() . '?page=invalid');
        $response->assertStatus(422);

        // 存在しない総合結果
        $response = $this->getJson($this->getBaseUrl() . '?overallResult=INVALID');
        $response->assertStatus(422);

        // 存在しない顧客リスクレベル
        $response = $this->getJson($this->getBaseUrl() . '?customerRiskLevel=INVALID');
        $response->assertStatus(422);

        // 不正な日付フォーマット（開始日）
        $response = $this->getJson($this->getBaseUrl() . '?startedAtFrom=invalid-date');
        $response->assertStatus(422);

        // 開始日より前の終了日
        $response = $this->getJson($this->getBaseUrl() . '?startedAtFrom=2025-12-31&startedAtTo=2025-01-01');
        $response->assertStatus(422);

        // DDケース番号が長すぎる場合
        $longCaseNo = str_repeat('A', 14); // 13文字を超える
        $response = $this->getJson($this->getBaseUrl() . "?ddCaseNo={$longCaseNo}");
        $response->assertStatus(422);
    }

    /**
     * 権限のないユーザーがアクセスした場合のテスト
     */
    public function test_index_requires_authentication(): void
    {
        // 認証なしでアクセス
        $this->app['auth']->forgetGuards();

        $response = $this->getJson($this->getBaseUrl() . '?page=1');

        $response->assertStatus(401);
    }

    /**
     * admin権限でアクセスできることをテストする
     */
    public function test_index_accessible_by_admin_role(): void
    {
        // admin権限のユーザーでテスト（既にsetUpで設定済み）
        $response = $this->getJson($this->getBaseUrl() . '?page=1');

        $response->assertStatus(200);
    }

    /**
     * テナント権限でアクセスできることをテストする
     */
    public function test_index_accessible_by_tenant_role(): void
    {
        // テナント権限のユーザーでテスト
        $this->actingAs($this->createTenantManageUser());

        $response = $this->getJson($this->getBaseUrl() . '?page=1');

        $response->assertStatus(200);
    }

    /**
     * 空の結果が返されることをテストする
     */
    public function test_index_returns_empty_result_when_no_data(): void
    {
        // 存在しない条件でフィルタリング
        $response = $this->getJson($this->getBaseUrl() . '?ddCaseNo=NONEXISTENT&page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ])
            ->assertJsonFragment([
                'data' => [],
            ]);
    }
}
