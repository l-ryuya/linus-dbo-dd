<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\DueDiligence;
use App\Models\ServiceContract;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\tests\DueDiligencesSeeder;
use Database\Seeders\tests\UsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ServiceContractsControllerTest extends TestCase
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
        return '/v1/admin/service-contracts';
    }

    /**
     * サービス契約一覧APIが正常なレスポンスを返すことをテストする
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
                        'companyStatus',
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

    /**
     * サービス契約詳細APIが正常なレスポンスを返すことをテストする
     */
    public function test_show_returns_successful_response(): void
    {
        $this->seed(UsersSeeder::class);
        $this->seed(DueDiligencesSeeder::class);

        $dd = DueDiligence::where('dd_entity_type_code', 'target_company')
            ->where('company_name', '株式会社FINOLAB')
            ->first();
        $company = Company::firstWhere('company_code', 'C-000003');
        $company->latest_dd_id = $dd->dd_id;
        $company->save();

        ServiceContract::factory()->create([
            'company_id' => $company->company_id,
            'responsible_user_id' => User::firstWhere('user_code', 'U-900001')->user_id,
            'contract_manager_user_id' => User::firstWhere('user_code', 'U-900002')->user_id,
        ]);

        $this->authenticateAsAdmin();

        // テストデータベースに存在する会社コードを使用
        $companyCode = 'C-000003';
        $response = $this->getJson($this->getBaseUrl() . '/' . $companyCode);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'companyCode',
                    'companyName',
                    'companyStatus',
                    'postalCode',
                    'prefecture',
                    'city',
                    'street',
                    'buildingRoom',
                    'latestDdId',
                    'ddStatus',
                    'serviceContracts' => [
                        '*' => [
                            'serviceContractCode',
                            'serviceCode',
                            'serviceName',
                            'servicePlanCode',
                            'servicePlanName',
                            'departmentName',
                            'serviceUsageStatus',
                            'serviceContractStatus',
                            'personInCharge' => [
                                'lastName',
                                'middleName',
                                'firstName',
                                'position',
                                'email',
                            ],
                            'contractManager' => [
                                'lastName',
                                'middleName',
                                'firstName',
                                'position',
                                'email',
                            ],
                        ],
                    ],
                ],
            ]);
    }

    /**
     * 認証されていない場合にサービス契約詳細へのアクセスが拒否されることをテストする
     */
    public function test_show_denies_access_without_authentication(): void
    {
        $companyCode = 'C-000001';
        $response = $this->getJson($this->getBaseUrl() . '/' . $companyCode);
        $response->assertStatus(401);
    }

    /**
     * 存在しない会社コードの場合に404が返されることをテストする
     */
    public function test_show_returns_404_for_nonexistent_company(): void
    {
        $this->authenticateAsAdmin();

        $nonExistentCompanyCode = 'NON-EXISTENT';
        $response = $this->getJson($this->getBaseUrl() . '/' . $nonExistentCompanyCode);

        $response->assertStatus(404);
    }

}
