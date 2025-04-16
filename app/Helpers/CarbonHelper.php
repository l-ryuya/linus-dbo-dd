<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;

if (!function_exists('convertToCarbon')) {
    /**
     * 日時文字列をCarbonインスタンスに変換する
     */
    function convertToCarbon(?string $value): ?Carbon
    {
        try {
            return empty($value) ? null : Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }
}

if (!function_exists('convertToUserTimezone')) {
    /**
     * Carbonインスタンスをユーザーのタイムゾーンに変換する
     */
    function convertToUserTimezone(?Carbon $value): ?Carbon
    {
        // どこかでユーザーのタイムゾーンを取得する必要がある、users.timezoneとかX-Timezoneとか
        return $value?->timezone('Asia/Tokyo');
    }
}

if (!function_exists('convertToCarbonUserTimezone')) {
    /**
     * 日時文字列をCarbonインスタンスに変換し、ユーザーのタイムゾーンに変換する
     */
    function convertToCarbonUserTimezone(?string $value): ?Carbon
    {
        return convertToUserTimezone(convertToCarbon($value));
    }
}
