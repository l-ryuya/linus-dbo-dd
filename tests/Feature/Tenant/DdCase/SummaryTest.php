<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\DdCase;

use App\Models\DdCase;
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

class SummaryTest extends TestCase
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
     * デューデリジェンスケースサマリーAPIが正常なレスポンスを返すことをテストする
     */
    public function test_summary_returns_successful_response(): void
    {
        // テスト用のDdCaseデータを取得
        $ddCase = DdCase::first();

        $response = $this->getJson($this->getBaseUrl() . '/' . $ddCase->public_id . '/summary');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'ddStep' => [
                        'current' => [
                            'ddStep',
                            'ddStepCode',
                            'stepComment',
                            'stepCompletedAt',
                            'stepUserName',
                        ],
                        'steps' => [
                            '*' => [
                                'stepNumber',
                                'stepName',
                                'ddStepCode',
                                'stepInfo',
                            ],
                        ],
                    ],
                    'ddCase' => [
                        'ddCasePublicId',
                        'tenantName',
                        'ddCaseNo',
                        'startedAt',
                        'endedAt',
                        'caseUserName',
                        'currentDdStep',
                        'currentDdStepCode',
                        'currentDdStatus',
                        'currentDdStatusCode',
                        'overallResult',
                        'industryCheckRegResult',
                        'industryCheckWebResult',
                        'customerRiskLevel',
                        'asfCheckResult',
                        'repCheckResult',
                        'lastProcessUserName',
                        'lastProcessDatetime',
                    ],
                    'targetCompany' => [
                        'ddRelationPublicId',
                        'companyName',
                        'industryCheckReg',
                        'industryCheckWeb',
                        'customerRiskLevel',
                        'asfCheckResult',
                        'repCheckResult',
                        'exchangeName',
                        'securitiesCode',
                    ],
                    'executives' => [
                        '*' => [
                            'ddRelationPublicId',
                            'executiveName',
                            'position',
                            'asfCheckResult',
                            'repCheckResult',
                        ],
                    ],
                    'directShareholders' => [
                        '*' => [
                            'ddRelationPublicId',
                            'shareholderName',
                            'shareholdingRatio',
                            'industryCheckReg',
                            'industryCheckWeb',
                            'asfCheckResult',
                            'repCheckResult',
                            'exchangeName',
                            'securitiesCode',
                        ],
                    ],
                ],
            ]);
    }

    /**
     * 存在しないデューデリジェンスケースIDを指定した場合に404エラーが返されることをテストする
     */
    public function test_summary_returns_404_for_non_existent_dd_case(): void
    {
        $response = $this->getJson($this->getBaseUrl() . '/non-existent-dd-case-id/summary');

        $response->assertStatus(404);
    }

    /**
     * 権限のないユーザーがアクセスした場合のテスト
     */
    public function test_summary_requires_authentication(): void
    {
        // 認証なしでアクセス
        $this->app['auth']->forgetGuards();

        $ddCase = DdCase::first();
        $response = $this->getJson($this->getBaseUrl() . '/' . $ddCase->public_id . '/summary');

        $response->assertStatus(401);
    }

    /**
     * テナント権限でアクセスできることをテストする
     */
    public function test_summary_accessible_by_tenant_role(): void
    {
        // テナント権限のユーザーでテスト
        $this->actingAs($this->createTenantManageUser());

        $ddCase = DdCase::first();
        $response = $this->getJson($this->getBaseUrl() . '/' . $ddCase->public_id . '/summary');

        $response->assertStatus(200);
    }

    /**
     * 管理者権限でアクセスできることをテストする
     */
    public function test_summary_accessible_by_admin_role(): void
    {
        // 管理者権限のユーザーでテスト
        $this->actingAs($this->createServiceManageUser());

        $ddCase = DdCase::first();
        $response = $this->getJson($this->getBaseUrl() . '/' . $ddCase->public_id . '/summary');

        $response->assertStatus(200);
    }

    /**
     * レスポンスデータの型と構造をテストする
     */
    public function test_summary_response_data_types(): void
    {
        $ddCase = DdCase::first();
        $response = $this->getJson($this->getBaseUrl() . '/' . $ddCase->public_id . '/summary');

        $response->assertStatus(200);

        $data = $response->json('data');

        // ddCaseの必須フィールドの存在確認
        $this->assertArrayHasKey('ddCase', $data);
        $this->assertArrayHasKey('ddCasePublicId', $data['ddCase']);
        $this->assertArrayHasKey('ddCaseNo', $data['ddCase']);

        // targetCompanyの必須フィールドの存在確認
        $this->assertArrayHasKey('targetCompany', $data);
        $this->assertArrayHasKey('companyName', $data['targetCompany']);

        // ddStepの構造確認
        $this->assertArrayHasKey('ddStep', $data);
        $this->assertArrayHasKey('current', $data['ddStep']);
        $this->assertArrayHasKey('steps', $data['ddStep']);
        $this->assertIsArray($data['ddStep']['steps']);

        // executives配列の存在確認
        $this->assertArrayHasKey('executives', $data);
        $this->assertIsArray($data['executives']);

        // directShareholders配列の存在確認
        $this->assertArrayHasKey('directShareholders', $data);
        $this->assertIsArray($data['directShareholders']);
    }
}
