<?php

declare(strict_types=1);

return [

    'auth' => [
        'token_validation_url' => env('M5_TOKEN_VALIDATION_URL'),
        'token_functions_verify_url' => env('M5_TOKEN_FUNCTION_VERIFY_URL'),
    ],
    'user' => [
        'user_organization_url' => env('M5_USER_ORGANIZATION_URL'),
    ],
    'customer' => [
        'fixed_sys_organization_code' => env('M5_CUSTOMER_FIXED_SYS_ORGANIZATION_CODE'),
    ]

];
