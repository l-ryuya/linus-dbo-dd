<?php

declare(strict_types=1);

namespace App\Services\DboBilling;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class BaseService
{
    protected readonly string $apiKey;

    public function __construct()
    {
        $json = config('services.billing.api_key');
        if (empty($json)) {
            throw new \RuntimeException('Billing API key is not set in the configuration.');
        }

        try {
            // AWS Secrets Manager returns a JSON string, so we decode it.
            $secrets = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            $this->apiKey = $secrets['REQUEST_API_KEY'];
        } catch (\JsonException $e) {
            $this->apiKey = $json;
        }
    }

    /**
     * @param string     $endPoint
     * @param array<string, mixed>|null $query
     *
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function getApi(
        string $endPoint,
        ?array $query = null,
    ): Response {
        return Http::withHeaders([
            'Authorization' => 'ApiKey ' . $this->apiKey,
        ])->get(
            config('services.billing.host') . $endPoint,
            $query,
        );
    }

    /**
     * @param string $endPoint
     * @param array<string, mixed>  $data
     *
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function postApi(
        string $endPoint,
        array $data = [],
    ): Response {
        return Http::withHeaders([
            'Authorization' => 'ApiKey ' . $this->apiKey,
        ])->asJson()->post(
            config('services.billing.host') . $endPoint,
            $data,
        );
    }

    /**
     * @param string $endPoint
     * @param array<string, mixed>  $data
     *
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function putApi(
        string $endPoint,
        array $data = [],
    ): Response {
        return Http::withHeaders([
            'Authorization' => 'ApiKey ' . $this->apiKey,
        ])->asJson()->put(
            config('services.billing.host') . $endPoint,
            $data,
        );
    }
}
