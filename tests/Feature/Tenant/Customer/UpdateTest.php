<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Customer;

use App\Models\Company;
use App\Models\CompanyNameTranslation;
use App\Models\Customer;
use App\Models\Tenant;
use Database\Seeders\base\CompaniesSeeder;
use Database\Seeders\base\CompanyNameTranslationsSeeder;
use Database\Seeders\base\CountryRegionsSeeder;
use Database\Seeders\base\CountryRegionsTranslationsSeeder;
use Database\Seeders\base\CustomersSeeder;
use Database\Seeders\base\SelectionItemsSeeder;
use Database\Seeders\base\SelectionItemTranslationsSeeder;
use Database\Seeders\base\ServicePlansSeeder;
use Database\Seeders\base\ServicePlanTranslationsSeeder;
use Database\Seeders\base\ServicesSeeder;
use Database\Seeders\base\ServiceTranslationsSeeder;
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

    private Company $company;

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
        ]);

        $authUser = $this->createTenantManageUser();
        $this->tenant = $authUser->getUserOption()->tenant;

        // テスト用の認証を設定
        $this->actingAs($authUser);

        // テスト用の顧客データを作成
        $this->createTestCustomer();
    }

    /**
     * テスト用の顧客データを作成
     */
    private function createTestCustomer(): void
    {
        // 会社情報を作成
        $this->company = Company::create([
            'tenant_id' => $this->tenant->tenant_id,
            'company_name_en' => 'Original Test Corporation',
            'default_language_code' => 'jpn',
            'country_code_alpha3' => 'JPN',
            'website_url' => 'https://example.com',
            'shareholders_url' => 'https://example.com/shareholders',
            'executives_url' => 'https://example.com/executives',
            'postal' => '123-4567',
            'state' => '東京都',
            'city' => '渋谷区',
            'street' => '渋谷1-1-1',
            'building' => '渋谷ビル101',
            'remarks' => 'テスト顧客の備考',
        ]);

        // 会社名の翻訳を作成
        CompanyNameTranslation::create([
            'company_id' => $this->company->company_id,
            'language_code' => 'jpn',
            'company_legal_name' => '元のテスト株式会社',
        ]);

        // 顧客情報を作成
        $this->publicId = Str::uuid()->toString();
        Customer::create([
            'public_id' => $this->publicId,
            'tenant_id' => $this->tenant->tenant_id,
            'company_id' => $this->company->company_id,
            'sys_organization_code' => $this->tenant->customers_sys_organization_code,
            'customer_status_type' => 'customer_status',
            'customer_status_code' => 'customer_registered',
        ]);
    }

    /**
     * APIエンドポイントのベースURLを取得
     *
     * @return string
     */
    private function getBaseUrl(): string
    {
        return '/v1/tenant/customers/' . $this->publicId;
    }

    /**
     * 顧客更新APIが成功すること（正常系）をテストする
     */
    public function test_update_updates_customer_successfully(): void
    {
        $updateData = [
            'customerName' => '更新テスト株式会社',
            'customerNameEn' => 'Updated Test Corporation',
            'websiteUrl' => 'https://updated-example.com',
            'shareholdersUrl' => 'https://updated-example.com/shareholders',
            'executivesUrl' => 'https://updated-example.com/executives',
            'customerStatusCode' => 'dd_completed', // 顧客ステータスを更新
            'defaultLanguageCode' => 'jpn',
            'countryCodeAlpha3' => 'USA', // 国を更新
            'postalCode' => '999-8888',
            'state' => 'カリフォルニア州',
            'city' => 'サンフランシスコ',
            'street' => 'マーケットストリート123',
            'building' => 'テックビル505',
            'remarks' => '更新された備考です',
        ];

        $response = $this->putJson(
            $this->getBaseUrl(),
            $updateData,
            ['Accept-Language' => 'jpn'],
        );

        $response->assertStatus(204);

        // データベースのレコードが更新されたことを確認
        $this->assertDatabaseHas('companies', [
            'company_id' => $this->company->company_id,
            'company_name_en' => $updateData['customerNameEn'],
            'default_language_code' => $updateData['defaultLanguageCode'],
            'country_code_alpha3' => $updateData['countryCodeAlpha3'],
            'website_url' => $updateData['websiteUrl'],
            'shareholders_url' => $updateData['shareholdersUrl'],
            'executives_url' => $updateData['executivesUrl'],
            'postal' => $updateData['postalCode'],
            'state' => $updateData['state'],
            'city' => $updateData['city'],
            'street' => $updateData['street'],
            'building' => $updateData['building'],
            'remarks' => $updateData['remarks'],
        ]);

        $this->assertDatabaseHas('company_name_translations', [
            'company_id' => $this->company->company_id,
            'language_code' => $updateData['defaultLanguageCode'],
            'company_legal_name' => $updateData['customerName'],
        ]);

        $this->assertDatabaseHas('customers', [
            'public_id' => $this->publicId,
            'tenant_id' => $this->tenant->tenant_id,
            'company_id' => $this->company->company_id,
            'customer_status_code' => $updateData['customerStatusCode'],
        ]);
    }

    /**
     * バリデーションエラーが適切に処理されることをテストする（異常系）
     */
    public function test_update_validates_input_properly(): void
    {
        // 必須項目が欠けているデータ
        $incompleteData = [
            'customerName' => '更新テスト株式会社',
            // customerNameEnが欠けている
            'websiteUrl' => 'https://updated-example.com',
            'shareholdersUrl' => 'https://updated-example.com/shareholders',
            'executivesUrl' => 'https://updated-example.com/executives',
            'customerStatusCode' => 'dd_completed',
            'defaultLanguageCode' => 'jpn',
            'countryCodeAlpha3' => 'JPN',
        ];

        $response = $this->putJson(
            $this->getBaseUrl(),
            $incompleteData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customerNameEn']);

        // 不正なURL形式
        $invalidUrlData = [
            'customerName' => '更新テスト株式会社',
            'customerNameEn' => 'Updated Test Corporation',
            'websiteUrl' => 'invalid-url',
            'shareholdersUrl' => 'https://updated-example.com/shareholders',
            'executivesUrl' => 'https://updated-example.com/executives',
            'customerStatusCode' => 'completed_dd',
            'defaultLanguageCode' => 'jpn',
            'countryCodeAlpha3' => 'JPN',
        ];

        $response = $this->putJson(
            $this->getBaseUrl(),
            $invalidUrlData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['websiteUrl']);

        // 存在しない国コード
        $invalidCountryCodeData = [
            'customerName' => '更新テスト株式会社',
            'customerNameEn' => 'Updated Test Corporation',
            'websiteUrl' => 'https://updated-example.com',
            'shareholdersUrl' => 'https://updated-example.com/shareholders',
            'executivesUrl' => 'https://updated-example.com/executives',
            'customerStatusCode' => 'dd_completed',
            'defaultLanguageCode' => 'jpn',
            'countryCodeAlpha3' => 'XXX', // 存在しない国コード
        ];

        $response = $this->putJson(
            $this->getBaseUrl(),
            $invalidCountryCodeData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['countryCodeAlpha3']);

        // 存在しない顧客ステータスコード
        $invalidStatusCodeData = [
            'customerName' => '更新テスト株式会社',
            'customerNameEn' => 'Updated Test Corporation',
            'websiteUrl' => 'https://updated-example.com',
            'shareholdersUrl' => 'https://updated-example.com/shareholders',
            'executivesUrl' => 'https://updated-example.com/executives',
            'customerStatusCode' => 'invalid_status', // 存在しない顧客ステータスコード
            'defaultLanguageCode' => 'jpn',
            'countryCodeAlpha3' => 'JPN',
        ];

        $response = $this->putJson(
            $this->getBaseUrl(),
            $invalidStatusCodeData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customerStatusCode']);

        // 英語名に不正な文字を含む
        $invalidEnglishNameData = [
            'customerName' => '更新テスト株式会社',
            'customerNameEn' => 'Updated Test Corporation ✓✓✓', // 許可されていない文字を含む
            'websiteUrl' => 'https://updated-example.com',
            'shareholdersUrl' => 'https://updated-example.com/shareholders',
            'executivesUrl' => 'https://updated-example.com/executives',
            'customerStatusCode' => 'dd_completed',
            'defaultLanguageCode' => 'jpn',
            'countryCodeAlpha3' => 'JPN',
        ];

        $response = $this->putJson(
            $this->getBaseUrl(),
            $invalidEnglishNameData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customerNameEn']);
    }

    /**
     * 長すぎる値が適切に処理されることをテストする
     */
    public function test_update_validates_input_length(): void
    {
        // 長すぎる値を含むデータ
        $tooLongData = [
            'customerName' => str_repeat('あ', 256), // 255文字以上
            'customerNameEn' => 'Updated Test Corporation',
            'websiteUrl' => 'https://updated-example.com',
            'shareholdersUrl' => 'https://updated-example.com/shareholders',
            'executivesUrl' => 'https://updated-example.com/executives',
            'customerStatusCode' => 'dd_completed',
            'defaultLanguageCode' => 'jpn',
            'countryCodeAlpha3' => 'JPN',
            'remarks' => str_repeat('a', 256), // 255文字以上
        ];

        $response = $this->putJson(
            $this->getBaseUrl(),
            $tooLongData,
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customerName', 'remarks']);
    }

    /**
     * 存在しない顧客IDを指定した場合のテスト
     */
    public function test_update_returns_404_for_nonexistent_customer(): void
    {
        $nonexistentId = 'e4a9a9d1-29ee-4029-b231-7d112f66ace0'; // 存在しないUUID

        $updateData = [
            'customerName' => '更新テスト株式会社',
            'customerNameEn' => 'Updated Test Corporation',
            'websiteUrl' => 'https://updated-example.com',
            'shareholdersUrl' => 'https://updated-example.com/shareholders',
            'executivesUrl' => 'https://updated-example.com/executives',
            'customerStatusCode' => 'dd_completed',
            'defaultLanguageCode' => 'jpn',
            'countryCodeAlpha3' => 'JPN',
        ];

        $response = $this->putJson(
            '/v1/tenant/customers/' . $nonexistentId,
            $updateData,
            ['Accept-Language' => 'jpn'],
        );

        $response->assertStatus(404);
    }

    /**
     * オプションフィールドが正しく処理されることをテストする
     */
    public function test_update_handles_optional_fields_correctly(): void
    {
        // オプションフィールドを含めないデータ
        $dataWithoutOptionalFields = [
            'customerName' => '更新テスト株式会社',
            'customerNameEn' => 'Updated Test Corporation',
            'websiteUrl' => 'https://updated-example.com',
            'shareholdersUrl' => 'https://updated-example.com/shareholders',
            'executivesUrl' => 'https://updated-example.com/executives',
            'customerStatusCode' => 'dd_completed',
            'defaultLanguageCode' => 'jpn',
            'countryCodeAlpha3' => 'JPN',
            // 以下のフィールドは省略
            // 'postalCode', 'state', 'city', 'street', 'building', 'remarks'
        ];

        $response = $this->putJson(
            $this->getBaseUrl(),
            $dataWithoutOptionalFields,
            ['Accept-Language' => 'jpn'],
        );

        $response->assertStatus(204);

        // データベースのレコードが正しく更新されていることを確認
        $this->assertDatabaseHas('companies', [
            'company_id' => $this->company->company_id,
            'company_name_en' => $dataWithoutOptionalFields['customerNameEn'],
            'website_url' => $dataWithoutOptionalFields['websiteUrl'],
        ]);

        // オプションフィールドがnullに更新されていることを確認
        $updatedCompany = Company::find($this->company->company_id);
        $this->assertNull($updatedCompany->postal);
        $this->assertNull($updatedCompany->remarks);
    }
}
