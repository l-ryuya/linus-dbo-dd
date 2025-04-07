<?php

namespace App\Enums;

use App\Enums\Traits\EnumToGet;

/**
 * 権限種別
 */
enum RoleType: String
{
    use EnumToGet;

    case Admin = 'admin';
    case ServiceManager = 'service_manager';
    case Customer = 'customer';
}
