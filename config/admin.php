<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Уведомления в Telegram при HTTP Basic (первый уровень доступа)
    |--------------------------------------------------------------------------
    |
    | Отправка через Telegram\Bot\Api (irazasyed/telegram-bot-sdk).
    | Если токен или chat_id пусты — уведомления не отправляются.
    |
    */

    'http_basic_notify' => [
        'telegram_token' => env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_TOKEN', ''),
        'telegram_chat_id' => env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_CHAT_ID', ''),
    ],
];
