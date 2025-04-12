<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Traits\EnumToEqual;
use App\Enums\Traits\EnumToGet;

/**
 * 権限種別
 */
enum RoleType: String
{
    use EnumToGet;
    use EnumToEqual;

    case Admin = 'admin';
    case ServiceManager = 'service_manager';
    case Customer = 'customer';
}
