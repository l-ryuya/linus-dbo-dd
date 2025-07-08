<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Traits\EnumToEqual;
use App\Enums\Traits\EnumToGet;

/**
 * サービス提供ステータス
 *
 * nameとvalueはいったん同じ値とする
 * Value側にDBの値が入る想定
 */
enum ServiceStatus: String
{
    use EnumToGet;
    use EnumToEqual;

    case Preparing = 'preparing';
    case Active = 'active';
    case Suspended = 'suspended';
    case Terminated = 'terminated';
}
