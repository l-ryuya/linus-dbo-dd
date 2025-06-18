<?php

declare(strict_types=1);

namespace Tests\Feature\NoAuthentication;

use Database\Seeders\base\CountryRegionsSeeder;
use Database\Seeders\base\CountryRegionsTranslationsSeeder;
use Database\Seeders\base\SelectionItemsSeeder;
use Database\Seeders\base\SelectionItemTranslationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountryRegionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            SelectionItemsSeeder::class,
            SelectionItemTranslationsSeeder::class,
            CountryRegionsSeeder::class,
            CountryRegionsTranslationsSeeder::class,
        ]);
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
        $response = $this->getJson($this->getBaseUrl() . '?page=1');

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
                'links',
                'meta',
            ]);
    }

    /**
     * クエリパラメータでフィルタリングできることをテストする
     */
    public function test_index_filters_by_country_code(): void
    {
        // alpha2コードでフィルタリング
        $response = $this->getJson($this->getBaseUrl() . '?countryCodeAlpha2=JP&page=1');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'countryCodeAlpha2' => 'JP',
            ]);

        // alpha3コードでフィルタリング
        $response = $this->getJson($this->getBaseUrl() . '?countryCodeAlpha3=JPN&page=1');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'countryCodeAlpha3' => 'JPN',
            ]);
    }

    /**
     * ページネーションが機能することをテストする
     */
    public function test_index_supports_pagination(): void
    {
        $response = $this->getJson($this->getBaseUrl() . '?displayed=10&page=1');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'meta' => ['currentPage', 'from', 'lastPage', 'perPage'],
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
