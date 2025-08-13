<?php

declare(strict_types=1);

namespace App\Enums\Dd;

use App\Enums\Traits\EnumToEqual;

/**
 * DDエンティティ種別
 * nameとvalueは同じ値とする
 * Value側にDBの値が入る想定
 */
enum DdEntityTypeCode: string
{
    use EnumToEqual;

    case Company = 'company'; // 法人
    case Individual = 'individual'; // 個人
}
