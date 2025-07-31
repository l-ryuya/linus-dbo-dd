<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\ServiceContract;

use App\Jobs\DboBilling\CustomerJob;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceContract;
use App\Models\ServicePlan;
use App\Models\Tenant;
use App\Models\UserOption;
use App\Services\CloudSign\GetDocumentService;
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
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class CloudsignStatusSyncTest extends TestCase
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

        App::setLocale('ja');

        $authUser = $this->createServiceManageUser();
        $this->tenant = $authUser->getUserOption()->tenant;

        // CloudSignの設定をモック
        Config::set('services.cloudsign.client_id', 'test-client-id');
        Config::set('services.cloudsign.host', 'test-host');

        // テスト用の認証を設定
        $this->actingAs($authUser);

        // テスト用のサービス契約データを作成
        $this->createTestServiceContract();

        // メール送信をモック
        Mail::fake();
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
            'contract_name' => 'Test Contract',
            'contract_language' => 'jpn',
            'contract_status_type' => 'service_contract_status',
            'contract_status_code' => 'contract_document_sent',
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
            'contract_doc_id' => 'test-doc-id-12345',
        ]);
    }

    /**
     * APIエンドポイントのベースURLを取得
     *
     * @return string
     */
    private function getBaseUrl(): string
    {
        return '/v1/tenant/service-contracts/' . $this->publicId . '/cloudsign-status/sync';
    }

    /**
     * 契約ステータス同期APIが成功すること（先方確認中）をテストする
     */
    public function test_cloudsign_status_sync_updates_status_to_under_review(): void
    {
        // GetDocumentServiceのモックを作成
        $mockService = \Mockery::mock(GetDocumentService::class)->makePartial();
        $mockService->shouldReceive('getStatus')->andReturn(1);

        // サービスコンテナにモックを登録
        $this->app->bind(GetDocumentService::class, function () use ($mockService) {
            return $mockService;
        });

        $response = $this->postJson($this->getBaseUrl());

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'contractStatus' => '先方確認中',
                ],
            ]);

        // データベースのステータスが変更されていないことを確認（先方確認中の場合）
        $this->assertDatabaseHas('service_contracts', [
            'public_id' => $this->publicId,
            'contract_status_code' => 'contract_document_sent',
            'contract_executed_at' => null,
        ]);
    }

    /**
     * 契約ステータス同期APIが成功すること（締結完了）をテストする
     */
    public function test_cloudsign_status_sync_updates_status_to_executed(): void
    {
        Queue::fake();

        // GetDocumentServiceのモックを作成
        $mockService = \Mockery::mock(GetDocumentService::class)->makePartial();
        $mockService->shouldReceive('getStatus')->andReturn(2);

        // サービスコンテナにモックを登録
        $this->app->bind(GetDocumentService::class, function () use ($mockService) {
            return $mockService;
        });

        $response = $this->postJson($this->getBaseUrl());

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'contractStatus' => '締結完了',
                ],
            ]);

        // データベースのステータスが更新されていることを確認
        $this->assertDatabaseHas('service_contracts', [
            'public_id' => $this->publicId,
            'contract_status_code' => 'contract_executed',
        ]);

        // contract_executed_atが設定されていることを確認
        $serviceContract = ServiceContract::where('public_id', $this->publicId)->first();
        $this->assertNotNull($serviceContract->contract_executed_at);

        // CustomerJobがキューにプッシュされたことを確認
        Queue::assertPushedOn('billing', CustomerJob::class);

        // メール送信が実行されたことを確認
        Mail::assertQueued(\App\Mail\ContractStatusNotificationsMail::class);
    }

    /**
     * 契約ステータス同期APIが成功すること（取り消し・却下）をテストする
     */
    public function test_cloudsign_status_sync_updates_status_to_cancelled(): void
    {
        // GetDocumentServiceのモックを作成
        $mockService = \Mockery::mock(GetDocumentService::class)->makePartial();
        $mockService->shouldReceive('getStatus')->andReturn(3);

        // サービスコンテナにモックを登録
        $this->app->bind(GetDocumentService::class, function () use ($mockService) {
            return $mockService;
        });

        $response = $this->postJson($this->getBaseUrl());

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'contractStatus' => '取り消し・却下',
                ],
            ]);

        // データベースのステータスが更新されていることを確認
        $this->assertDatabaseHas('service_contracts', [
            'public_id' => $this->publicId,
            'contract_status_code' => 'contract_cancelled',
        ]);

        // メール送信が実行されたことを確認
        Mail::assertQueued(\App\Mail\ContractStatusNotificationsMail::class);
    }

    /**
     * 不明なステータスの場合をテストする
     */
    public function test_cloudsign_status_sync_handles_unknown_status(): void
    {
        // GetDocumentServiceのモックを作成
        $mockService = \Mockery::mock(GetDocumentService::class)->makePartial();
        $mockService->shouldReceive('getStatus')->andReturn(99);

        // サービスコンテナにモックを登録
        $this->app->bind(GetDocumentService::class, function () use ($mockService) {
            return $mockService;
        });

        $response = $this->postJson($this->getBaseUrl());

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'contractStatus' => '不明なステータス',
                ],
            ]);

        // データベースのステータスが変更されていないことを確認
        $this->assertDatabaseHas('service_contracts', [
            'public_id' => $this->publicId,
            'contract_status_code' => 'contract_document_sent',
        ]);
    }

    /**
     * 不正な契約ステータスで同期を実行した場合のエラーテスト
     */
    public function test_cloudsign_status_sync_fails_with_invalid_contract_status(): void
    {
        // 契約ステータスを送信済み以外に変更
        ServiceContract::where('public_id', $this->publicId)
            ->update(['contract_status_code' => 'contract_executed']);

        $response = $this->postJson($this->getBaseUrl());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['contractStatusCode']);
    }

    /**
     * 存在しないサービス契約IDを指定した場合のテスト
     */
    public function test_cloudsign_status_sync_returns_404_for_nonexistent_service_contract(): void
    {
        $nonexistentId = 'e4a9a9d1-29ee-4029-b231-7d112f66ace0';

        $response = $this->postJson(
            '/v1/tenant/service-contracts/' . $nonexistentId . '/cloudsign-status/sync',
        );

        $response->assertStatus(404);
    }

    /**
     * CloudSign API接続エラーの場合のテスト
     */
    public function test_cloudsign_status_sync_handles_connection_error(): void
    {
        // GetDocumentServiceのモックを作成して例外を投げる
        /** @var GetDocumentService&\Mockery\MockInterface $mockService */
        $mockService = \Mockery::mock(GetDocumentService::class)->makePartial();
        /** @var \Mockery\Expectation $expectation */
        $expectation = $mockService->shouldReceive('getStatus');
        $expectation->andThrow(new \Illuminate\Http\Client\ConnectionException('Connection failed'));

        // サービスコンテナにモックを登録
        $this->app->bind(GetDocumentService::class, function () use ($mockService) {
            return $mockService;
        });

        $response = $this->postJson($this->getBaseUrl());

        $response->assertStatus(500);
    }

    /**
     * contract_doc_idが設定されていないサービス契約の場合のテスト
     */
    public function test_cloudsign_status_sync_with_missing_contract_doc_id(): void
    {
        // contract_doc_idをnullに設定
        ServiceContract::where('public_id', $this->publicId)
            ->update(['contract_doc_id' => null]);

        $response = $this->postJson($this->getBaseUrl());

        // contract_doc_idが未設定の場合は422エラーが返される
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['contractDocId']);
    }
}
