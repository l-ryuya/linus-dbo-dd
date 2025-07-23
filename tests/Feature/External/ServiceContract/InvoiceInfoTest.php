<?php

declare(strict_types=1);

namespace Tests\Feature\External\ServiceContract;

use App\Models\ServiceContract;
use Database\Seeders\base\CompaniesSeeder;
use Database\Seeders\base\CompanyNameTranslationsSeeder;
use Database\Seeders\base\CountryRegionsSeeder;
use Database\Seeders\base\CountryRegionsTranslationsSeeder;
use Database\Seeders\base\CustomersSeeder;
use Database\Seeders\base\SelectionItemsSeeder;
use Database\Seeders\base\SelectionItemTranslationsSeeder;
use Database\Seeders\base\ServiceContractsSeeder;
use Database\Seeders\base\ServicePlansSeeder;
use Database\Seeders\base\ServicePlanTranslationsSeeder;
use Database\Seeders\base\ServicesSeeder;
use Database\Seeders\base\ServiceTranslationsSeeder;
use Database\Seeders\base\TenantsSeeder;
use Database\Seeders\base\TimeZonesSeeder;
use Database\Seeders\base\UserOptionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvoiceInfoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // 必要なシードデータを読み込み
        $this->seed([
            TimeZonesSeeder::class,
            SelectionItemsSeeder::class,
            SelectionItemTranslationsSeeder::class,
            CountryRegionsSeeder::class,
            CountryRegionsTranslationsSeeder::class,
            TenantsSeeder::class,
            CompaniesSeeder::class,
            CompanyNameTranslationsSeeder::class,
            CustomersSeeder::class,
            ServicesSeeder::class,
            ServicePlansSeeder::class,
            ServiceTranslationsSeeder::class,
            ServicePlanTranslationsSeeder::class,
            ServiceContractsSeeder::class,
            UserOptionsSeeder::class,
        ]);

        // APIキーを設定
        config(['external.billing.api_key' => 'test-api-key']);
    }

    #[Test]
    public function invoiceInfo_正常系_請求情報が取得できること(): void
    {
        // 準備
        $serviceContract = ServiceContract::first();
        $publicId = $serviceContract->public_id;

        // 実行
        $response = $this->withHeader('X-API-Key', 'test-api-key')
            ->getJson("/v1/external/service-contracts/{$publicId}/invoice-info");

        // 検証
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'serviceContractPublicId',
                    'sender' => [
                        'salesRepCompanyName',
                        'salesRepName',
                        'salesRepEmail',
                        'salesRepPhoneNumber',
                        'salesRepManagerCompanyName',
                        'salesRepManagerName',
                        'salesRepManagerEmail',
                        'salesRepManagerPhoneNumber',
                        'serviceDeptGroupEmail',
                        'backofficeGroupEmail',
                    ],
                    'recipient' => [
                        'userName',
                        'userDept',
                        'userTitle',
                        'userEmail',
                    ],
                ],
            ]);
    }

    #[Test]
    public function invoiceInfo_異常系_APIキーが不正な場合401が返却されること(): void
    {
        // 準備
        $serviceContract = ServiceContract::first();
        $publicId = $serviceContract->public_id;

        // 実行
        $response = $this->withHeader('X-API-Key', 'invalid-api-key')
            ->getJson("/v1/external/service-contracts/{$publicId}/invoice-info");

        // 検証
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized.',
            ]);
    }

    #[Test]
    public function invoiceInfo_異常系_存在しないサービス契約IDの場合404が返却されること(): void
    {
        // 準備
        $nonExistingPublicId = 'non-existing-id';

        // 実行
        $response = $this->withHeader('X-API-Key', 'test-api-key')
            ->getJson("/v1/external/service-contracts/{$nonExistingPublicId}/invoice-info");

        // 検証
        $response->assertStatus(404);
    }
}
