<?php

declare(strict_types=1);

namespace App\Services\CloudSign\Traits;

use Illuminate\Http\Client\Response;

/**
 * @see https://app.swaggerhub.com/apis/CloudSign/cloudsign-web_api/0.27.10#/documents/
 */
trait DocumentsParticipants
{
    /**
     * @param string               $documentId
     * @param string               $participantID
     * @param array<string, mixed> $data
     *
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function putParticipants(
        string $documentId,
        string $participantID,
        array $data = [],
    ): Response {
        return $this->putApi(
            "/documents/{$documentId}/participants/{$participantID}",
            $data,
        );
    }
}
