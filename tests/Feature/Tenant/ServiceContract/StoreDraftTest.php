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

class StoreDraftTest extends TestCase
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
        return '/v1/tenant/service-contracts/draft';
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
            'serviceUsageStatusCode' => 'awaiting_activation',
            'contractDate' => '2025-01-01',
            'contractStartDate' => '2025-01-15',
            'contractEndDate' => '2025-12-31',
            'contractAutoUpdate' => true,
            'customerContactUserName' => 'テスト顧客担当者',
            'customerContactUserDept' => '営業部',
            'customerContactUserTitle' => '部長',
            'customerContactUserEmail' => 'contact@example.com',
            'customerContractUserName' => 'テスト契約担当者',
            'customerContractUserDept' => '管理部',
            'customerContractUserTitle' => '課長',
            'customerContractUserEmail' => 'contract@example.com',
            'customerPaymentUserName' => 'テスト支払担当者',
            'customerPaymentUserDept' => '経理部',
            'customerPaymentUserTitle' => '担当',
            'customerPaymentUserEmail' => 'payment@example.com',
            'serviceRepUserPublicId' => $this->serviceRepUserOption->public_id,
            'serviceMgrUserPublicId' => $this->serviceMgrUserOption->public_id,
            'quotationName' => '見積書名称',
            'quotationNumber' => '提案書名称',
            'quotationDate' => '2024-12-01',
            'proposalName' => '提案書名称',
            'proposalNumber' => '提案書番号',
            'proposalDate' => '2024-11-01',
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
            'contract_status_code' => 'contract_info_drafted',
            'service_usage_status_type' => 'service_usage_status',
            'service_usage_status_code' => $contractData['serviceUsageStatusCode'],
            'contract_date' => $contractData['contractDate'],
            'contract_start_date' => $contractData['contractStartDate'],
            'contract_end_date' => $contractData['contractEndDate'],
            'contract_auto_update' => $contractData['contractAutoUpdate'],
            'customer_contact_user_name' => $contractData['customerContactUserName'],
            'customer_contact_user_dept' => $contractData['customerContactUserDept'],
            'customer_contact_user_title' => $contractData['customerContactUserTitle'],
            'customer_contact_user_email' => $contractData['customerContactUserEmail'],
            'customer_contract_user_name' => $contractData['customerContractUserName'],
            'customer_contract_user_dept' => $contractData['customerContractUserDept'],
            'customer_contract_user_title' => $contractData['customerContractUserTitle'],
            'customer_contract_user_email' => $contractData['customerContractUserEmail'],
            'customer_payment_user_name' => $contractData['customerPaymentUserName'],
            'customer_payment_user_dept' => $contractData['customerPaymentUserDept'],
            'customer_payment_user_title' => $contractData['customerPaymentUserTitle'],
            'customer_payment_user_email' => $contractData['customerPaymentUserEmail'],
            'service_rep_user_option_id' => $this->serviceRepUserOption->user_option_id,
            'service_mgr_user_option_id' => $this->serviceMgrUserOption->user_option_id,
            'quotation_name' => $contractData['quotationName'],
            'quotation_number' => $contractData['quotationNumber'],
            'quotation_date' => $contractData['quotationDate'],
            'proposal_name' => $contractData['proposalName'],
            'proposal_number' => $contractData['proposalNumber'],
            'proposal_date' => $contractData['proposalDate'],
            'billing_cycle_type' => 'billing_cycle',
            'billing_cycle_code' => $contractData['billingCycleCode'],
            'invoice_remind_days' => '{' . $contractData['invoiceRemindDays'] . '}',
            'remarks' => $contractData['remarks'],
        ]);
    }

    /**
     * オプションフィールドが正しく処理されることをテストする
     */
    public function test_store_handles_optional_fields_correctly(): void
    {
        // オプションフィールドを含めないデータ
        $dataWithoutOptionalFields = [
            'servicePublicId' => $this->service->public_id,
            'customerPublicId' => $this->customer->public_id,
            'contractName' => 'テスト契約',
            'serviceUsageStatusCode' => 'awaiting_activation',
            // 以下のフィールドは省略
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
}
