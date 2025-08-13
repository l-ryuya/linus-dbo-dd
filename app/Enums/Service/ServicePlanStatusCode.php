<?php

declare(strict_types=1);

namespace App\Enums\Service;

use App\Enums\Traits\EnumToEqual;
use App\Enums\Traits\EnumToGet;

/**
 * サービスプラン提供ステータス
 *
 * nameとvalueはいったん同じ値とする
 * Value側にDBの値が入る想定
 */
enum ServicePlanStatusCode: String
{
    use EnumToGet;
    use EnumToEqual;

    case Preparing = 'preparing';
    case Active = 'active';
    case Suspended = 'suspended';
    case Terminated = 'terminated';
}
