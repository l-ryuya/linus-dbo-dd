<?php

declare(strict_types=1);

namespace Tests\Unit\Services\AiDd\PreDd;

use App\Enums\Dd\DdEntityTypeCode;
use App\Enums\Dd\DdRelationCode;
use App\Enums\Dd\DdStatusCode;
use App\Enums\Dd\DdStepCode;
use App\Models\CompanyNameTranslation;
use App\Models\Customer;
use App\Models\DdCase;
use App\Models\DdCompany;
use App\Models\DdEntity;
use App\Models\DdRelation;
use App\Models\DdStep;
use App\Models\Tenant;
use App\Models\UserOption;
use App\Services\AiDd\PreDd\Step0Service;
use Database\Seeders\Base\CompaniesSeeder;
use Database\Seeders\Base\CountryRegionsSeeder;
use Database\Seeders\Base\SelectionItemsSeeder;
use Database\Seeders\Base\ServicePlansSeeder;
use Database\Seeders\Base\ServicesSeeder;
use Database\Seeders\Base\TenantsSeeder;
use Database\Seeders\Base\TimeZonesSeeder;
use Database\Seeders\Base\UserOptionsSeeder;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Step0ServiceTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private Customer $customer;

    private UserOption $caseUserOption;

    private Step0Service $step0Service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            TimeZonesSeeder::class,
            SelectionItemsSeeder::class,
            CountryRegionsSeeder::class,
            TenantsSeeder::class,
            CompaniesSeeder::class,
            ServicesSeeder::class,
            ServicePlansSeeder::class,
            UserOptionsSeeder::class,
            TestDatabaseSeeder::class,
        ]);

        $authUser = $this->createServiceManageUser();
        $this->tenant = $authUser->getUserOption()->tenant;

        // テスト用の認証を設定
        $this->actingAs($authUser);

        // テスト用のデータを準備
        $this->prepareTestData();

        // Step0Serviceのインスタンスを作成
        $this->step0Service = new Step0Service();
    }

    /**
     * テスト用のデータを準備
     */
    private function prepareTestData(): void
    {
        $this->customer = Customer::where('tenant_id', $this->tenant->tenant_id)->first();
        $this->caseUserOption = UserOption::where('tenant_id', $this->tenant->tenant_id)->first();

        // 顧客の会社に名前翻訳データを確実に設定
        $company = $this->customer->company;
        if (!$company->nameTranslation($company->default_language_code)) {
            CompanyNameTranslation::create([
                'company_id' => $company->company_id,
                'language_code' => $company->default_language_code,
                'company_legal_name' => 'Test Company Legal Name',
                'company_trade_name' => 'Test Company Trade Name',
            ]);
        }
    }

    /**
     * 初期データ作成が成功することをテストする
     */
    public function test_create_initial_data_successfully(): void
    {
        // 実際のcreateInitialDataを実行
        $ddCaseNo = $this->step0Service->createInitialData(
            $this->tenant->tenant_id,
            $this->customer->customer_id,
            $this->caseUserOption->user_option_id,
        );

        // 戻り値がケース番号として返されることを確認
        $this->assertNotEmpty($ddCaseNo);

        // DdCaseが正しく作成されていることを確認
        $this->assertDatabaseHas('dd_cases', [
            'dd_case_no' => $ddCaseNo,
            'tenant_id' => $this->tenant->tenant_id,
            'customer_id' => $this->customer->customer_id,
            'case_user_option_id' => $this->caseUserOption->user_option_id,
            'current_dd_step_type' => 'dd_step',
            'current_dd_step_code' => DdStepCode::PreDdAi->value,
            'current_dd_status_type' => 'dd_status',
            'current_dd_status_code' => DdStatusCode::PreDdAiStarted->value,
        ]);

        // 作成されたDdCaseを取得
        $ddCase = DdCase::where('dd_case_no', $ddCaseNo)->first();
        $this->assertNotNull($ddCase);
        $this->assertNotNull($ddCase->started_at);

        // DdStepが正しく作成されていることを確認
        $this->assertDatabaseHas('dd_steps', [
            'tenant_id' => $this->tenant->tenant_id,
            'dd_case_id' => $ddCase->dd_case_id,
            'dd_step_type' => 'dd_step',
            'dd_step_code' => DdStepCode::PreDdAi->value,
        ]);

        $expectedCompanyName = $this->customer
            ->company
            ->nameTranslation($this->customer->company->default_language_code)
            ->company_legal_name;

        // DdEntityが正しく作成されていることを確認
        $this->assertDatabaseHas('dd_entities', [
            'tenant_id' => $this->tenant->tenant_id,
            'dd_entity_name' => $expectedCompanyName,
            'dd_entity_type_type' => 'dd_entity_type',
            'dd_entity_type_code' => DdEntityTypeCode::Company->value,
        ]);

        // 作成されたDdRelationを取得
        $ddRelation = DdRelation::where('dd_case_id', $ddCase->dd_case_id)
            ->where('dd_relation_code', DdRelationCode::CounterpartyEntity->value)
            ->first();
        $this->assertNotNull($ddRelation);

        // DdCompanyが正しく作成されていることを確認
        $this->assertDatabaseHas('dd_companies', [
            'tenant_id' => $this->tenant->tenant_id,
            'dd_entity_id' => $ddRelation->dd_entity_id,
            'company_name' => $expectedCompanyName,
        ]);

        // DdRelationが正しく作成されていることを確認
        $this->assertDatabaseHas('dd_relations', [
            'tenant_id' => $this->tenant->tenant_id,
            'dd_case_id' => $ddCase->dd_case_id,
            'dd_entity_id' => $ddRelation->dd_entity_id,
            'dd_relation_type' => 'dd_relation',
            'dd_relation_code' => 'counterparty_entity',
            'dd_relation_status' => 'CREATE',
            'is_confirmed' => true,
        ]);
    }

    /**
     * 存在しない顧客IDを指定した場合のテスト
     */
    public function test_create_initial_data_throws_exception_for_nonexistent_customer(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->step0Service->createInitialData(
            $this->tenant->tenant_id,
            99999, // 存在しない顧客ID
            $this->caseUserOption->user_option_id,
        );
    }

    /**
     * データベーストランザクションのロールバックテスト
     */
    public function test_create_initial_data_rolls_back_on_exception(): void
    {
        // 存在しない顧客IDを使用して例外を発生させる
        $nonExistentCustomerId = 99999;

        // 例外が発生することを確認
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        try {
            $this->step0Service->createInitialData(
                $this->tenant->tenant_id,
                $nonExistentCustomerId,
                $this->caseUserOption->user_option_id,
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // ロールバックされて、データが作成されていないことを確認
            $this->assertDatabaseMissing('dd_cases', [
                'tenant_id' => $this->tenant->tenant_id,
                'customer_id' => $nonExistentCustomerId,
                'case_user_option_id' => $this->caseUserOption->user_option_id,
            ]);

            throw $e;
        }
    }

    /**
     * 複数の初期データ作成でそれぞれ独立したデータが作成されることをテスト
     */
    public function test_create_initial_data_creates_independent_data(): void
    {
        // 1回目の実行
        $ddCaseNo1 = $this->step0Service->createInitialData(
            $this->tenant->tenant_id,
            $this->customer->customer_id,
            $this->caseUserOption->user_option_id,
        );

        // 2回目の実行
        $ddCaseNo2 = $this->step0Service->createInitialData(
            $this->tenant->tenant_id,
            $this->customer->customer_id,
            $this->caseUserOption->user_option_id,
        );

        // 異なるケース番号が生成されることを確認
        $this->assertNotEquals($ddCaseNo1, $ddCaseNo2);

        // 両方のケースがデータベースに存在することを確認
        $this->assertDatabaseHas('dd_cases', ['dd_case_no' => $ddCaseNo1]);
        $this->assertDatabaseHas('dd_cases', ['dd_case_no' => $ddCaseNo2]);

        // 各ケースに対応するステップが作成されていることを確認
        $ddCase1 = DdCase::where('dd_case_no', $ddCaseNo1)->first();
        $ddCase2 = DdCase::where('dd_case_no', $ddCaseNo2)->first();

        $this->assertDatabaseHas('dd_steps', ['dd_case_id' => $ddCase1->dd_case_id]);
        $this->assertDatabaseHas('dd_steps', ['dd_case_id' => $ddCase2->dd_case_id]);
    }

    /**
     * 作成されたデータの関連性を確認するテスト
     */
    public function test_create_initial_data_maintains_proper_relationships(): void
    {
        $ddCaseNo = $this->step0Service->createInitialData(
            $this->tenant->tenant_id,
            $this->customer->customer_id,
            $this->caseUserOption->user_option_id,
        );

        // 作成されたデータを取得
        $ddCase = DdCase::where('dd_case_no', $ddCaseNo)->first();
        $ddStep = DdStep::where('dd_case_id', $ddCase->dd_case_id)->first();
        $ddRelation = DdRelation::where('dd_case_id', $ddCase->dd_case_id)
            ->where('dd_relation_code', DdRelationCode::CounterpartyEntity->value)
            ->first();
        $ddEntity = DdEntity::where('dd_entity_id', $ddRelation->dd_entity_id)
            ->where('dd_entity_type_code', DdEntityTypeCode::Company->value)
            ->latest()
            ->first();
        $ddCompany = DdCompany::where('dd_entity_id', $ddRelation->dd_entity_id)->first();

        // 関連性の確認
        $this->assertEquals($ddCase->dd_case_id, $ddStep->dd_case_id);
        $this->assertEquals($ddCase->dd_case_id, $ddRelation->dd_case_id);
        $this->assertEquals($ddEntity->dd_entity_id, $ddCompany->dd_entity_id);
        $this->assertEquals($this->tenant->tenant_id, $ddCase->tenant_id);
        $this->assertEquals($this->tenant->tenant_id, $ddStep->tenant_id);
        $this->assertEquals($this->tenant->tenant_id, $ddEntity->tenant_id);
        $this->assertEquals($this->tenant->tenant_id, $ddCompany->tenant_id);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
