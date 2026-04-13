<?php

namespace App\Services\Admin;

use GuzzleHttp\Client;

/**
 * Исходящие HTTPS к api.telegram.org (как в проекте proxy).
 */
final class TelegramHttpClientFactory
{
    public static function make(): Client
    {
        $connectTimeout = max(5.0, (float) config('http_basic.notify_telegram_connect_timeout', 30));
        $totalTimeout = max($connectTimeout + 5.0, (float) config('http_basic.notify_telegram_timeout', 60));

        $options = [
            'timeout' => $totalTimeout,
            'connect_timeout' => $connectTimeout,
            'curl' => [
                \CURLOPT_IPRESOLVE => \CURL_IPRESOLVE_V4,
            ],
        ];

        $proxy = config('http_basic.notify_telegram_http_proxy');
        if (is_string($proxy)) {
            $proxy = trim($proxy);
            if ($proxy !== '') {
                $options['proxy'] = $proxy;
            }
        }

        return new Client($options);
    }
}
