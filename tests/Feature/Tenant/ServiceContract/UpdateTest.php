<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\ServiceContract;

use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceContract;
use App\Models\ServicePlan;
use App\Models\Tenant;
use App\Models\UserOption;
use Database\Seeders\base\CompaniesSeeder;
use Database\Seeders\base\CountryRegionsSeeder;
use Database\Seeders\base\CustomersSeeder;
use Database\Seeders\base\SelectionItemsSeeder;
use Database\Seeders\base\ServiceContractsSeeder;
use Database\Seeders\base\ServicePlansSeeder;
use Database\Seeders\base\ServicesSeeder;
use Database\Seeders\base\TenantsSeeder;
use Database\Seeders\base\TimeZonesSeeder;
use Database\Seeders\base\UserOptionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private Customer $customer;

    private Service $service;

    private ServicePlan $servicePlan;

    private UserOption $serviceRepUserOption;

    private UserOption $serviceMgrUserOption;

    private string $publicId;

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
            ServiceContractsSeeder::class,
        ]);

        $authUser = $this->createServiceManageUser();
        $this->tenant = $authUser->getUserOption()->tenant;

        // テスト用の認証を設定
        $this->actingAs($authUser);

        // テスト用のサービス契約データを作成
        $this->createTestServiceContract();
    }

    /**
     * テスト用のサービス契約データを作成
     */
    private function createTestServiceContract(): void
    {
        $this->customer = Customer::where('tenant_id', $this->tenant->tenant_id)->first();
        $this->service = Service::where('tenant_id', $this->tenant->tenant_id)->first();
        $this->servicePlan = ServicePlan::where('service_id', $this->service->service_id)->first();
        $this->serviceRepUserOption = UserOption::where('tenant_id', $this->tenant->tenant_id)
            ->where('service_id', $this->service->service_id)
            ->first();
        $this->serviceMgrUserOption = UserOption::where('tenant_id', $this->tenant->tenant_id)
            ->where('service_id', $this->service->service_id)
            ->first();

        $this->publicId = Str::uuid()->toString();
        ServiceContract::create([
            'public_id' => $this->publicId,
            'tenant_id' => $this->tenant->tenant_id,
            'customer_id' => $this->customer->customer_id,
            'service_id' => $this->service->service_id,
            'service_plan_id' => $this->servicePlan->service_plan_id,
            'contract_name' => 'Original Contract',
            'contract_language' => 'jpn',
            'contract_status_type' => 'service_contract_status',
            'contract_status_code' => 'contract_info_registered',
            'service_usage_status_type' => 'service_usage_status',
            'service_usage_status_code' => 'awaiting_activation',
            'contract_date' => '2024-01-01',
            'contract_start_date' => '2024-01-01',
            'contract_end_date' => '2024-12-31',
            'contract_auto_update' => false,
            'customer_contact_user_name' => 'Original Contact User',
            'customer_contact_user_dept' => 'Original Dept',
            'customer_contact_user_title' => 'Original Title',
            'customer_contact_user_mail' => 'contact@original.com',
            'customer_contract_user_name' => 'Original Contract User',
            'customer_contract_user_dept' => 'Original Contract Dept',
            'customer_contract_user_title' => 'Original Contract Title',
            'customer_contract_user_mail' => 'contract@original.com',
            'customer_payment_user_name' => 'Original Payment User',
            'customer_payment_user_dept' => 'Original Payment Dept',
            'customer_payment_user_title' => 'Original Payment Title',
            'customer_payment_user_mail' => 'payment@original.com',
            'service_rep_user_option_id' => $this->serviceRepUserOption->user_option_id,
            'service_mgr_user_option_id' => $this->serviceMgrUserOption->user_option_id,
            'billing_cycle_type' => 'billing_cycle',
            'billing_cycle_code' => 'monthly',
            'invoice_remind_days' => '{7,14,21}',
            'remarks' => 'Original remarks',
        ]);
    }

    /**
     * APIエンドポイントのベースURLを取得
     *
     * @return string
     */
    private function getBaseUrl(): string
    {
        return '/v1/tenant/service-contracts/' . $this->publicId;
    }

    /**
     * サービス契約更新APIが成功すること（正常系）をテストする
     */
    public function test_update_updates_service_contract_successfully(): void
    {
        $updateData = [
            'servicePublicId' => $this->service->public_id,
            'servicePlanPublicId' => $this->servicePlan->public_id,
            'customerPublicId' => $this->customer->public_id,
            'contractName' => 'Updated Contract Name',
            'contractLanguage' => 'eng',
            'contractStatusCode' => 'contract_executed',
            'serviceUsageStatusCode' => 'active',
            'contractDate' => '2024-02-01',
            'contractStartDate' => '2024-02-01',
            'contractEndDate' => '2025-01-31',
            'contractAutoUpdate' => true,
            'customerContactUserName' => 'Updated Contact User',
            'customerContactUserDept' => 'Updated Contact Dept',
            'customerContactUserTitle' => 'Updated Contact Title',
            'customerContactUserMail' => 'updated.contact@example.com',
            'customerContractUserName' => 'Updated Contract User',
            'customerContractUserDept' => 'Updated Contract Dept',
            'customerContractUserTitle' => 'Updated Contract Title',
            'customerContractUserMail' => 'updated.contract@example.com',
            'customerPaymentUserName' => 'Updated Payment User',
            'customerPaymentUserDept' => 'Updated Payment Dept',
            'customerPaymentUserTitle' => 'Updated Payment Title',
            'customerPaymentUserMail' => 'updated.payment@example.com',
            'serviceRepUserOptionPublicId' => $this->serviceRepUserOption->public_id,
            'serviceMgrUserOptionPublicId' => $this->serviceMgrUserOption->public_id,
            'billingCycleCode' => 'quarterly',
            'invoiceRemindDays' => '10,20,30',
            'remarks' => 'Updated remarks',
        ];

        $response = $this->putJson(
            $this->getBaseUrl(),
            $updateData,
        );

        $response->assertStatus(204);

        // データベースのレコードが更新されたことを確認
        $this->assertDatabaseHas('service_contracts', [
            'public_id' => $this->publicId,
            'tenant_id' => $this->tenant->tenant_id,
            'customer_id' => $this->customer->customer_id,
            'service_id' => $this->service->service_id,
            'service_plan_id' => $this->servicePlan->service_plan_id,
            'contract_name' => $updateData['contractName'],
            'contract_language' => $updateData['contractLanguage'],
            'contract_status_code' => $updateData['contractStatusCode'],
            'service_usage_status_code' => $updateData['serviceUsageStatusCode'],
            'contract_date' => $updateData['contractDate'],
            'contract_start_date' => $updateData['contractStartDate'],
            'contract_end_date' => $updateData['contractEndDate'],
            'contract_auto_update' => $updateData['contractAutoUpdate'],
            'customer_contact_user_name' => $updateData['customerContactUserName'],
            'customer_contact_user_mail' => $updateData['customerContactUserMail'],
            'customer_contract_user_name' => $updateData['customerContractUserName'],
            'customer_contract_user_mail' => $updateData['customerContractUserMail'],
            'customer_payment_user_name' => $updateData['customerPaymentUserName'],
            'customer_payment_user_mail' => $updateData['customerPaymentUserMail'],
            'billing_cycle_code' => $updateData['billingCycleCode'],
            'invoice_remind_days' => '{10,20,30}',
            'remarks' => $updateData['remarks'],
        ]);
    }

    /**
     * 更新不可な契約ステータスコードを使用して更新を試みる
     */
    public function test_update_invalid_contract_status_code(): void
    {
        ServiceContract::where('public_id', $this->publicId)
            ->update(['contract_status_code' => 'contract_executed']);

        $incompleteData = [
            'servicePublicId' => $this->service->public_id,
            'servicePlanPublicId' => $this->servicePlan->public_id,
            'customerPublicId' => $this->customer->public_id,
            'contractName' => 'Updated Contract Name',
            'contractLanguage' => 'eng',
            'contractStatusCode' => 'contract_executed',
            'serviceUsageStatusCode' => 'active',
            'contractDate' => '2024-02-01',
            'contractStartDate' => '2024-02-01',
            'contractAutoUpdate' => true,
            'customerContactUserName' => 'Updated Contact User',
            'customerContactUserMail' => 'updated.contact@example.com',
            'customerContractUserName' => 'Updated Contract User',
            'customerContractUserMail' => 'updated.contract@example.com',
            'customerPaymentUserName' => 'Updated Payment User',
            'customerPaymentUserMail' => 'updated.payment@example.com',
            'serviceRepUserOptionPublicId' => $this->serviceRepUserOption->public_id,
            'serviceMgrUserOptionPublicId' => $this->serviceMgrUserOption->public_id,
            'billingCycleCode' => 'quarterly',
            'invoiceRemindDays' => '10,20,30',
        ];

        $response = $this->putJson(
            $this->getBaseUrl(),
            $incompleteData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['contractStatusCode']);
    }

    /**
     * バリデーションエラーが適切に処理されることをテストする（異常系）
     */
    public function test_update_validates_input_properly(): void
    {
        // 必須項目が欠けているデータ
        $incompleteData = [
            'servicePublicId' => $this->service->public_id,
            'servicePlanPublicId' => $this->servicePlan->public_id,
            // customerPublicIdが欠けている
            'contractName' => 'Test Contract',
            'contractLanguage' => 'jpn',
            'contractStatusCode' => 'contract_info_registered',
        ];

        $response = $this->putJson(
            $this->getBaseUrl(),
            $incompleteData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customerPublicId']);

        // 不正なUUID形式
        $invalidUuidData = [
            'servicePublicId' => 'invalid-uuid',
            'servicePlanPublicId' => $this->servicePlan->public_id,
            'customerPublicId' => $this->customer->public_id,
            'contractName' => 'Test Contract',
            'contractLanguage' => 'jpn',
            'contractStatusCode' => 'contract_info_registered',
        ];

        $response = $this->putJson(
            $this->getBaseUrl(),
            $invalidUuidData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['servicePublicId']);

        // 不正なメールアドレス
        $invalidEmailData = [
            'servicePublicId' => $this->service->public_id,
            'servicePlanPublicId' => $this->servicePlan->public_id,
            'customerPublicId' => $this->customer->public_id,
            'contractName' => 'Test Contract',
            'contractLanguage' => 'jpn',
            'contractStatusCode' => 'contract_info_registered',
            'serviceUsageStatusCode' => 'in_use',
            'contractDate' => '2024-01-01',
            'contractStartDate' => '2024-01-01',
            'contractAutoUpdate' => true,
            'customerContactUserName' => 'Contact User',
            'customerContactUserMail' => 'invalid-email',
            'customerContractUserName' => 'Contract User',
            'customerContractUserMail' => 'contract@example.com',
            'customerPaymentUserName' => 'Payment User',
            'customerPaymentUserMail' => 'payment@example.com',
            'serviceRepUserOptionPublicId' => $this->serviceRepUserOption->public_id,
            'serviceMgrUserOptionPublicId' => $this->serviceMgrUserOption->public_id,
        ];

        $response = $this->putJson(
            $this->getBaseUrl(),
            $invalidEmailData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customerContactUserMail']);
    }

    /**
     * 長すぎる値が適切に処理されることをテストする
     */
    public function test_update_validates_input_length(): void
    {
        $tooLongData = [
            'servicePublicId' => $this->service->public_id,
            'servicePlanPublicId' => $this->servicePlan->public_id,
            'customerPublicId' => $this->customer->public_id,
            'contractName' => str_repeat('a', 256), // 255文字以上
            'contractLanguage' => 'jpn',
            'contractStatusCode' => 'contract_info_registered',
            'serviceUsageStatusCode' => 'in_use',
            'contractDate' => '2024-01-01',
            'contractStartDate' => '2024-01-01',
            'contractAutoUpdate' => true,
            'customerContactUserName' => 'Contact User',
            'customerContactUserMail' => 'contact@example.com',
            'customerContractUserName' => 'Contract User',
            'customerContractUserMail' => 'contract@example.com',
            'customerPaymentUserName' => 'Payment User',
            'customerPaymentUserMail' => 'payment@example.com',
            'serviceRepUserOptionPublicId' => $this->serviceRepUserOption->public_id,
            'serviceMgrUserOptionPublicId' => $this->serviceMgrUserOption->public_id,
            'remarks' => str_repeat('a', 256), // 255文字以上
        ];

        $response = $this->putJson(
            $this->getBaseUrl(),
            $tooLongData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['contractName', 'remarks']);
    }

    /**
     * 存在しないサービス契約IDを指定した場合のテスト
     */
    public function test_update_returns_404_for_nonexistent_service_contract(): void
    {
        $nonexistentId = 'e4a9a9d1-29ee-4029-b231-7d112f66ace0';

        $updateData = [
            'servicePublicId' => $this->service->public_id,
            'servicePlanPublicId' => $this->servicePlan->public_id,
            'customerPublicId' => $this->customer->public_id,
            'contractName' => 'Test Contract',
            'contractLanguage' => 'jpn',
            'contractStatusCode' => 'contract_info_registered',
            'serviceUsageStatusCode' => 'active',
            'contractDate' => '2024-01-01',
            'contractStartDate' => '2024-01-01',
            'contractAutoUpdate' => true,
            'customerContactUserName' => 'Contact User',
            'customerContactUserMail' => 'contact@example.com',
            'customerContractUserName' => 'Contract User',
            'customerContractUserMail' => 'contract@example.com',
            'customerPaymentUserName' => 'Payment User',
            'customerPaymentUserMail' => 'payment@example.com',
            'serviceRepUserOptionPublicId' => $this->serviceRepUserOption->public_id,
            'serviceMgrUserOptionPublicId' => $this->serviceMgrUserOption->public_id,
            'billingCycleCode' => 'quarterly',
        ];

        $response = $this->putJson(
            '/v1/tenant/service-contracts/' . $nonexistentId,
            $updateData,
        );

        $response->assertStatus(404);
    }

    /**
     * オプションフィールドが正しく処理されることをテストする
     */
    public function test_update_handles_optional_fields_correctly(): void
    {
        $dataWithoutOptionalFields = [
            'servicePublicId' => $this->service->public_id,
            'servicePlanPublicId' => $this->servicePlan->public_id,
            'customerPublicId' => $this->customer->public_id,
            'contractName' => 'Test Contract',
            'contractLanguage' => 'jpn',
            'contractStatusCode' => 'contract_info_registered',
            'serviceUsageStatusCode' => 'active',
            'contractDate' => '2024-01-01',
            'contractStartDate' => '2024-01-01',
            'contractAutoUpdate' => true,
            'customerContactUserName' => 'Contact User',
            'customerContactUserMail' => 'contact@example.com',
            'customerContractUserName' => 'Contract User',
            'customerContractUserMail' => 'contract@example.com',
            'customerPaymentUserName' => 'Payment User',
            'customerPaymentUserMail' => 'payment@example.com',
            'serviceRepUserOptionPublicId' => $this->serviceRepUserOption->public_id,
            'serviceMgrUserOptionPublicId' => $this->serviceMgrUserOption->public_id,
            'billingCycleCode' => 'quarterly',
            // 以下のオプションフィールドは省略
            // 'contractEndDate', 'invoiceRemindDays', 'remarks'
        ];

        $response = $this->putJson(
            $this->getBaseUrl(),
            $dataWithoutOptionalFields,
        );

        $response->assertStatus(204);

        // データベースで直接nullに更新されていることを確認
        $this->assertDatabaseHas('service_contracts', [
            'public_id' => $this->publicId,
            'contract_end_date' => null,
            'remarks' => null,
        ]);
    }

    /**
     * 請求督促日数のバリデーションテスト
     */
    public function test_update_validates_invoice_remind_days(): void
    {
        // 不正な形式の請求督促日数
        $invalidRemindDaysData = [
            'servicePublicId' => $this->service->public_id,
            'servicePlanPublicId' => $this->servicePlan->public_id,
            'customerPublicId' => $this->customer->public_id,
            'contractName' => 'Test Contract',
            'contractLanguage' => 'jpn',
            'contractStatusCode' => 'contract_info_registered',
            'serviceUsageStatusCode' => 'in_use',
            'contractDate' => '2024-01-01',
            'contractStartDate' => '2024-01-01',
            'contractAutoUpdate' => true,
            'customerContactUserName' => 'Contact User',
            'customerContactUserMail' => 'contact@example.com',
            'customerContractUserName' => 'Contract User',
            'customerContractUserMail' => 'contract@example.com',
            'customerPaymentUserName' => 'Payment User',
            'customerPaymentUserMail' => 'payment@example.com',
            'serviceRepUserOptionPublicId' => $this->serviceRepUserOption->public_id,
            'serviceMgrUserOptionPublicId' => $this->serviceMgrUserOption->public_id,
            'invoiceRemindDays' => 'invalid-format', // 不正な形式
        ];

        $response = $this->putJson(
            $this->getBaseUrl(),
            $invalidRemindDaysData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['invoiceRemindDays']);
    }
}
