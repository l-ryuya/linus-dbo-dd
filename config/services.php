<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'cloudsign' => [
        'host' => env('CLOUDSIGN_HOST'),
        'client_id' => env('CLOUDSIGN_CLIENT_ID'),
        'application_id' => env('CLOUDSIGN_APPLICATION_ID'),
        'attribute_order' => env('CLOUDSIGN_ATTRIBUTE_ORDER'),
    ],

    'billing' => [
        'host' => env('DBO_BILLING_HOST'),
        'api_key' => env('DBO_BILLING_API_KEY'),
    ],

];
