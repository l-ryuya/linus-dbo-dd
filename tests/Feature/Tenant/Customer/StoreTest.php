<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Customer;

use App\Models\Company;
use App\Models\Tenant;
use App\Services\M5\UserOrganizationService;
use Database\Seeders\base\CountryRegionsSeeder;
use Database\Seeders\base\CountryRegionsTranslationsSeeder;
use Database\Seeders\base\SelectionItemsSeeder;
use Database\Seeders\base\SelectionItemTranslationsSeeder;
use Database\Seeders\base\TenantsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            SelectionItemsSeeder::class,
            SelectionItemTranslationsSeeder::class,
            CountryRegionsSeeder::class,
            CountryRegionsTranslationsSeeder::class,
            TenantsSeeder::class,
        ]);

        Config::set('m5.customer.fixed_sys_organization_code', 'ORG00000001');

        $this->tenant = Tenant::where('sys_organization_code', 'ORG00000010')->first();

        // UserOrganizationServiceクラスのメソッドをモック
        $this->mock(UserOrganizationService::class, function ($mock) {
            $mock->shouldReceive('getTenantByOrganizationCode')
                ->andReturn($this->tenant);
        });

        // テスト用の認証を設定
        $this->actingAs($this->createTenantManageUser());
    }

    /**
     * APIエンドポイントのベースURLを取得
     *
     * @return string
     */
    private function getBaseUrl(): string
    {
        return '/v1/tenant/customers';
    }

    /**
     * 顧客登録APIが成功すること（正常系）をテストする
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_store_creates_customer_successfully(): void
    {
        $customerData = [
            'customerName' => 'テスト株式会社',
            'customerNameEn' => 'Test Corporation',
            'websiteUrl' => 'https://example.com',
            'shareholdersUrl' => 'https://example.com/shareholders',
            'executivesUrl' => 'https://example.com/executives',
            'countryCodeAlpha3' => 'JPN', // 日本
            'languageCode' => 'jpn', // 日本語
            'postalCode' => '123-4567',
            'state' => '東京都',
            'city' => '港区',
            'street' => '赤坂1-2-3',
            'building' => '赤坂ビル101',
            'remarks' => 'テスト顧客です',
        ];

        $response = $this->postJson(
            $this->getBaseUrl(),
            $customerData,
            ['Accept-Language' => 'jpn'],
        );

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'companyPublicId',
                    'customerPublicId',
                ],
            ]);

        // データベースにレコードが作成されたことを確認
        $responseData = $response->json('data');

        $this->assertDatabaseHas('companies', [
            'public_id' => $responseData['companyPublicId'],
            'company_name_en' => $customerData['customerNameEn'],
            'country_code_alpha3' => $customerData['countryCodeAlpha3'],
            'website_url' => $customerData['websiteUrl'],
            'shareholders_url' => $customerData['shareholdersUrl'],
            'executives_url' => $customerData['executivesUrl'],
            'postal' => $customerData['postalCode'],
            'state' => $customerData['state'],
            'city' => $customerData['city'],
            'street' => $customerData['street'],
            'building' => $customerData['building'],
            'remarks' => $customerData['remarks'],
        ]);

        $company = Company::where('public_id', $responseData['companyPublicId'])->first();

        $this->assertDatabaseHas('company_name_translations', [
            'company_id' => $company->company_id,
            'language_code' => $customerData['languageCode'],
            'legal_name' => $customerData['customerName'],
        ]);

        $this->assertDatabaseHas('customers', [
            'tenant_id' => $this->tenant->tenant_id,
            'company_id' => $company->company_id,
            'sys_organization_code' => Config::get('m5.customer.fixed_sys_organization_code'),
            'customer_status_type' => 'customer_status',
            'customer_status_code' => 'under_dd',
        ]);
    }

    /**
     * バリデーションエラーが適切に処理されることをテストする（異常系）
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_store_validates_input_properly(): void
    {
        // 必須項目が欠けているデータ
        $incompleteData = [
            'customerName' => 'テスト株式会社',
            // customerNameEnが欠けている
            'websiteUrl' => 'https://example.com',
            'shareholdersUrl' => 'https://example.com/shareholders',
            'executivesUrl' => 'https://example.com/executives',
            'countryCodeAlpha3' => 'JPN',
            'languageCode' => 'jpn',
        ];

        $response = $this->postJson(
            $this->getBaseUrl(),
            $incompleteData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customerNameEn']);

        // 不正なURL形式
        $invalidUrlData = [
            'customerName' => 'テスト株式会社',
            'customerNameEn' => 'Test Corporation',
            'websiteUrl' => 'invalid-url',
            'shareholdersUrl' => 'https://example.com/shareholders',
            'executivesUrl' => 'https://example.com/executives',
            'countryCodeAlpha3' => 'JPN',
            'languageCode' => 'jpn',
        ];

        $response = $this->postJson(
            $this->getBaseUrl(),
            $invalidUrlData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['websiteUrl']);

        // 存在しない国コード
        $invalidCountryCodeData = [
            'customerName' => 'テスト株式会社',
            'customerNameEn' => 'Test Corporation',
            'websiteUrl' => 'https://example.com',
            'shareholdersUrl' => 'https://example.com/shareholders',
            'executivesUrl' => 'https://example.com/executives',
            'countryCodeAlpha3' => 'XXX', // 存在しない国コード
            'languageCode' => 'jpn',
        ];

        $response = $this->postJson(
            $this->getBaseUrl(),
            $invalidCountryCodeData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['countryCodeAlpha3']);

        // 英語名に不正な文字を含む
        $invalidEnglishNameData = [
            'customerName' => 'テスト株式会社',
            'customerNameEn' => 'Test Corporation ✓✓✓', // 許可されていない文字を含む
            'websiteUrl' => 'https://example.com',
            'shareholdersUrl' => 'https://example.com/shareholders',
            'executivesUrl' => 'https://example.com/executives',
            'countryCodeAlpha3' => 'JPN',
            'languageCode' => 'jpn',
        ];

        $response = $this->postJson(
            $this->getBaseUrl(),
            $invalidEnglishNameData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customerNameEn']);
    }

    /**
     * 長すぎる値が適切に処理されることをテストする
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_store_validates_input_length(): void
    {
        // 長すぎる値を含むデータ
        $tooLongData = [
            'customerName' => str_repeat('あ', 256), // 255文字以上
            'customerNameEn' => 'Test Corporation',
            'websiteUrl' => 'https://example.com',
            'shareholdersUrl' => 'https://example.com/shareholders',
            'executivesUrl' => 'https://example.com/executives',
            'countryCodeAlpha3' => 'JPN',
            'languageCode' => 'jpn',
            'remarks' => str_repeat('a', 256), // 255文字以上
        ];

        $response = $this->postJson(
            $this->getBaseUrl(),
            $tooLongData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customerName', 'remarks']);
    }

    /**
     * オプションフィールドが正しく処理されることをテストする
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_store_handles_optional_fields_correctly(): void
    {
        // オプションフィールドを含めないデータ
        $dataWithoutOptionalFields = [
            'customerName' => 'テスト株式会社',
            'customerNameEn' => 'Test Corporation',
            'websiteUrl' => 'https://example.com',
            'shareholdersUrl' => 'https://example.com/shareholders',
            'executivesUrl' => 'https://example.com/executives',
            'countryCodeAlpha3' => 'JPN',
            'languageCode' => 'jpn',
            // 以下のフィールドは省略
            // 'postalCode', 'state', 'city', 'street', 'building', 'remarks'
        ];

        $response = $this->postJson(
            $this->getBaseUrl(),
            $dataWithoutOptionalFields,
            ['Accept-Language' => 'jpn'],
        );

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'companyPublicId',
                    'customerPublicId',
                ],
            ]);
    }
}
