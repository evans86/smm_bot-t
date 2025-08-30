<?php

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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'key_partner' => [
        'key' => env('PARTNER_KEY'),
    ],

    'bot_api_keys' => [
        'modules_log_bot_1' => env('MODULES_LOG_BOT_1'),
        'modules_log_bot_2' => env('MODULES_LOG_BOT_2'),
        'cron_log_bot_1' => env('CRON_LOG_BOT_1'),
        'cron_log_bot_2' => env('CRON_LOG_BOT_2'),
    ],
];
