<?php

declare(strict_types=1);

namespace App\Shared\Language;

use Illuminate\Support\Facades\App;

class IsoLanguageCode
{
    public const array LOCALE_MAP  = [
        'eng' => 'en', // 英語
        'jpn' => 'ja', // 日本語
        'ind' => 'id', // インドネシア語
        'tha' => 'th', // タイ語
        'cmn' => 'zh', // 中文（簡体字）
        'yue' => 'zh-Hant', // 中文（繁体字）
        'spa' => 'es', // スペイン語
        'vie' => 'vi', // ベトナム語
    ];

    /**
     * APP_LOCALEをISO639-1にして返す
     *
     * @return string
     */
    public static function getLocaleIso639_1(): string
    {
        return self::getIso639_3From1(App::getLocale());
    }

    /**
     * ISO 639-1 → ISO 639-3
     *
     * en -> eng
     *
     * @param string $iso639_1
     * @return string|null
     */
    public static function getIso639_3From1(string $iso639_1): ?string
    {
        $reversed = array_flip(self::LOCALE_MAP);
        return $reversed[$iso639_1] ?? 'eng';
    }

    /**
     * ISO 639-3 → ISO 639-1
     *
     * eng -> en
     *
     * @param string $iso639_3
     * @return string|null
     */
    public static function getIso639_1From3(string $iso639_3): ?string
    {
        return self::LOCALE_MAP[$iso639_3] ?? 'en';
    }
}
