<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\DueDiligence;
use App\Models\User;
use Database\Seeders\tests\DueDiligencesSeeder;
use Database\Seeders\tests\UsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DueDiligencesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    /**
     * 管理者ユーザーとして認証を行う
     *
     * @return void
     */
    private function authenticateAsAdmin(): void
    {
        $admin = User::where('user_code', 'SYS-000001')->first();
        Sanctum::actingAs($admin, ['service_manager']);
    }

    /**
     * APIエンドポイントのベースURLを取得
     *
     * @return string
     */
    private function getBaseUrl(): string
    {
        return '/v1/admin/due-diligences';
    }

    /**
     * デューデリジェンス詳細APIが正常なレスポンスを返すことをテストする
     */
    public function test_show_returns_successful_response(): void
    {
        $this->seed(UsersSeeder::class);
        $this->seed(DueDiligencesSeeder::class);

        $dueDiligence = DueDiligence::where('dd_entity_type_code', 'target_company')
            ->where('company_name', '株式会社FINOLAB')
            ->first();
        $company = Company::firstWhere('company_code', 'C-000003');
        $company->latest_dd_id = $dueDiligence->dd_id;
        $company->save();

        $this->authenticateAsAdmin();

        $response = $this->getJson($this->getBaseUrl() . '/' . $dueDiligence->dd_code);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'ddCode',
                    'companyName',
                    'ddStatus',
                    'aiDdResult',
                    'aiDdCompletedDate',
                    'primaryDdResult',
                    'primaryDdUserCode',
                    'primaryDdUserName',
                    'primaryDdCompletedDate',
                    'primaryDdComment',
                    'finalDdResult',
                    'finalDdUserCode',
                    'finalDdUserName',
                    'finalDdCompletedDate',
                    'finalDdComment',
                ],
            ]);
    }

    /**
     * 認証されていない場合にデューデリジェンス詳細へのアクセスが拒否されることをテストする
     */
    public function test_show_denies_access_without_authentication(): void
    {
        $ddCode = 'DD-000001';
        $response = $this->getJson($this->getBaseUrl() . '/' . $ddCode);
        $response->assertStatus(401);
    }

    /**
     * 存在しないデューデリジェンスコードの場合に404が返されることをテストする
     */
    public function test_show_returns_404_for_nonexistent_dd_code(): void
    {
        $this->authenticateAsAdmin();

        $nonExistentDdCode = 'NON-EXISTENT';
        $response = $this->getJson($this->getBaseUrl() . '/' . $nonExistentDdCode);

        $response->assertStatus(404);
    }

    /**
     * 不正なスコープを持つユーザーがアクセスできないことをテストする
     */
    public function test_show_denies_access_with_invalid_scope(): void
    {
        $user = User::where('user_code', 'SYS-000001')->first();
        // 不正なスコープでアクティングする
        Sanctum::actingAs($user, ['invalid_scope']);

        $ddCode = 'DD-000001';
        $response = $this->getJson($this->getBaseUrl() . '/' . $ddCode);

        $response->assertStatus(403);
    }
}