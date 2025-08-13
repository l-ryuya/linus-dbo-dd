<?php

declare(strict_types=1);

namespace Tests\Feature\MasterData;

use Database\Seeders\Base\CompaniesSeeder;
use Database\Seeders\Base\CompanyNameTranslationsSeeder;
use Database\Seeders\Base\CountryRegionsSeeder;
use Database\Seeders\Base\CountryRegionsTranslationsSeeder;
use Database\Seeders\Base\SelectionItemsSeeder;
use Database\Seeders\Base\SelectionItemTranslationsSeeder;
use Database\Seeders\Base\ServicePlansSeeder;
use Database\Seeders\Base\ServicePlanTranslationsSeeder;
use Database\Seeders\Base\ServicesSeeder;
use Database\Seeders\Base\ServiceTranslationsSeeder;
use Database\Seeders\Base\TenantsSeeder;
use Database\Seeders\Base\TimeZonesSeeder;
use Database\Seeders\Base\UserOptionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountryRegionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            TimeZonesSeeder::class,
            SelectionItemsSeeder::class,
            SelectionItemTranslationsSeeder::class,
            CountryRegionsSeeder::class,
            CountryRegionsTranslationsSeeder::class,
            TenantsSeeder::class,
            CompaniesSeeder::class,
            CompanyNameTranslationsSeeder::class,
            ServicesSeeder::class,
            ServicePlansSeeder::class,
            ServiceTranslationsSeeder::class,
            ServicePlanTranslationsSeeder::class,
            UserOptionsSeeder::class,
        ]);

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
        return '/v1/country-regions';
    }

    /**
     * 国・地域一覧APIが正常なレスポンスを返すことをテストする
     */
    public function test_index_returns_successful_response(): void
    {
        $response = $this->getJson($this->getBaseUrl());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'countryCodeAlpha3',
                        'countryCodeAlpha2',
                        'countryCodeNumeric',
                        'worldRegion',
                        'countryRegionName',
                        'capitalName',
                    ],
                ],
            ]);
    }

    /**
     * クエリパラメータでフィルタリングできることをテストする
     */
    public function test_index_filters_by_country_code(): void
    {
        // alpha2コードでフィルタリング
        $response = $this->getJson($this->getBaseUrl() . '?countryCodeAlpha2=JP');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'countryCodeAlpha2' => 'JP',
            ]);

        // alpha3コードでフィルタリング
        $response = $this->getJson($this->getBaseUrl() . '?countryCodeAlpha3=JPN');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'countryCodeAlpha3' => 'JPN',
            ]);
    }

    /**
     * バリデーションエラーが適切に処理されることをテストする
     */
    public function test_index_validates_input(): void
    {
        // 不正なalpha2コード（3文字）
        $response = $this->getJson($this->getBaseUrl() . '?countryCodeAlpha2=JPN');
        $response->assertStatus(422);

        // 不正なalpha3コード（2文字）
        $response = $this->getJson($this->getBaseUrl() . '?countryCodeAlpha3=JP');
        $response->assertStatus(422);
    }
}
