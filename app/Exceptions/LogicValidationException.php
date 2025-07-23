<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

/**
 * ビジネスロジック内で起こったバリデーションエラーを処理する例外
 */
class LogicValidationException extends Exception
{
    /**
     * @var array<string, string[]>
     */
    public array $errors;

    /**
     * @param string                  $message
     * @param array<string, string[]> $errors
     * @param int                     $code
     * @param Exception|null          $previous
     */
    public function __construct(
        string $message = '',
        array $errors = [],
        int $code = 422,
        ?Exception $previous = null,
    ) {
        $message = empty($message) ? __('validation.message') : $message;
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function render(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => $this->message,
            'errors' => $this->errors,
        ], $this->code);
    }
}
