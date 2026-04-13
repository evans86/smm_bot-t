<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Уведомления в Telegram при HTTP Basic (первый уровень доступа)
    |--------------------------------------------------------------------------
    |
    | Тот же контент, что в проекте vpn (HTML), отправка через API sendMessage.
    | Guzzle + IPv4 + опциональный прокси — если с хостинга до api.telegram.org нет прямого маршрута.
    |
    */

    'http_basic_notify_telegram_token' => env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_TOKEN'),
    'http_basic_notify_telegram_chat_id' => env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_CHAT_ID'),
    'http_basic_notify_http_proxy' => env('ADMIN_HTTP_BASIC_NOTIFY_HTTP_PROXY', ''),
];
