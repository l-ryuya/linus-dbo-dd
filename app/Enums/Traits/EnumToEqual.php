<?php

declare(strict_types=1);

namespace App\Enums\Traits;

/**
 * Enum比較
 */
trait EnumToEqual
{
    /**
     * nameの比較
     *
     * @param string $name
     * @return bool
     */
    public function isEqualName(string $name): bool
    {
        return $this->name === $name;
    }

    /**
     * valueの比較
     *
     * @param string $value
     * @return bool
     */
    public function isEqualValue(string $value): bool
    {
        return $this->value === $value;
    }
}
