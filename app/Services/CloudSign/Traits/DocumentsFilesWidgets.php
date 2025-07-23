<?php

declare(strict_types=1);

namespace App\Services\CloudSign\Traits;

use Illuminate\Http\Client\Response;

/**
 * @see https://app.swaggerhub.com/apis/CloudSign/cloudsign-web_api/0.27.10#/documents/
 */
trait DocumentsFilesWidgets
{
    /**
     * @param string               $documentId
     * @param string               $fileId
     * @param array<string, mixed> $data
     *
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function postWidgets(
        string $documentId,
        string $fileId,
        array $data = [],
    ): Response {
        return $this->postApi(
            "/documents/{$documentId}/files/{$fileId}/widgets",
            $data,
        );
    }

    /**
     * @param string               $documentId
     * @param string               $fileId
     * @param string               $widgetId
     * @param array<string, mixed> $data
     *
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function putWidgets(
        string $documentId,
        string $fileId,
        string $widgetId,
        array $data = [],
    ): Response {
        return $this->putApi(
            "/documents/{$documentId}/files/{$fileId}/widgets/{$widgetId}",
            $data,
        );
    }
}
