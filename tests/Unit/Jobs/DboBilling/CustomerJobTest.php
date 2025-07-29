<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs\DboBilling;

use App\Jobs\DboBilling\CustomerJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CustomerJobTest extends TestCase
{
    use RefreshDatabase;

    private CustomerJob $customerJob;

    protected function setUp(): void
    {
        parent::setUp();

        // DBO Billingの設定をモック
        Config::set('services.billing.api_key', json_encode(['REQUEST_API_KEY' => 'test-api-key']));
        Config::set('services.billing.host', 'https://api.billing.example.com');
        Config::set('mail.system.admin_address', 'admin@example.com');

        // テスト用のCustomerJobを作成
        $this->customerJob = new CustomerJob(
            'Test Customer',
            'customer@example.com',
            'jpn',
            'external-123',
            'service-456',
            'contract-789',
            [7, 14, 21],
        );
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
                'serviceId' => 'service-456',
                'serviceContractId' => 'contract-789',
                'invoiceRemindDays' => [7, 14, 21],
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
                   $request['serviceId'] === 'service-456' &&
                   $request['serviceContractId'] === 'contract-789' &&
                   $request['invoiceRemindDays'] === [7, 14, 21];
        });
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
            'service-789',
            'contract-012',
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
                'serviceId' => 'service-789',
                'serviceContractId' => 'contract-012',
                'invoiceRemindDays' => [3, 7],
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
                   $request['serviceId'] === 'service-789' &&
                   $request['serviceContractId'] === 'contract-012' &&
                   $request['invoiceRemindDays'] === [3, 7];
        });
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
            'service-012',
            'contract-345',
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
                'serviceId' => 'service-012',
                'serviceContractId' => 'contract-345',
                'invoiceRemindDays' => [],
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
            'service-empty',
            'contract-empty',
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
