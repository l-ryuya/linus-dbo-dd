<?php

declare(strict_types=1);

namespace Tests\Feature\ServiceRepresentatives;

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

class SearchControllerTest extends TestCase
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
        return '/v1/service-representatives';
    }

    /**
     * サービス担当者検索APIが正常なレスポンスを返すことをテストする
     */
    public function test_index_returns_successful_response(): void
    {
        $response = $this->getJson($this->getBaseUrl());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'userPublicId',
                        'userName',
                    ],
                ],
            ]);
    }

    /**
     * ユーザー名でフィルタリングできることをテストする
     */
    public function test_index_filters_by_user_name(): void
    {
        // ユーザー名でフィルタリング
        $response = $this->getJson($this->getBaseUrl() . '?userName=テスト');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'userPublicId',
                        'userName',
                    ],
                ],
            ]);
    }

    /**
     * 権限のないユーザーがアクセスできないことをテストする
     */
    public function test_index_requires_authentication(): void
    {
        // 認証をリセット
        $this->app['auth']->forgetGuards();
        // 認証なしでアクセス
        $response = $this->getJson($this->getBaseUrl());
        $response->assertStatus(401);
    }

    /**
     * バリデーションエラーが適切に処理されることをテストする
     */
    public function test_index_validates_input(): void
    {
        // 短すぎるユーザー名（1文字）
        $response = $this->getJson($this->getBaseUrl() . '?userName=a');
        $response->assertStatus(422);

        // 長すぎるユーザー名（65文字）
        $longUserName = str_repeat('a', 65);
        $response = $this->getJson($this->getBaseUrl() . '?userName=' . $longUserName);
        $response->assertStatus(422);
    }

    /**
     * 空のクエリパラメータでも正常に動作することをテストする
     */
    public function test_index_works_with_empty_parameters(): void
    {
        $response = $this->getJson($this->getBaseUrl() . '?userName=');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [],
            ]);
    }

    /**
     * 存在しないユーザー名での検索が空の結果を返すことをテストする
     */
    public function test_index_returns_empty_for_non_existent_user(): void
    {
        $response = $this->getJson($this->getBaseUrl() . '?userName=存在しないユーザー名123456');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [],
            ]);
    }
}
