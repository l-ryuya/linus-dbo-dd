<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * サービス利用ステータス
 *
 * nameとvalueはいったん同じ値とする
 * Value側にDBの値が入る想定
 */
enum ServiceUsageStatusCode: String
{
    case AwaitingActivation = 'awaiting_activation';
    case Active = 'active';
    case Paused = 'paused';
    case Suspended = 'suspended';
    case Closed = 'closed';
    case Terminated = 'terminated';
}
