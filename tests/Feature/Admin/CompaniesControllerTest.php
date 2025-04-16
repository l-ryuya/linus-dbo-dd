<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CompaniesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    /**
     * 管理者ユーザーとして認証を行う
     *
     * @return void
     */
    private function authenticateAsAdmin(): void
    {
        $admin = User::where('user_code', 'SYS-000001')->first();
        Sanctum::actingAs($admin, ['service_manager']);
    }

    /**
     * APIエンドポイントのベースURLを取得
     *
     * @return string
     */
    private function getBaseUrl(): string
    {
        return '/v1/admin/companies';
    }

    /**
     * 企業一覧APIが正常なレスポンスを返すことをテストする
     */
    public function test_index_returns_successful_response(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->getJson($this->getBaseUrl() . '?page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'companyCode',
                        'companyName',
                        'companyStatusType',
                        'companyStatusCode',
                        'signupDate',
                        'activationDate',
                        'serviceContracts' => [
                            '*' => [
                                'serviceContractCode',
                                'serviceCode',
                                'serviceName',
                                'servicePlanCode',
                                'servicePlanName',
                            ],
                        ],
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    /**
     * 認証されていない場合にアクセスが拒否されることをテストする
     */
    public function test_index_denies_access_without_authentication(): void
    {
        $response = $this->getJson($this->getBaseUrl());
        $response->assertStatus(401);
    }

    /**
     * クエリパラメータでフィルタリングできることをテストする
     */
    public function test_index_filters_by_company_name(): void
    {
        $this->authenticateAsAdmin();

        // 会社名でフィルタリング
        $companyName = 'DENTSU SOKEN Inc.';
        $response = $this->getJson($this->getBaseUrl() . '?companyName=' . urlencode($companyName) . '&page=1');

        $response->assertStatus(200);
    }

    /**
     * 会社ステータスコードでフィルタリングできることをテストする
     */
    public function test_index_filters_by_company_status_code(): void
    {
        $this->authenticateAsAdmin();

        // テストデータベースに存在するステータスコードを仮定
        $statusCode = 'Active';
        $response = $this->getJson($this->getBaseUrl() . '?companyStatusCode=' . $statusCode . '&page=1');

        $response->assertStatus(200);
    }

    /**
     * サービス登録日範囲でフィルタリングできることをテストする
     */
    public function test_index_filters_by_service_signup_date_range(): void
    {
        $this->authenticateAsAdmin();

        $startDate = Carbon::now()->subMonth()->toDateString();
        $endDate = Carbon::now()->addYear()->toDateString();

        $response = $this->getJson(
            $this->getBaseUrl() .
            '?serviceSignupStartDate=' . $startDate .
            '&serviceSignupEndDate=' . $endDate .
            '&page=1',
        );

        $response->assertStatus(200);
    }

    /**
     * ページネーションが機能することをテストする
     */
    public function test_index_supports_pagination(): void
    {
        $this->authenticateAsAdmin();

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
        $this->authenticateAsAdmin();

        // 不正な日付範囲（開始日が終了日より後）
        $response = $this->getJson(
            $this->getBaseUrl() .
            '?serviceSignupStartDate=2023-12-31&serviceSignupEndDate=2023-01-01',
        );

        $response->assertStatus(422);
    }
}
