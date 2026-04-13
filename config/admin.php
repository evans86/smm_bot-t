<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Уведомления в Telegram при HTTP Basic (первый уровень доступа)
    |--------------------------------------------------------------------------
    |
    | Как OrderService::notifyTelegram: Guzzle, IPv4, один бот (отдельно от CRON/modules).
    | Прокси — если хостинг не достаёт api.telegram.org напрямую.
    |
    */

    'http_basic_notify' => [
        'telegram_token' => env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_TOKEN', ''),
        'telegram_chat_id' => env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_CHAT_ID', ''),
        'http_proxy' => env('ADMIN_HTTP_BASIC_NOTIFY_HTTP_PROXY', ''),
    ],
];
