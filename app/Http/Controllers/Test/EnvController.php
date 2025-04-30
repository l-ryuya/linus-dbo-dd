<?php

declare(strict_types=1);

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class EnvController extends Controller
{
    /**
     * .envファイルの内容を出力するアクション
     *
     * @return JsonResponse
     */
    public function showEnvVariables(): JsonResponse
    {
        try {
            // 環境変数の取得
            $envVariables = [
                'APP_NAME' => config('app.name'),
                'APP_ENV' => config('app.env'),
                'APP_DEBUG' => config('app.debug'),
                'APP_URL' => config('app.url'),
                'APP_LOCALE' => config('app.locale'),
                'APP_FALLBACK_LOCALE' => config('app.fallback_locale'),
                'APP_FAKER_LOCALE' => config('app.faker_locale'),
                'LOG_CHANNEL' => config('logging.default'),
                'LOG_LEVEL' => config('logging.channels.stack.level'),
                'MAIL_MAILER' => config('mail.default'),
                'MAIL_HOST' => config('mail.mailers.smtp.host'),
                'MAIL_PORT' => config('mail.mailers.smtp.port'),
                'MAIL_FROM_ADDRESS' => config('mail.from.address'),
                'MAIL_FROM_NAME' => config('mail.from.name'),
                // セキュリティ上重要な情報は除外
            ];

            return response()->json([
                'success' => true,
                'env_variables' => $envVariables,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '環境変数の取得に失敗しました',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
