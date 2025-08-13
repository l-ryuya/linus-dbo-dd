<?php

declare(strict_types=1);

namespace App\Services\CloudSign;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class BaseService
{
    protected readonly string $clientId;
    protected readonly string $applicationId;

    protected ?string $accessToken = null;

    public function __construct()
    {
        $this->clientId = config('services.cloudsign.client_id');
        $this->applicationId = config('services.cloudsign.application_id');
    }

    /**
     * @return string
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function getAccessToken(): string
    {
        if (!empty($this->accessToken)) {
            return $this->accessToken;
        }

        $response = Http::asForm()->withHeaders(
            ['X-AppID' => $this->applicationId],
        )->post(
            config('services.cloudsign.host') . '/token',
            ['client_id' => $this->clientId],
        );

        $this->accessToken = $response->json('access_token');

        return $this->accessToken;
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
        return Http::withToken(
            $this->getAccessToken(),
        )->withHeaders(
            ['X-AppID' => $this->applicationId],
        )->get(
            config('services.cloudsign.host') . $endPoint,
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
        return Http::withToken(
            $this->getAccessToken(),
        )->withHeaders(
            ['X-AppID' => $this->applicationId],
        )->asForm()->post(
            config('services.cloudsign.host') . $endPoint,
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
        return Http::withToken(
            $this->getAccessToken(),
        )->withHeaders(
            ['X-AppID' => $this->applicationId],
        )->asForm()->put(
            config('services.cloudsign.host') . $endPoint,
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
    protected function putJsonApi(
        string $endPoint,
        array $data = [],
    ): Response {
        return Http::withToken(
            $this->getAccessToken(),
        )->withHeaders(
            ['X-AppID' => $this->applicationId],
        )->asJson()->put(
            config('services.cloudsign.host') . $endPoint,
            $data,
        );
    }
}
