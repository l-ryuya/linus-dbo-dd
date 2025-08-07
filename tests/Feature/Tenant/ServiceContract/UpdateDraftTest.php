<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\ServiceContract;

use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceContract;
use App\Models\ServicePlan;
use App\Models\Tenant;
use App\Models\UserOption;
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
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateDraftTest extends TestCase
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
            ServicesSeeder::class,
            ServicePlansSeeder::class,
            UserOptionsSeeder::class,
            TestDatabaseSeeder::class,
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
            'customer_contact_user_email' => 'contact@original.com',
            'customer_contract_user_name' => 'Original Contract User',
            'customer_contract_user_dept' => 'Original Contract Dept',
            'customer_contract_user_title' => 'Original Contract Title',
            'customer_contract_user_email' => 'contract@original.com',
            'customer_payment_user_name' => 'Original Payment User',
            'customer_payment_user_dept' => 'Original Payment Dept',
            'customer_payment_user_title' => 'Original Payment Title',
            'customer_payment_user_email' => 'payment@original.com',
            'service_rep_user_option_id' => $this->serviceRepUserOption->user_option_id,
            'service_mgr_user_option_id' => $this->serviceMgrUserOption->user_option_id,
            'quotationName' => '見積書名称',
            'quotationNumber' => '提案書名称',
            'quotationDate' => '2024-12-01',
            'proposalName' => '提案書名称',
            'proposalNumber' => '提案書番号',
            'proposalDate' => '2024-11-01',
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
        return '/v1/tenant/service-contracts/' . $this->publicId . '/draft';
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
            'serviceUsageStatusCode' => 'awaiting_activation',
            'contractDate' => '2024-02-01',
            'contractStartDate' => '2024-02-01',
            'contractEndDate' => '2025-01-31',
            'contractAutoUpdate' => true,
            'customerContactUserName' => 'Updated Contact User',
            'customerContactUserDept' => 'Updated Contact Dept',
            'customerContactUserTitle' => 'Updated Contact Title',
            'customerContactUserEmail' => 'updated.contact@example.com',
            'customerContractUserName' => 'Updated Contract User',
            'customerContractUserDept' => 'Updated Contract Dept',
            'customerContractUserTitle' => 'Updated Contract Title',
            'customerContractUserEmail' => 'updated.contract@example.com',
            'customerPaymentUserName' => 'Updated Payment User',
            'customerPaymentUserDept' => 'Updated Payment Dept',
            'customerPaymentUserTitle' => 'Updated Payment Title',
            'customerPaymentUserEmail' => 'updated.payment@example.com',
            'serviceRepUserPublicId' => $this->serviceRepUserOption->public_id,
            'serviceMgrUserPublicId' => $this->serviceMgrUserOption->public_id,
            'quotationName' => 'Updated Quotation Name',
            'quotationNumber' => 'Updated Quotation Number',
            'quotationDate' => '2024-12-02',
            'proposalName' => 'Updated Proposal Name',
            'proposalNumber' => 'Updated Proposal Number',
            'proposalDate' => '2024-11-02',
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
            'contract_status_code' => 'contract_info_drafted',
            'service_usage_status_code' => $updateData['serviceUsageStatusCode'],
            'contract_date' => $updateData['contractDate'],
            'contract_start_date' => $updateData['contractStartDate'],
            'contract_end_date' => $updateData['contractEndDate'],
            'contract_auto_update' => $updateData['contractAutoUpdate'],
            'customer_contact_user_name' => $updateData['customerContactUserName'],
            'customer_contact_user_dept' => $updateData['customerContactUserDept'],
            'customer_contact_user_title' => $updateData['customerContactUserTitle'],
            'customer_contact_user_email' => $updateData['customerContactUserEmail'],
            'customer_contract_user_name' => $updateData['customerContractUserName'],
            'customer_contract_user_dept' => $updateData['customerContractUserDept'],
            'customer_contract_user_title' => $updateData['customerContractUserTitle'],
            'customer_contract_user_email' => $updateData['customerContractUserEmail'],
            'customer_payment_user_name' => $updateData['customerPaymentUserName'],
            'customer_payment_user_dept' => $updateData['customerPaymentUserDept'],
            'customer_payment_user_title' => $updateData['customerPaymentUserTitle'],
            'customer_payment_user_email' => $updateData['customerPaymentUserEmail'],
            'service_rep_user_option_id' => $this->serviceRepUserOption->user_option_id,
            'service_mgr_user_option_id' => $this->serviceMgrUserOption->user_option_id,
            'quotation_name' => $updateData['quotationName'],
            'quotation_number' => $updateData['quotationNumber'],
            'quotation_date' => $updateData['quotationDate'],
            'proposal_name' => $updateData['proposalName'],
            'proposal_number' => $updateData['proposalNumber'],
            'proposal_date' => $updateData['proposalDate'],
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
            'serviceUsageStatusCode' => 'awaiting_activation',
            'contractDate' => '2024-02-01',
            'contractStartDate' => '2024-02-01',
            'contractAutoUpdate' => true,
            'customerContactUserName' => 'Updated Contact User',
            'customerContactUserEmail' => 'updated.contact@example.com',
            'customerContractUserName' => 'Updated Contract User',
            'customerContractUserEmail' => 'updated.contract@example.com',
            'customerPaymentUserName' => 'Updated Payment User',
            'customerPaymentUserEmail' => 'updated.payment@example.com',
            'serviceRepUserPublicId' => $this->serviceRepUserOption->public_id,
            'serviceMgrUserPublicId' => $this->serviceMgrUserOption->public_id,
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
            'serviceUsageStatusCode' => 'awaiting_activation',
            'contractDate' => '2024-01-01',
            'contractStartDate' => '2024-01-01',
            'contractAutoUpdate' => true,
            'customerContactUserName' => 'Contact User',
            'customerContactUserEmail' => 'contact@example.com',
            'customerContractUserName' => 'Contract User',
            'customerContractUserEmail' => 'contract@example.com',
            'customerPaymentUserName' => 'Payment User',
            'customerPaymentUserEmail' => 'payment@example.com',
            'serviceRepUserPublicId' => $this->serviceRepUserOption->public_id,
            'serviceMgrUserPublicId' => $this->serviceMgrUserOption->public_id,
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
            'customerPublicId' => $this->customer->public_id,
            'contractName' => 'Test Contract',
            'serviceUsageStatusCode' => 'awaiting_activation',
            // 以下のオプションフィールドは省略
        ];

        $response = $this->putJson(
            $this->getBaseUrl(),
            $dataWithoutOptionalFields,
        );

        $response->assertStatus(204);
    }
}
