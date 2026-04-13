<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Уведомления в Telegram при HTTP Basic (первый уровень доступа)
    |--------------------------------------------------------------------------
    |
    | Отправка через Guzzle (принудительно IPv4).
    | Если токен или chat_id пусты — уведомления не отправляются.
    |
    | Если с сервера до api.telegram.org нет маршрута (таймаут в логах) — типично блокировка
    | хостингом; укажите исходящий HTTP(S) прокси (VPS в другой сети, где Telegram доступен).
    |
    */

    'http_basic_notify' => [
        'telegram_token' => env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_TOKEN', ''),
        'telegram_chat_id' => env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_CHAT_ID', ''),
        'http_proxy' => env('ADMIN_HTTP_BASIC_NOTIFY_HTTP_PROXY', ''),
        'verify_ssl' => filter_var(env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_SSL_VERIFY', 'true'), FILTER_VALIDATE_BOOLEAN),
    ],
];
