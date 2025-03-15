<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * 多言語に対応するためのロケール変更
 */
class LocaleMiddleware
{
    /**
     * HTTPヘッダ Accept-Language から言語を取得し変更する
     *
     * 例：
     * Accept-Language: ja
     * もしくは
     * Accept-Language: en
     *
     * https://developer.mozilla.org/ja/docs/Web/HTTP/Headers/Accept-Language
     * 重み付けなどは考慮せず、対応言語の単体指定のみとする
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // サポートする言語リスト
        $supportedLocales = ['en', 'ja'];

        // Accept-Language ヘッダーから言語を取得
        $acceptLanguage = $request->header('Accept-Language');

        if ($acceptLanguage) {
            $languages = explode(',', $acceptLanguage);
            foreach ($languages as $lang) {
                $locale = substr(trim($lang), 0, 2); // 言語コードのみ抽出
                if (in_array($locale, $supportedLocales)) {
                    App::setLocale($locale);
                    break;
                }
            }
        }

        return $next($request);
    }
}
