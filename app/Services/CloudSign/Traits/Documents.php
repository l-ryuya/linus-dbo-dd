<?php

declare(strict_types=1);

namespace App\Services\CloudSign\Traits;

use Illuminate\Http\Client\Response;

/**
 * @see https://app.swaggerhub.com/apis/CloudSign/cloudsign-web_api/0.27.10#/documents/
 */
trait Documents
{
    /**
     * @param string|null          $documentId
     * @param array<string, mixed> $query
     *
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function getDocuments(
        ?string $documentId,
        array $query = [],
    ): Response {
        $endpoint = '/documents' . ($documentId ? "/{$documentId}" : '');

        return $this->getApi($endpoint, $query);
    }

    /**
     * @param string|null          $documentId
     * @param array<string, mixed> $data
     *
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function postDocuments(
        ?string $documentId,
        array $data = [],
    ): Response {
        $endpoint = '/documents' . ($documentId ? "/{$documentId}" : '');

        return $this->postApi($endpoint, $data);
    }
}
