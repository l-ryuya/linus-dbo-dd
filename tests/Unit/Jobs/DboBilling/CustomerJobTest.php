<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs\DboBilling;

use App\Jobs\DboBilling\CustomerJob;
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
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class CustomerJobTest extends TestCase
{
    use RefreshDatabase;

    private CustomerJob $customerJob;

    private Tenant $tenant;

    private Service $service;

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

        // DBO Billingの設定をモック
        Config::set('services.billing.api_key', json_encode(['REQUEST_API_KEY' => 'test-api-key']));
        Config::set('services.billing.host', 'https://api.billing.example.com');
        Config::set('mail.system.admin_address', 'admin@example.com');

        $authUser = $this->createServiceManageUser();
        $this->tenant = $authUser->getUserOption()->tenant;

        // テスト用のサービス契約データを作成
        $this->createTestServiceContract();

        // テスト用のCustomerJobを作成
        $this->customerJob = new CustomerJob(
            'Test Customer',
            'customer@example.com',
            'jpn',
            'external-123',
            $this->service->billing_service_id,
            $this->publicId,
            [7, 14, 21],
        );
    }

    /**
     * テスト用のサービス契約データを作成
     */
    private function createTestServiceContract(): void
    {
        $customer = Customer::where('tenant_id', $this->tenant->tenant_id)->first();
        $this->service = Service::where('tenant_id', $this->tenant->tenant_id)->first();
        $servicePlan = ServicePlan::where('service_id', $this->service->service_id)->first();
        $serviceRepUserOption = UserOption::where('tenant_id', $this->tenant->tenant_id)
            ->where('service_id', $this->service->service_id)
            ->first();
        $serviceMgrUserOption = UserOption::where('tenant_id', $this->tenant->tenant_id)
            ->where('service_id', $this->service->service_id)
            ->first();

        $this->publicId = Str::uuid()->toString();
        ServiceContract::create([
            'public_id' => $this->publicId,
            'tenant_id' => $this->tenant->tenant_id,
            'customer_id' => $customer->customer_id,
            'service_id' => $this->service->service_id,
            'service_plan_id' => $servicePlan->service_plan_id,
            'contract_name' => 'Test Contract',
            'contract_language' => 'jpn',
            'contract_status_type' => 'service_contract_status',
            'contract_status_code' => 'contract_executed',
            'service_usage_status_type' => 'service_usage_status',
            'service_usage_status_code' => 'awaiting_activation',
            'contract_date' => '2024-01-01',
            'contract_start_date' => '2024-01-01',
            'contract_end_date' => null,
            'contract_auto_update' => false,
            'customer_contact_user_name' => 'Contact User',
            'customer_contact_user_dept' => 'Contact Dept',
            'customer_contact_user_title' => 'Contact Title',
            'customer_contact_user_email' => 'contact@example.com',
            'customer_contract_user_name' => 'Contract User',
            'customer_contract_user_dept' => 'Contract Dept',
            'customer_contract_user_title' => 'Contract Title',
            'customer_contract_user_email' => 'contract@example.com',
            'customer_payment_user_name' => 'Payment User',
            'customer_payment_user_dept' => 'Payment Dept',
            'customer_payment_user_title' => 'Payment Title',
            'customer_payment_user_email' => 'payment@example.com',
            'service_rep_user_option_id' => $serviceRepUserOption->user_option_id,
            'service_mgr_user_option_id' => $serviceMgrUserOption->user_option_id,
            'billing_cycle_type' => 'billing_cycle',
            'billing_cycle_code' => 'monthly',
            'invoice_remind_days' => '{7,14,21}',
            'remarks' => 'Test remarks',
            'contract_doc_id' => 'test-doc-id-12345',
        ]);
    }

    /**
     * 顧客登録ジョブが成功することをテストする（日本語）
     */
    public function test_handle_successfully_with_japanese_language(): void
    {
        // DBO Billing APIのHTTPレスポンスをモック
        Http::fake([
            'https://api.billing.example.com/customers/dd' => Http::response([
                'id' => 'customer-123',
                'name' => 'Test Customer',
                'email' => 'customer@example.com',
                'language' => 'JAPANESE',
                'externalId' => 'external-123',
                'invoiceRemindDays' => [7, 14, 21],
                'stripeId' => 'cus_123',
                'serviceId' => $this->service->billing_service_id,
                'serviceContractId' => $this->publicId,
            ], 200),
        ]);

        // ジョブを実行
        $this->customerJob->handle();

        // HTTPリクエストが期待通りに送信されたことを確認
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.billing.example.com/customers/dd' &&
                   $request['name'] === 'Test Customer' &&
                   $request['email'] === 'customer@example.com' &&
                   $request['language'] === 'JAPANESE' &&
                   $request['externalId'] === 'external-123' &&
                   $request['serviceId'] === $this->service->billing_service_id &&
                   $request['serviceContractId'] === $this->publicId &&
                   $request['invoiceRemindDays'] === [7, 14, 21];
        });

        // データベースの状態を確認
        $this->assertDatabaseHas('service_contracts', [
            'public_id' => $this->publicId,
            'billing_service_id' => $this->service->billing_service_id,
            'stripe_id' => 'cus_123',
        ]);
    }

    /**
     * 顧客登録ジョブが成功することをテストする（英語）
     */
    public function test_handle_successfully_with_english_language(): void
    {
        // 英語の顧客ジョブを作成
        $englishCustomerJob = new CustomerJob(
            'Test Customer EN',
            'customer-en@example.com',
            'eng',
            'external-456',
            $this->service->billing_service_id,
            $this->publicId,
            [3, 7],
        );

        // DBO Billing APIのHTTPレスポンスをモック
        Http::fake([
            'https://api.billing.example.com/customers/dd' => Http::response([
                'id' => 'customer-456',
                'name' => 'Test Customer EN',
                'email' => 'customer-en@example.com',
                'language' => 'ENGLISH',
                'externalId' => 'external-456',
                'invoiceRemindDays' => [3, 7],
                'stripeId' => 'cus_123',
                'serviceId' => $this->service->billing_service_id,
                'serviceContractId' => $this->publicId,
            ], 200),
        ]);

        // ジョブを実行
        $englishCustomerJob->handle();

        // HTTPリクエストが期待通りに送信されたことを確認
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.billing.example.com/customers/dd' &&
                   $request['name'] === 'Test Customer EN' &&
                   $request['email'] === 'customer-en@example.com' &&
                   $request['language'] === 'ENGLISH' &&
                   $request['externalId'] === 'external-456' &&
                   $request['serviceId'] === $this->service->billing_service_id &&
                   $request['serviceContractId'] === $this->publicId &&
                   $request['invoiceRemindDays'] === [3, 7];
        });

        // データベースの状態を確認
        $this->assertDatabaseHas('service_contracts', [
            'public_id' => $this->publicId,
            'billing_service_id' => $this->service->billing_service_id,
            'stripe_id' => 'cus_123',
        ]);
    }

    /**
     * その他の言語は英語として扱われることをテストする
     */
    public function test_handle_with_other_language_defaults_to_english(): void
    {
        // フランス語の顧客ジョブを作成
        $frenchCustomerJob = new CustomerJob(
            'Test Customer FR',
            'customer-fr@example.com',
            'fra',
            'external-789',
            $this->service->billing_service_id,
            $this->publicId,
            [],
        );

        // DBO Billing APIのHTTPレスポンスをモック
        Http::fake([
            'https://api.billing.example.com/customers/dd' => Http::response([
                'id' => 'customer-789',
                'name' => 'Test Customer FR',
                'email' => 'customer-fr@example.com',
                'language' => 'ENGLISH',
                'externalId' => 'external-789',
                'invoiceRemindDays' => [],
                'stripeId' => 'cus_123',
                'serviceId' => $this->service->billing_service_id,
                'serviceContractId' => $this->publicId,
            ], 200),
        ]);

        // ジョブを実行
        $frenchCustomerJob->handle();

        // 英語として処理されることを確認
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.billing.example.com/customers/dd' &&
                   $request['language'] === 'ENGLISH';
        });
    }

    /**
     * DBO Billing API接続エラーの場合のテスト
     */
    public function test_handle_throws_connection_exception(): void
    {
        // HTTP接続エラーをモック
        Http::fake([
            'https://api.billing.example.com/customers/dd' => function () {
                throw new ConnectionException('Connection failed');
            },
        ]);

        $this->expectException(ConnectionException::class);

        $this->customerJob->handle();
    }

    /**
     * DBO Billing APIでエラーレスポンスが返された場合のテスト
     */
    public function test_handle_throws_runtime_exception_for_api_error(): void
    {
        // DBO Billing APIエラーレスポンスをモック
        Http::fake([
            'https://api.billing.example.com/customers/dd' => Http::response([
                'error' => 'Bad Request',
                'message' => 'Invalid customer data',
            ], 400),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to send contract. Status: 400');

        $this->customerJob->handle();
    }

    /**
     * API認証エラーの場合のテスト
     */
    public function test_handle_throws_runtime_exception_for_auth_error(): void
    {
        // 認証エラーレスポンスをモック
        Http::fake([
            'https://api.billing.example.com/customers/dd' => Http::response([
                'error' => 'Unauthorized',
                'message' => 'Invalid API key',
            ], 401),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to send contract. Status: 401');

        $this->customerJob->handle();
    }

    /**
     * サーバーエラーの場合のテスト
     */
    public function test_handle_throws_runtime_exception_for_server_error(): void
    {
        // サーバーエラーレスポンスをモッ��
        Http::fake([
            'https://api.billing.example.com/customers/dd' => Http::response([
                'error' => 'Internal Server Error',
            ], 500),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to send contract. Status: 500');

        $this->customerJob->handle();
    }

    /**
     * ジョブがキューに正しく設定されていることをテストする
     */
    public function test_job_queue_configuration(): void
    {
        Queue::fake();

        CustomerJob::dispatch(
            'Test Name',
            'test@example.com',
            'jpn',
            'external-123',
            'service-456',
            'contract-789',
            [7, 14],
        );

        Queue::assertPushed(CustomerJob::class, function ($job) {
            return $job->connection === 'database' &&
                   $job->queue === 'billing' &&
                   $job->name === 'Test Name' &&
                   $job->email === 'test@example.com' &&
                   $job->language === 'jpn' &&
                   $job->externalId === 'external-123' &&
                   $job->serviceId === 'service-456' &&
                   $job->serviceContractId === 'contract-789' &&
                   $job->invoiceRemindDays === [7, 14];
        });
    }

    /**
     * 空のinvoiceRemindDaysでジョブが正常に動作することをテストする
     */
    public function test_handle_with_empty_invoice_remind_days(): void
    {
        $jobWithEmptyDays = new CustomerJob(
            'Test Customer Empty',
            'empty@example.com',
            'jpn',
            'external-empty',
            $this->service->billing_service_id,
            $this->publicId,
            [],
        );

        // DBO Billing APIのHTTPレスポンスをモック
        Http::fake([
            'https://api.billing.example.com/customers/dd' => Http::response([
                'id' => 'customer-empty',
                'invoiceRemindDays' => [],
            ], 200),
        ]);

        // ジョブを実行
        $jobWithEmptyDays->handle();

        // 空の配列が正しく送信されることを�����
        Http::assertSent(function ($request) {
            return $request['invoiceRemindDays'] === [];
        });
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
