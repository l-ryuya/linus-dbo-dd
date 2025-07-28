<?php

declare(strict_types=1);

namespace App\Services\CloudSign;

use App\Services\CloudSign\Traits\Documents;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;

class GetDocumentService extends BaseService
{
    use Documents;

    /**
     * @var array<string, mixed>
     */
    private array $documents = [];

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function __construct(string $documentId)
    {
        parent::__construct();

        try {
            $response = $this->getDocuments($documentId);
            $this->throwIfNotSuccessful($response);

            $this->documents = $response->json();
        } catch (ConnectionException $e) {
            \Log::error($e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getDocument(): array
    {
        return $this->documents;
    }

    public function getStatus(): int
    {
        return (int) $this->documents['status'];
    }

    private function throwIfNotSuccessful(Response $response): void
    {
        if (!$response->successful()) {
            throw new \RuntimeException(
                'Failed to send contract. Status: ' . $response->status() .
                ' Body: ' . $response->body(),
            );
        }
    }
}
