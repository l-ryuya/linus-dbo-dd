<?php

namespace App\Enums;

use App\Enums\Traits\EnumToGet;

/**
 * サービスプラン提供ステータス
 *
 * nameとvalueはいったん同じ値とする
 * Value側にDBの値が入る想定
 */
enum ServicePlanStatus: String
{
    use EnumToGet;

    case Preparing = 'Preparing';
    case Active = 'Active';
    case Suspended = 'Suspended';
    case Terminated = 'Terminated';
}
