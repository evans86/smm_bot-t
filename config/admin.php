<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Уведомления в Telegram при HTTP Basic (первый уровень доступа)
    |--------------------------------------------------------------------------
    |
    | Как в проекте vpn: Telegram Bot SDK, токен и chat_id из .env.
    |
    */

    'http_basic_notify_telegram_token' => env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_TOKEN'),
    'http_basic_notify_telegram_chat_id' => env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_CHAT_ID'),
];
