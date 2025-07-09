<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\ServiceContract;

use App\Models\Customer;
use App\Models\Service;
use App\Models\ServicePlan;
use App\Models\Tenant;
use App\Models\UserOption;
use Database\Seeders\base\CompaniesSeeder;
use Database\Seeders\base\CountryRegionsSeeder;
use Database\Seeders\base\CustomersSeeder;
use Database\Seeders\base\SelectionItemsSeeder;
use Database\Seeders\base\ServicePlansSeeder;
use Database\Seeders\base\ServicesSeeder;
use Database\Seeders\base\TenantsSeeder;
use Database\Seeders\base\TimeZonesSeeder;
use Database\Seeders\base\UserOptionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private Customer $customer;

    private Service $service;

    private ServicePlan $servicePlan;

    private UserOption $serviceRepUserOption;

    private UserOption $serviceMgrUserOption;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            TimeZonesSeeder::class,
            SelectionItemsSeeder::class,
            CountryRegionsSeeder::class,
            TenantsSeeder::class,
            CompaniesSeeder::class,
            CustomersSeeder::class,
            ServicesSeeder::class,
            ServicePlansSeeder::class,
            UserOptionsSeeder::class,
        ]);

        $authUser = $this->createServiceManageUser();
        $this->tenant = $authUser->getUserOption()->tenant;

        // テスト用の認証を設定
        $this->actingAs($authUser);

        // テスト用データの設定
        $this->customer = Customer::where('tenant_id', $this->tenant->tenant_id)->first();
        $this->service = Service::where('tenant_id', $this->tenant->tenant_id)
            ->where('service_code', 'Securate')
            ->first();
        $this->servicePlan = ServicePlan::where('service_id', $this->service->service_id)->first();

        // サービス担当者と管理者の設定
        $userOption = UserOption::where('tenant_id', $this->tenant->tenant_id)
            ->where('service_id', $this->service->service_id)
            ->first();

        $this->serviceRepUserOption = $userOption;
        $this->serviceMgrUserOption = $userOption;
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
     * サービス契約登録APIが成功すること（正常系）をテストする
     */
    public function test_store_creates_service_contract_successfully(): void
    {
        $contractData = [
            'servicePublicId' => $this->service->public_id,
            'servicePlanPublicId' => $this->servicePlan->public_id,
            'customerPublicId' => $this->customer->public_id,
            'contractName' => 'テスト契約',
            'contractLanguage' => 'jpn',
            'contractStatusCode' => 'contract_info_registered',
            'serviceUsageStatusCode' => 'awaiting_activation',
            'contractDate' => '2025-01-01',
            'contractStartDate' => '2025-01-15',
            'contractEndDate' => '2025-12-31',
            'contractAutoUpdate' => true,
            'customerContactUserName' => 'テスト顧客担当者',
            'customerContactUserDept' => '営業部',
            'customerContactUserTitle' => '部長',
            'customerContactUserMail' => 'contact@example.com',
            'customerContractUserName' => 'テスト契約担当者',
            'customerContractUserDept' => '管理部',
            'customerContractUserTitle' => '課長',
            'customerContractUserMail' => 'contract@example.com',
            'customerPaymentUserName' => 'テスト支払担当者',
            'customerPaymentUserDept' => '経理部',
            'customerPaymentUserTitle' => '担当',
            'customerPaymentUserMail' => 'payment@example.com',
            'serviceRepUserOptionPublicId' => $this->serviceRepUserOption->public_id,
            'serviceMgrUserOptionPublicId' => $this->serviceMgrUserOption->public_id,
            'billingCycleCode' => 'monthly',
            'invoiceRemindDays' => '7,14,30',
            'remarks' => 'テスト契約の備考',
        ];

        $response = $this->postJson(
            $this->getBaseUrl(),
            $contractData,
        );

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'serviceContractPublicId',
                ],
            ]);

        // データベースにレコードが作成されたことを確認
        $responseData = $response->json('data');

        $this->assertDatabaseHas('service_contracts', [
            'public_id' => $responseData['serviceContractPublicId'],
            'tenant_id' => $this->tenant->tenant_id,
            'customer_id' => $this->customer->customer_id,
            'service_id' => $this->service->service_id,
            'service_plan_id' => $this->servicePlan->service_plan_id,
            'contract_name' => $contractData['contractName'],
            'contract_language' => $contractData['contractLanguage'],
            'contract_status_type' => 'service_contract_status',
            'contract_status_code' => $contractData['contractStatusCode'],
            'service_usage_status_type' => 'service_usage_status',
            'service_usage_status_code' => $contractData['serviceUsageStatusCode'],
            'contract_date' => $contractData['contractDate'],
            'contract_start_date' => $contractData['contractStartDate'],
            'contract_end_date' => $contractData['contractEndDate'],
            'contract_auto_update' => $contractData['contractAutoUpdate'],
            'customer_contact_user_name' => $contractData['customerContactUserName'],
            'customer_contact_user_dept' => $contractData['customerContactUserDept'],
            'customer_contact_user_title' => $contractData['customerContactUserTitle'],
            'customer_contact_user_mail' => $contractData['customerContactUserMail'],
            'customer_contract_user_name' => $contractData['customerContractUserName'],
            'customer_contract_user_dept' => $contractData['customerContractUserDept'],
            'customer_contract_user_title' => $contractData['customerContractUserTitle'],
            'customer_contract_user_mail' => $contractData['customerContractUserMail'],
            'customer_payment_user_name' => $contractData['customerPaymentUserName'],
            'customer_payment_user_dept' => $contractData['customerPaymentUserDept'],
            'customer_payment_user_title' => $contractData['customerPaymentUserTitle'],
            'customer_payment_user_mail' => $contractData['customerPaymentUserMail'],
            'service_rep_user_option_id' => $this->serviceRepUserOption->user_option_id,
            'service_mgr_user_option_id' => $this->serviceMgrUserOption->user_option_id,
            'billing_cycle_type' => 'billing_cycle',
            'billing_cycle_code' => $contractData['billingCycleCode'],
            'invoice_remind_days' => '{' . $contractData['invoiceRemindDays'] . '}',
            'remarks' => $contractData['remarks'],
        ]);
    }

    /**
     * バリデーションエラーが適切に処理されることをテストする（異常系）
     */
    public function test_store_validates_input_properly(): void
    {
        // 必須項目が欠けているデータ
        $incompleteData = [
            'servicePublicId' => $this->service->public_id,
            'servicePlanPublicId' => $this->servicePlan->public_id,
            // customerPublicId が欠けている
            'contractName' => 'テスト契約',
            'contractLanguage' => 'jpn',
            'contractStatusCode' => 'contract_info_registered',
            'serviceUsageStatusCode' => 'awaiting_activation',
            'contractDate' => '2025-01-01',
            'contractStartDate' => '2025-01-15',
        ];

        $response = $this->postJson(
            $this->getBaseUrl(),
            $incompleteData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customerPublicId']);

        // 存在しないサービスID
        $invalidServiceData = [
            'servicePublicId' => 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee', // 存在しないID
            'servicePlanPublicId' => $this->servicePlan->public_id,
            'customerPublicId' => $this->customer->public_id,
            'contractName' => 'テスト契約',
            'contractLanguage' => 'jpn',
            'contractStatusCode' => 'contract_info_registered',
            'serviceUsageStatusCode' => 'awaiting_activation',
            'contractDate' => '2025-01-01',
            'contractStartDate' => '2025-01-15',
            'contractAutoUpdate' => true,
        ];

        $response = $this->postJson(
            $this->getBaseUrl(),
            $invalidServiceData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['servicePublicId']);

        // 存在しないステータスコード
        $invalidStatusCodeData = [
            'servicePublicId' => $this->service->public_id,
            'servicePlanPublicId' => $this->servicePlan->public_id,
            'customerPublicId' => $this->customer->public_id,
            'contractName' => 'テスト契約',
            'contractLanguage' => 'jpn',
            'contractStatusCode' => 'invalid_status_code', // 存在しないステータスコード
            'serviceUsageStatusCode' => 'awaiting_activation',
            'contractDate' => '2025-01-01',
            'contractStartDate' => '2025-01-15',
            'contractAutoUpdate' => true,
        ];

        $response = $this->postJson(
            $this->getBaseUrl(),
            $invalidStatusCodeData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['contractStatusCode']);

        // 不正な日付形式
        $invalidDateData = [
            'servicePublicId' => $this->service->public_id,
            'servicePlanPublicId' => $this->servicePlan->public_id,
            'customerPublicId' => $this->customer->public_id,
            'contractName' => 'テスト契約',
            'contractLanguage' => 'jpn',
            'contractStatusCode' => 'contract_info_registered',
            'serviceUsageStatusCode' => 'awaiting_activation',
            'contractDate' => '2025/01/01', // スラッシュを使用した不正な形式
            'contractStartDate' => '2025-01-15',
            'contractAutoUpdate' => true,
        ];

        $response = $this->postJson(
            $this->getBaseUrl(),
            $invalidDateData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['contractDate']);

        // 不正なメールアドレス
        $invalidEmailData = [
            'servicePublicId' => $this->service->public_id,
            'servicePlanPublicId' => $this->servicePlan->public_id,
            'customerPublicId' => $this->customer->public_id,
            'contractName' => 'テスト契約',
            'contractLanguage' => 'jpn',
            'contractStatusCode' => 'contract_info_registered',
            'serviceUsageStatusCode' => 'awaiting_activation',
            'contractDate' => '2025-01-01',
            'contractStartDate' => '2025-01-15',
            'contractAutoUpdate' => true,
            'customerContactUserName' => 'テスト顧客担当者',
            'customerContactUserMail' => 'invalid-email', // 不正なメールアドレス
        ];

        $response = $this->postJson(
            $this->getBaseUrl(),
            $invalidEmailData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customerContactUserMail']);
    }

    /**
     * 長すぎる値が適切に処理されることをテストする
     */
    public function test_store_validates_input_length(): void
    {
        // 長すぎる値を含むデータ
        $tooLongData = [
            'servicePublicId' => $this->service->public_id,
            'servicePlanPublicId' => $this->servicePlan->public_id,
            'customerPublicId' => $this->customer->public_id,
            'contractName' => str_repeat('あ', 256), // 255文字以上
            'contractLanguage' => 'jpn',
            'contractStatusCode' => 'contract_info_registered',
            'serviceUsageStatusCode' => 'awaiting_activation',
            'contractDate' => '2025-01-01',
            'contractStartDate' => '2025-01-15',
            'contractAutoUpdate' => true,
            'customerContactUserName' => 'テスト顧客担当者',
            'customerContactUserMail' => 'contact@example.com',
            'remarks' => str_repeat('a', 256), // 255文字以上
        ];

        $response = $this->postJson(
            $this->getBaseUrl(),
            $tooLongData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['contractName', 'remarks']);
    }

    /**
     * オプションフィールドが正しく処理されることをテストする
     */
    public function test_store_handles_optional_fields_correctly(): void
    {
        // オプションフィールドを含めないデータ
        $dataWithoutOptionalFields = [
            'servicePublicId' => $this->service->public_id,
            'servicePlanPublicId' => $this->servicePlan->public_id,
            'customerPublicId' => $this->customer->public_id,
            'contractName' => 'テスト契約',
            'contractLanguage' => 'jpn',
            'contractStatusCode' => 'contract_info_registered',
            'serviceUsageStatusCode' => 'awaiting_activation',
            'contractDate' => '2025-01-01',
            'contractStartDate' => '2025-01-15',
            'contractAutoUpdate' => true,
            'customerContactUserName' => 'テスト顧客担当者',
            'customerContactUserMail' => 'contact@example.com',
            'customerContractUserName' => 'テスト契約担当者',
            'customerContractUserMail' => 'contract@example.com',
            'customerPaymentUserName' => 'テスト支払担当者',
            'customerPaymentUserMail' => 'payment@example.com',
            'serviceRepUserOptionPublicId' => $this->serviceRepUserOption->public_id,
            'serviceMgrUserOptionPublicId' => $this->serviceMgrUserOption->public_id,
            // 以下のフィールドは省略
            // 'contractEndDate', 'customerContactUserDept', 'customerContactUserTitle', etc.
        ];

        $response = $this->postJson(
            $this->getBaseUrl(),
            $dataWithoutOptionalFields,
        );

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'serviceContractPublicId',
                ],
            ]);
    }

    /**
     * 請求サイクルコードのデフォルト値（monthly）が正しく設定されることをテストする
     */
    public function test_store_sets_default_billing_cycle_code(): void
    {
        $dataWithoutBillingCycleCode = [
            'servicePublicId' => $this->service->public_id,
            'servicePlanPublicId' => $this->servicePlan->public_id,
            'customerPublicId' => $this->customer->public_id,
            'contractName' => 'テスト契約',
            'contractLanguage' => 'jpn',
            'contractStatusCode' => 'contract_info_registered',
            'serviceUsageStatusCode' => 'awaiting_activation',
            'contractDate' => '2025-01-01',
            'contractStartDate' => '2025-01-15',
            'contractAutoUpdate' => true,
            'customerContactUserName' => 'テスト顧客担当者',
            'customerContactUserMail' => 'contact@example.com',
            'customerContractUserName' => 'テスト契約担当者',
            'customerContractUserMail' => 'contract@example.com',
            'customerPaymentUserName' => 'テスト支払担当者',
            'customerPaymentUserMail' => 'payment@example.com',
            'serviceRepUserOptionPublicId' => $this->serviceRepUserOption->public_id,
            'serviceMgrUserOptionPublicId' => $this->serviceMgrUserOption->public_id,
            // billingCycleCodeは省略
        ];

        $response = $this->postJson(
            $this->getBaseUrl(),
            $dataWithoutBillingCycleCode,
        );

        $response->assertStatus(201);

        $responseData = $response->json('data');

        // デフォルト値のmonthlyが設定されていることを確認
        $this->assertDatabaseHas('service_contracts', [
            'public_id' => $responseData['serviceContractPublicId'],
            'billing_cycle_code' => 'monthly',
        ]);
    }
}
