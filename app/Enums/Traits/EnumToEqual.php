<?php
declare(strict_types = 1);

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
}
