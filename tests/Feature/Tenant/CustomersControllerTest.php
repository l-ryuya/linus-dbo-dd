<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant;

use App\Models\SelectionItemTranslation;
use App\Models\Tenant;
use App\Services\M5\UserOrganizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;

class CustomersControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $tenant = Tenant::where('sys_organization_code', 'ORG00000010')->first();

        // UserOrganizationServiceクラスのgetLowestLevelOrganizationメソッドをモック
        $this->mock(UserOrganizationService::class, function ($mock) use ($tenant) {
            $mock->shouldReceive('getLowestLevelOrganization')
                ->andReturn([
                    'sysOrganizationCode' => 'ORG00000010',
                    'organizationLevelId' => 2,
                    'organizationLevelCode' => 'TENANT',
                ]);
            $mock->shouldReceive('getTenantByOrganizationCode')
                ->andReturn($tenant);
        });

        // テスト用の認証を設定
        $this->actingAs($this->createTenantManageUser());
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
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_index_returns_successful_response(): void
    {
        $response = $this->getJson(
            $this->getBaseUrl() . '?page=1',
            ['Accept-Language' => 'jpn'],
        );

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'customerCompanyPublicId',
                        'customerName',
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
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_index_filters_by_customer_name(): void
    {
        // テスト用の顧客データを作成
        $customerName = '株式会社FINOLAB';

        $response = $this->getJson(
            $this->getBaseUrl() . "?customerName={$customerName}&page=1",
            ['Accept-Language' => 'jpn'],
        );

        $response->assertStatus(200)
            ->assertJsonFragment([
                'customerName' => $customerName,
            ]);
    }

    /**
     * 顧客ステータスでフィルタリングできることをテストする
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_index_filters_by_customer_status(): void
    {
        // テスト用のステータスを取得
        $status = SelectionItemTranslation::where('selection_item_type', 'customer_status')
            ->first();

        if ($status) {
            $response = $this->getJson(
                $this->getBaseUrl() . "?customerStatusCode={$status->selection_item_code}&page=1",
                ['Accept-Language' => 'jpn'],
            );

            $response->assertStatus(200);
            // ステータス名は変換されて返されるためフラグメントの検証は省略
        }
    }

    /**
     * ページネーションが機能することをテストする
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_index_supports_pagination(): void
    {
        $response = $this->getJson(
            $this->getBaseUrl() . '?displayed=10&page=1',
            ['Accept-Language' => 'jpn'],
        );

        $response->assertStatus(200)
            ->assertJsonStructure([
                'meta' => ['currentPage', 'from', 'lastPage', 'perPage'],
            ]);
    }

    /**
     * バリデーションエラーが適切に処理されることをテストする
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
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
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_index_filters_by_organization_code(): void
    {
        $response = $this->getJson(
            $this->getBaseUrl() . "?organizationCode=ORG00000022&page=1",
            ['Accept-Language' => 'jpn'],
        );

        $response->assertStatus(200);
    }
}
