<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;

if (!function_exists('convertToCarbonOrNull')) {
    /**
     * 空なら null、それ以外なら Carbon に変換
     */
    function convertToCarbonOrNull(?string $value): ?Carbon
    {
        return empty($value) ? null : Carbon::parse($value);
    }
}
