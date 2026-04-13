<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Уведомления в Telegram при HTTP Basic (первый уровень доступа)
    |--------------------------------------------------------------------------
    |
    | Отправка через Guzzle (принудительно IPv4 — иначе на части хостингов api.telegram.org уходит в IPv6 и падает).
    | Если токен или chat_id пусты — уведомления не отправляются.
    |
    */

    'http_basic_notify' => [
        'telegram_token' => env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_TOKEN', ''),
        'telegram_chat_id' => env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_CHAT_ID', ''),
    ],
];
