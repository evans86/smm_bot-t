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
];
