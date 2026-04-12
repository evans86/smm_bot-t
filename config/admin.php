<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Учётные данные панели (не из таблицы users)
    |--------------------------------------------------------------------------
    |
    | Рекомендуется задать ADMIN_PASSWORD_BCRYPT (результат password_hash в PHP).
    | Альтернатива для разработки: ADMIN_PASSWORD в открытом виде (только .env на сервере).
    |
    */

    'username' => env('ADMIN_USERNAME', ''),

    'password_bcrypt' => env('ADMIN_PASSWORD_BCRYPT', ''),

    'password_plain' => env('ADMIN_PASSWORD', ''),
];
