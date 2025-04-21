<?php

declare(strict_types=1);

return [
    'paths' => ['v1/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'https://bizdevforge-front.dev.dsbizdev.com',
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
