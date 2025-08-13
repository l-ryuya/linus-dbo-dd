<?php

declare(strict_types=1);

namespace App\Services\CloudSign\Traits;

use Illuminate\Http\Client\Response;

/**
 * @see https://app.swaggerhub.com/apis/CloudSign/cloudsign-web_api/0.27.10#/documents/
 */
trait DocumentsAttribute
{
    /**
     * @param string               $documentId
     * @param array<string, mixed> $data
     *
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function putAttribute(
        string $documentId,
        array $data = [],
    ): Response {
        return $this->putJsonApi(
            "/documents/{$documentId}/attribute",
            $data,
        );
    }
}
