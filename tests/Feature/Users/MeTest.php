<?php

declare(strict_types=1);

namespace Tests\Feature\Users;

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
use Tests\TestCase;

class MeTest extends TestCase
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
            ServiceContractsSeeder::class,
            UserOptionsSeeder::class,
        ]);

        // テスト用の認証を設定
        $this->actingAs($this->createServiceManageUser());
    }

    /**
     * APIエンドポイントのベースURLを取得
     *
     * @return string
     */
    private function getBaseUrl(): string
    {
        return '/v1/users/me';
    }

    /**
     * ログインユーザー情報取得APIが正常なレスポンスを返すことをテストする
     */
    public function test_me_returns_successful_response(): void
    {
        $response = $this->getJson($this->getBaseUrl());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'userPublicId',
                    'roleName',
                    'companyName',
                    'serviceName',
                    'userName',
                    'userEmail',
                    'userIconUrl',
                    'countryRegionName',
                    'countryCodeAlpha3',
                    'languageName',
                    'languageCode',
                    'timeZoneName',
                    'timeZoneId',
                    'dateFormat',
                    'phoneNumber',
                ],
            ]);
    }

    /**
     * 未認証ユーザーがアクセスした場合に401エラーが返されることをテストする
     */
    public function test_me_returns_401_for_unauthenticated_user(): void
    {
        // 認証をリセット
        $this->app['auth']->forgetGuards();

        $response = $this->getJson($this->getBaseUrl());

        $response->assertStatus(401);
    }

    /**
     * レスポンスにユーザーの役割名が含まれていることをテストする
     */
    public function test_me_includes_role_name(): void
    {
        $response = $this->getJson($this->getBaseUrl());

        $response->assertStatus(200)
            ->assertJsonPath('data.roleName', fn($roleName) => !empty($roleName));
    }

    /**
     * レスポンスにユーザーの会社名が含まれていることをテストする
     */
    public function test_me_includes_company_name(): void
    {
        $response = $this->getJson($this->getBaseUrl());

        $response->assertStatus(200)
            ->assertJsonPath('data.companyName', fn($companyName) => !empty($companyName));
    }
}
