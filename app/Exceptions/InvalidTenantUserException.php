<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ユーザーのテナント情報が識別できない場合にスローされる例外クラス
 */
class InvalidTenantUserException extends Exception
{
    public function __construct(string $message = "The user's tenant information could not be identified.")
    {
        parent::__construct($message, Response::HTTP_FORBIDDEN);
    }

    public function render(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'statusCode' => $this->getCode(),
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
