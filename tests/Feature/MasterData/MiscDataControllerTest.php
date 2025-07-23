<?php

declare(strict_types=1);

namespace Tests\Feature\MasterData;

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
use Tests\TestCase;

class MiscDataControllerTest extends TestCase
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
            CustomersSeeder::class,
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
        return '/v1/misc-data';
    }

    /**
     * 選択肢アイテム一覧APIが正常なレスポンスを返すことをテストする
     */
    public function test_index_returns_successful_response(): void
    {
        $response = $this->getJson($this->getBaseUrl());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'selectionItemType',
                        'selectionItemCode',
                        'selectionItemName',
                        'selectionItemShortName',
                    ],
                ],
            ]);
    }

    /**
     * 異なる種別でフィルタリングできることをテストする
     */
    public function test_index_filters_by_different_types(): void
    {
        // type1でフィルタリング
        $response1 = $this->getJson($this->getBaseUrl() . '?type=user_status');

        $response1->assertStatus(200);

        // type2で別のフィルタリング
        $response2 = $this->getJson($this->getBaseUrl() . '?type=world_region');

        $response2->assertStatus(200);

        // 両方のレスポンスが異なることを確認（もしデータがある場合）
        if (count($response1->json('data')) > 0 && count($response2->json('data')) > 0) {
            $this->assertNotEquals(
                $response1->json('data'),
                $response2->json('data'),
            );
        }
    }

    /**
     * バリデーションエラーが適切に処理されることをテストする
     */
    public function test_index_validates_input(): void
    {
        // typeパラメータが短すぎる場合
        $response = $this->getJson($this->getBaseUrl() . '?type=ab');
        $response->assertStatus(422);

        // typeパラメータが長すぎる場合
        $tooLongType = str_repeat('a', 65);
        $response = $this->getJson($this->getBaseUrl() . '?type=' . $tooLongType);
        $response->assertStatus(422);
    }

    /**
     * 存在しない種別でもエラーにならないことをテストする
     */
    public function test_index_returns_empty_array_for_nonexistent_type(): void
    {
        // 存在しない種別を指定
        $response = $this->getJson($this->getBaseUrl() . '?type=nonexistent_type');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }
}
