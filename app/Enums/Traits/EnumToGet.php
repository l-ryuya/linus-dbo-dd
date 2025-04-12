<?php
declare(strict_types = 1);

namespace App\Enums\Traits;

/**
 * Enumから取得する
 */
trait EnumToGet
{
    /**
     * NameからValueを取得
     *
     * @param string $value
     * @return string|null
     */
    public static function getNameFromValue(string $value): ?string
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case->name;
            }
        }
        return null;
    }

    /**
     * ValueからNameを取得
     *
     * @param string $name
     * @return string|null
     */
    public static function getValueFromName(string $name): ?string
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case->value;
            }
        }
        return null;
    }
}
