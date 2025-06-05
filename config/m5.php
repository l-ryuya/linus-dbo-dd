<?php

declare(strict_types=1);

return [

    'auth' => [
        'token_validation_url' => env('M5_TOKEN_VALIDATION_URL'),
        'token_functions_verify_url' => env('M5_TOKEN_FUNCTION_VERIFY_URL'),
    ],

];
