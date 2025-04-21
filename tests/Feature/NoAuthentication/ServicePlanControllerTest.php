<?php

declare(strict_types=1);

namespace Tests\Feature\NoAuthentication;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePlanControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    /**
     * APIエンドポイントのベースURLを取得
     *
     * @return string
     */
    private function getBaseUrl(): string
    {
        return '/v1/service-plans';
    }

    /**
     * サービスプラン一覧APIが正常なレスポンスを返すことをテストする
     */
    public function test_index_returns_successful_response(): void
    {
        $response = $this->getJson($this->getBaseUrl() . '?serviceCode=SV-00003');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'serviceCode',
                        'servicePlanCode',
                        'servicePlanStatusType',
                        'servicePlanStatus',
                        'billingCycle',
                        'unitPrice',
                        'serviceStartDate',
                        'serviceEndDate',
                        'servicePlanName',
                        'servicePlanDescription',
                    ],
                ],
            ]);
    }

    /**
     * 異なるサービスコードでフィルタリングできることをテストする
     */
    public function test_index_filters_by_different_service_codes(): void
    {
        // サービスコード1でフィルタリング
        $response1 = $this->getJson($this->getBaseUrl() . '?serviceCode=SV-00003');

        $response1->assertStatus(200);

        // サービスコード2で別のフィルタリング
        $response2 = $this->getJson($this->getBaseUrl() . '?serviceCode=SV-00002');

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
        // service_codeパラメータが無い場合
        $response = $this->getJson($this->getBaseUrl());
        $response->assertStatus(422);

        // service_codeパラメータが短すぎる場合
        $response = $this->getJson($this->getBaseUrl() . '?serviceCode=1234567');
        $response->assertStatus(422);

        // service_codeパラメータが長すぎる場合
        $tooLongCode = str_repeat('1', 17);
        $response = $this->getJson($this->getBaseUrl() . '?serviceCode=' . $tooLongCode);
        $response->assertStatus(422);
    }

    /**
     * 存在しないサービスコードでも正常に空の配列が返ることをテストする
     */
    public function test_index_returns_empty_array_for_nonexistent_service_code(): void
    {
        // 存在しないサービスコードを指定
        $response = $this->getJson($this->getBaseUrl() . '?serviceCode=NONEXIST1');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }
}
