<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Traits\EnumToEqual;

/**
 * サービス提供ステータス
 *
 * nameとvalueはいったん同じ値とする
 * Value側にDBの値が入る想定
 */
enum WidgetType: String
{
    use EnumToEqual;

    case Date = 'DATE';
    case DateTime = 'DATETIME';
    case Currency = 'CURRENCY';
    case String = 'STRING';
}
