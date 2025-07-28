<?php

declare(strict_types=1);

namespace Tests\Unit\Services\CloudSign;

use App\Enums\ServiceContractStatus;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceContract;
use App\Models\ServicePlan;
use App\Models\Tenant;
use App\Models\UserOption;
use App\Services\CloudSign\ContractService;
use App\Services\CloudSign\ParameterMappingService;
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
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class ContractServiceTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private Customer $customer;

    private Service $service;

    private ServicePlan $servicePlan;

    private UserOption $serviceRepUserOption;

    private UserOption $serviceMgrUserOption;

    private ServiceContract $serviceContract;

    private ContractService $contractService;

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

        // CloudSignの設定をモック
        Config::set('services.cloudsign.client_id', 'test-client-id');
        Config::set('services.cloudsign.host', 'https://api.cloudsign.jp');

        // テスト用の認証を設定
        $this->actingAs($authUser);

        // テスト用のサービス契約データを作成
        $this->createTestServiceContract();

        // ContractServiceのインスタンスを作成
        $this->contractService = new ContractService();

        // ParameterMappingServiceのモック
        $mockParameterMappingService = \Mockery::mock(ParameterMappingService::class);
        $mockParameterMappingService->shouldReceive('buildToWidget')
            ->andReturn(collect([]));

        $this->app->bind(ParameterMappingService::class, function () use ($mockParameterMappingService) {
            return $mockParameterMappingService;
        });
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

        // サービスプランにテンプレートIDを設定
        $this->servicePlan->update([
            'contract_template_jp_id' => 'template-jp-123',
            'contract_template_en_id' => 'template-en-123',
        ]);

        $this->serviceContract = ServiceContract::create([
            'public_id' => Str::uuid()->toString(),
            'tenant_id' => $this->tenant->tenant_id,
            'customer_id' => $this->customer->customer_id,
            'service_id' => $this->service->service_id,
            'service_plan_id' => $this->servicePlan->service_plan_id,
            'contract_name' => 'Test Contract',
            'contract_language' => 'jpn',
            'contract_status_type' => 'service_contract_status',
            'contract_status_code' => ServiceContractStatus::ContractInfoRegistered->value,
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
            'service_rep_user_option_id' => $this->serviceRepUserOption->user_option_id,
            'service_mgr_user_option_id' => $this->serviceMgrUserOption->user_option_id,
            'billing_cycle_type' => 'billing_cycle',
            'billing_cycle_code' => 'monthly',
            'invoice_remind_days' => '{7,14,21}',
            'remarks' => 'Test remarks',
        ]);
    }

    /**
     * 契約書送信が成功することをテストする（日本語契約書）
     */
    public function test_send_contract_successfully_with_japanese_language(): void
    {
        // CloudSign APIのHTTPレスポンスをモック（アクセストークン取得も含む）
        Http::fake([
            'https://api.cloudsign.jp/token' => Http::response([
                'access_token' => 'test-access-token-123',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://api.cloudsign.jp/documents' => Http::response([
                'id' => 'test-document-id-123',
                'files' => [
                    ['id' => 'file-id-123'],
                ],
                'participants' => [
                    ['id' => 'participant-sender-123', 'order' => 0],
                    ['id' => 'participant-recipient-123', 'order' => 1],
                ],
            ], 200),
            'https://api.cloudsign.jp/documents/*/files/*/widgets' => Http::response(['id' => 'widget-123'], 200),
            'https://api.cloudsign.jp/documents/*/participants/*' => Http::response(['success' => true], 200),
            'https://api.cloudsign.jp/documents/*' => Http::response(['success' => true], 200),
        ]);

        // 実際のsendContractを実行
        $this->contractService->sendContract($this->serviceContract->service_contract_id);

        // データベースの状態を確認
        $this->assertDatabaseHas('service_contracts', [
            'service_contract_id' => $this->serviceContract->service_contract_id,
            'contract_doc_id' => 'test-document-id-123',
            'contract_status_code' => ServiceContractStatus::ContractDocumentSent->value,
        ]);

        $updatedContract = ServiceContract::find($this->serviceContract->service_contract_id);
        $this->assertNotNull($updatedContract->contract_sent_at);

        // HTTPリクエストが期待通りに送信されたことを確認
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.cloudsign.jp/documents' &&
                   $request['title'] === 'Test Contract' &&
                   $request['template_id'] === 'template-jp-123';
        });
    }

    /**
     * 契約書送信が成功することをテストする（英語契約書）
     */
    public function test_send_contract_successfully_with_english_language(): void
    {
        // 契約書言語を英語に変更
        $this->serviceContract->update(['contract_language' => 'eng']);

        // CloudSign APIのHTTPレスポンスをモック（アクセストークン取得も含む）
        Http::fake([
            'https://api.cloudsign.jp/token' => Http::response([
                'access_token' => 'test-access-token-456',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://api.cloudsign.jp/documents' => Http::response([
                'id' => 'test-document-id-456',
                'files' => [
                    ['id' => 'file-id-456'],
                ],
                'participants' => [
                    ['id' => 'participant-sender-456', 'order' => 0],
                    ['id' => 'participant-recipient-456', 'order' => 1],
                ],
            ], 200),
            'https://api.cloudsign.jp/documents/*/files/*/widgets' => Http::response(['id' => 'widget-456'], 200),
            'https://api.cloudsign.jp/documents/*/participants/*' => Http::response(['success' => true], 200),
            'https://api.cloudsign.jp/documents/*' => Http::response(['success' => true], 200),
        ]);

        // 実際のsendContractを実行
        $this->contractService->sendContract($this->serviceContract->service_contract_id);

        // データベースの状態を確認
        $this->assertDatabaseHas('service_contracts', [
            'service_contract_id' => $this->serviceContract->service_contract_id,
            'contract_doc_id' => 'test-document-id-456',
            'contract_status_code' => ServiceContractStatus::ContractDocumentSent->value,
        ]);

        // HTTPリクエストが期待通りに送信されたことを確認
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.cloudsign.jp/documents' &&
                   $request['template_id'] === 'template-en-123';
        });
    }

    /**
     * 契約書送信が成功することをテストする（その他言語は英語として扱う）
     */
    public function test_send_contract_with_other_language_defaults_to_english(): void
    {
        // 契約書言語をその他の言語に変更
        $this->serviceContract->update(['contract_language' => 'fra']);

        // CloudSign APIのHTTPレスポンスをモック（アクセストークン取得も含む）
        Http::fake([
            'https://api.cloudsign.jp/token' => Http::response([
                'access_token' => 'test-access-token-789',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://api.cloudsign.jp/documents' => Http::response([
                'id' => 'test-document-id-789',
                'files' => [
                    ['id' => 'file-id-789'],
                ],
                'participants' => [
                    ['id' => 'participant-sender-789', 'order' => 0],
                    ['id' => 'participant-recipient-789', 'order' => 1],
                ],
            ], 200),
            'https://api.cloudsign.jp/documents/*/files/*/widgets' => Http::response(['id' => 'widget-789'], 200),
            'https://api.cloudsign.jp/documents/*/participants/*' => Http::response(['success' => true], 200),
            'https://api.cloudsign.jp/documents/*' => Http::response(['success' => true], 200),
        ]);

        // 実際のsendContractを実行
        $this->contractService->sendContract($this->serviceContract->service_contract_id);

        // データベースの状態を確認
        $this->assertDatabaseHas('service_contracts', [
            'service_contract_id' => $this->serviceContract->service_contract_id,
            'contract_doc_id' => 'test-document-id-789',
            'contract_status_code' => ServiceContractStatus::ContractDocumentSent->value,
        ]);

        // 英語テンプレートが使用されることを確認
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.cloudsign.jp/documents' &&
                   $request['template_id'] === 'template-en-123';
        });
    }

    /**
     * 存在しないサービス契約IDを指定した場合のテスト
     */
    public function test_send_contract_throws_exception_for_nonexistent_service_contract(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->contractService->sendContract(99999);
    }

    /**
     * 既に送信済みの契約書を再送信しようとした場合のテスト
     */
    public function test_send_contract_throws_exception_for_already_sent_contract(): void
    {
        // 契約ステータスを送信済みに変更
        $this->serviceContract->update([
            'contract_status_code' => ServiceContractStatus::ContractDocumentSent->value,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The contract has already been sent.');

        $this->contractService->sendContract($this->serviceContract->service_contract_id);
    }

    /**
     * 契約実行済みの契約書を再送信しようとした場合のテスト
     */
    public function test_send_contract_throws_exception_for_executed_contract(): void
    {
        // 契約ステータスを実行済みに変更
        $this->serviceContract->update([
            'contract_status_code' => ServiceContractStatus::ContractExecuted->value,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The contract has already been sent.');

        $this->contractService->sendContract($this->serviceContract->service_contract_id);
    }

    /**
     * CloudSign API接続エラーの場合のテスト
     */
    public function test_send_contract_throws_connection_exception(): void
    {
        // HTTP接続エラーをモック（アクセストークン取得は成功させる）
        Http::fake([
            'https://api.cloudsign.jp/token' => Http::response([
                'access_token' => 'test-access-token-error',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://api.cloudsign.jp/documents' => function () {
                throw new ConnectionException('Connection failed');
            },
        ]);

        $this->expectException(ConnectionException::class);

        $this->contractService->sendContract($this->serviceContract->service_contract_id);

        // エラー発生時はデータベースが更新されていないことを確認
        $this->assertDatabaseHas('service_contracts', [
            'service_contract_id' => $this->serviceContract->service_contract_id,
            'contract_doc_id' => null,
            'contract_status_code' => ServiceContractStatus::ContractInfoRegistered->value,
        ]);
    }

    /**
     * CloudSign APIでエラーレスポンスが返された場合のテスト
     */
    public function test_send_contract_throws_runtime_exception_for_api_error(): void
    {
        // CloudSign APIエラーレスポンスをモック（アクセストークン取得は成功させる）
        Http::fake([
            'https://api.cloudsign.jp/token' => Http::response([
                'access_token' => 'test-access-token-error',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://api.cloudsign.jp/documents' => Http::response(['error' => 'API Error'], 400),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to send contract. Status: 400');

        $this->contractService->sendContract($this->serviceContract->service_contract_id);

        // エラー発生時はデータベースが更新されていないことを確認
        $this->assertDatabaseHas('service_contracts', [
            'service_contract_id' => $this->serviceContract->service_contract_id,
            'contract_doc_id' => null,
            'contract_status_code' => ServiceContractStatus::ContractInfoRegistered->value,
        ]);
    }

    /**
     * CloudSign APIで参加者更新エラーが発生した場合のテスト
     */
    public function test_send_contract_throws_exception_for_participant_update_error(): void
    {
        // 参加者更新でエラーが発生するようにモック（アクセストークン取得は成功させる）
        Http::fake([
            'https://api.cloudsign.jp/token' => Http::response([
                'access_token' => 'test-access-token-participant-error',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://api.cloudsign.jp/documents' => Http::response([
                'id' => 'test-document-id-123',
                'files' => [
                    ['id' => 'file-id-123'],
                ],
                'participants' => [
                    ['id' => 'participant-sender-123', 'order' => 0],
                    ['id' => 'participant-recipient-123', 'order' => 1],
                ],
            ], 200),
            'https://api.cloudsign.jp/documents/*/files/*/widgets' => Http::response(['id' => 'widget-123'], 200),
            'https://api.cloudsign.jp/documents/*/participants/*' => Http::response(['error' => 'Participant Error'], 400),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to send contract. Status: 400');

        $this->contractService->sendContract($this->serviceContract->service_contract_id);
    }

    /**
     * アクセストークン取得エラーの場合のテスト
     */
    public function test_send_contract_throws_exception_for_token_acquisition_error(): void
    {
        // アクセストークン取得でエラーが発生するようにモック
        Http::fake([
            'https://api.cloudsign.jp/token' => Http::response(['error' => 'Invalid client'], 401),
        ]);

        // アクセストークンがnullになることでTypeErrorが発生する可能性があるので、
        // 適切な例外処理がされているかテストする
        $this->expectException(\TypeError::class);

        $this->contractService->sendContract($this->serviceContract->service_contract_id);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
