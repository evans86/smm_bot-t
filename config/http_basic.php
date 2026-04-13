<?php

return [

    /*
    |--------------------------------------------------------------------------
    | HTTP Basic (первый уровень перед формой входа в БД)
    |--------------------------------------------------------------------------
    |
    | Nginx + PHP-FPM: при 401 без запроса пароля добавьте в server:
    | fastcgi_param HTTP_AUTHORIZATION $http_authorization;
    |
    */

    'username' => env('HTTP_BASIC_USERNAME', ''),

    'password' => env('HTTP_BASIC_PASSWORD', ''),

    /*
    |--------------------------------------------------------------------------
    | Уведомления в Telegram после HTTP Basic (как в проекте proxy)
    |--------------------------------------------------------------------------
    */
    'notify_telegram_token' => env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_TOKEN'),
    'notify_telegram_chat_id' => env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_CHAT_ID'),

    /** Таймауты Guzzle к api.telegram.org (секунды). При cURL 28 увеличьте connect. */
    'notify_telegram_connect_timeout' => (float) env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_CONNECT_TIMEOUT', 30),
    'notify_telegram_timeout' => (float) env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_TIMEOUT', 60),

    /**
     * HTTP(S) прокси к Telegram. В .env можно ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_HTTP_PROXY или старый ADMIN_HTTP_BASIC_NOTIFY_HTTP_PROXY.
     */
    'notify_telegram_http_proxy' => env('ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_HTTP_PROXY')
        ?: env('ADMIN_HTTP_BASIC_NOTIFY_HTTP_PROXY'),
];
