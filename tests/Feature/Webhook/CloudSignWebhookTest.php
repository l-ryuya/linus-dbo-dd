<?php

declare(strict_types=1);

namespace Tests\Feature\Webhook;

use App\Jobs\DboBilling\CustomerJob;
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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class CloudSignWebhookTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private Customer $customer;

    private Service $service;

    private ServicePlan $servicePlan;

    private UserOption $serviceRepUserOption;

    private UserOption $serviceMgrUserOption;

    private string $contractDocId;

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

        // テスト用のサービス契約データを作成
        $this->createTestServiceContract();

        // メール送信とジョブをモック
        Mail::fake();
        Queue::fake();
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

        $this->contractDocId = 'test-doc-id-12345';
        ServiceContract::create([
            'public_id' => Str::uuid()->toString(),
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
            'contract_doc_id' => $this->contractDocId,
        ]);
    }

    /**
     * APIエンドポイントのベースURLを取得
     *
     * @return string
     */
    private function getWebhookUrl(): string
    {
        return '/v1/webhooks/cloudsign/contracts';
    }

    /**
     * Webhookが正常に処理されること（先方確認中）をテストする
     */
    public function test_webhook_handles_under_review_status(): void
    {
        $payload = [
            'documentID' => $this->contractDocId,
            'status' => 1,
            'text' => 'Document is under review',
        ];

        $response = $this->postJson($this->getWebhookUrl(), $payload);

        $response->assertStatus(204);

        // データベースのステータスが変更されていないことを確認（先方確認中の場合）
        $this->assertDatabaseHas('service_contracts', [
            'contract_doc_id' => $this->contractDocId,
            'contract_status_code' => 'contract_document_sent',
            'contract_executed_at' => null,
        ]);

        // メール送信は実行されないことを確認
        Mail::assertNothingQueued();

        // ジョブは実行されないことを確認
        Queue::assertNothingPushed();
    }

    /**
     * Webhookが正常に処理されること（締結完了）をテストする
     */
    public function test_webhook_handles_executed_status(): void
    {
        $payload = [
            'documentID' => $this->contractDocId,
            'status' => 2,
            'text' => 'Contract has been executed',
        ];

        $response = $this->postJson($this->getWebhookUrl(), $payload);

        $response->assertStatus(204);

        // データベースのステータスが更新されていることを確認
        $this->assertDatabaseHas('service_contracts', [
            'contract_doc_id' => $this->contractDocId,
            'contract_status_code' => 'contract_executed',
        ]);

        // contract_executed_atが設定されていることを確認
        $serviceContract = ServiceContract::where('contract_doc_id', $this->contractDocId)->first();
        $this->assertNotNull($serviceContract->contract_executed_at);

        // CustomerJobがキューにプッシュされたことを確認
        Queue::assertPushed(CustomerJob::class, function ($job) {
            return $job->queue === 'billing';
        });

        // メール送信が実行されたことを確認
        Mail::assertQueued(\App\Mail\ContractStatusNotificationsMail::class, 2); // サービス部門とバックオフィス宛の2通
    }

    /**
     * Webhookが正常に処理されること（取り消し・却下）をテストする
     */
    public function test_webhook_handles_cancelled_status(): void
    {
        $payload = [
            'documentID' => $this->contractDocId,
            'status' => 3,
            'text' => 'Contract has been cancelled',
        ];

        $response = $this->postJson($this->getWebhookUrl(), $payload);

        $response->assertStatus(204);

        // データベースのステータスが更新されていることを確認
        $this->assertDatabaseHas('service_contracts', [
            'contract_doc_id' => $this->contractDocId,
            'contract_status_code' => 'contract_cancelled',
        ]);

        // CustomerJobは実行されないことを確認
        Queue::assertNotPushed(CustomerJob::class);

        // メール送信が実行されたことを確認
        Mail::assertQueued(\App\Mail\ContractStatusNotificationsMail::class, 2); // サービス部門とバックオフィス宛の2通
    }

    /**
     * 不明なステータスの場合をテストする
     */
    public function test_webhook_handles_unknown_status(): void
    {
        $payload = [
            'documentID' => $this->contractDocId,
            'status' => 99,
            'text' => 'Unknown status',
        ];

        $response = $this->postJson($this->getWebhookUrl(), $payload);

        $response->assertStatus(204);

        // データベースのステータスが変更されていないことを確認
        $this->assertDatabaseHas('service_contracts', [
            'contract_doc_id' => $this->contractDocId,
            'contract_status_code' => 'contract_document_sent',
        ]);

        // メール送信は実行されないことを確認
        Mail::assertNothingQueued();

        // ジョブは実行されないことを確認
        Queue::assertNothingPushed();
    }

    /**
     * textフィールドがないペイロードのテスト
     */
    public function test_webhook_handles_payload_without_text_field(): void
    {
        $payload = [
            'documentID' => $this->contractDocId,
            'status' => 2,
        ];

        $response = $this->postJson($this->getWebhookUrl(), $payload);

        $response->assertStatus(204);

        // データベースのステータスが更新されていることを確認
        $this->assertDatabaseHas('service_contracts', [
            'contract_doc_id' => $this->contractDocId,
            'contract_status_code' => 'contract_executed',
        ]);

        // メール送信が実行されたことを確認（textフィールドは空文字列として処理される）
        Mail::assertQueued(\App\Mail\ContractStatusNotificationsMail::class);
    }

    /**
     * documentIDが存在しない場合のテスト
     */
    public function test_webhook_fails_with_nonexistent_document_id(): void
    {
        $payload = [
            'documentID' => 'nonexistent-doc-id',
            'status' => 2,
            'text' => 'Contract executed',
        ];

        $response = $this->postJson($this->getWebhookUrl(), $payload);

        $response->assertStatus(404);

        // データベースに変更がないことを確認
        $this->assertDatabaseHas('service_contracts', [
            'contract_doc_id' => $this->contractDocId,
            'contract_status_code' => 'contract_document_sent',
        ]);

        // メール送信は実行されないことを確認
        Mail::assertNothingQueued();

        // ジョブは実行されないことを確認
        Queue::assertNothingPushed();
    }

    /**
     * 契約ステータスが送信済み以外の場合のテスト
     */
    public function test_webhook_fails_with_invalid_contract_status(): void
    {
        // 契約ステータスを送信済み以外に変更
        ServiceContract::where('contract_doc_id', $this->contractDocId)
            ->update(['contract_status_code' => 'contract_executed']);

        $payload = [
            'documentID' => $this->contractDocId,
            'status' => 2,
            'text' => 'Contract executed',
        ];

        $response = $this->postJson($this->getWebhookUrl(), $payload);

        $response->assertStatus(404);

        // メール送信は実行されないことを確認
        Mail::assertNothingQueued();

        // ジョブは実行されないことを確認
        Queue::assertNothingPushed();
    }

    /**
     * documentIDが未設定の場合のテスト
     */
    public function test_webhook_fails_with_missing_document_id(): void
    {
        $payload = [
            'status' => 2,
            'text' => 'Contract executed',
        ];

        $response = $this->postJson($this->getWebhookUrl(), $payload);

        $response->assertStatus(400);

        // データベースに変更がないことを確認
        $this->assertDatabaseHas('service_contracts', [
            'contract_doc_id' => $this->contractDocId,
            'contract_status_code' => 'contract_document_sent',
        ]);
    }

    /**
     * statusが未設定の場合のテスト
     */
    public function test_webhook_fails_with_missing_status(): void
    {
        $payload = [
            'documentID' => $this->contractDocId,
            'text' => 'Contract executed',
        ];

        $response = $this->postJson($this->getWebhookUrl(), $payload);

        $response->assertStatus(400);

        // データベースに変更がないことを確認
        $this->assertDatabaseHas('service_contracts', [
            'contract_doc_id' => $this->contractDocId,
            'contract_status_code' => 'contract_document_sent',
        ]);
    }

    /**
     * 空のペイロードの場合のテスト
     */
    public function test_webhook_fails_with_empty_payload(): void
    {
        $payload = [];

        $response = $this->postJson($this->getWebhookUrl(), $payload);

        $response->assertStatus(400);

        // データベースに変更がないことを確認
        $this->assertDatabaseHas('service_contracts', [
            'contract_doc_id' => $this->contractDocId,
            'contract_status_code' => 'contract_document_sent',
        ]);
    }

    /**
     * invoice_remind_daysがnullの場合の締結完了処理をテストする
     */
    public function test_webhook_handles_executed_status_with_null_invoice_remind_days(): void
    {
        // invoice_remind_daysをnullに設定
        ServiceContract::where('contract_doc_id', $this->contractDocId)
            ->update(['invoice_remind_days' => null]);

        $payload = [
            'documentID' => $this->contractDocId,
            'status' => 2,
            'text' => 'Contract has been executed',
        ];

        $response = $this->postJson($this->getWebhookUrl(), $payload);

        $response->assertStatus(204);

        // データベースのステータスが更新されていることを確認
        $this->assertDatabaseHas('service_contracts', [
            'contract_doc_id' => $this->contractDocId,
            'contract_status_code' => 'contract_executed',
        ]);

        // CustomerJobがキューにプッシュされたことを確認（空の配列で）
        Queue::assertPushed(CustomerJob::class);
    }
}
