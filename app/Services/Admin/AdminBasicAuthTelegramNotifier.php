<?php

namespace App\Services\Admin;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Формат сообщений как в vpn (HTML), транспорт — прямой вызов Telegram HTTP API
 * (Guzzle, IPv4, опциональный прокси — устойчивее, чем только SDK на проблемных хостингах).
 */
class AdminBasicAuthTelegramNotifier
{
    public const SESSION_KEY_SUCCESS_NOTIFIED = 'admin_basic_telegram_success_notified';

    public function isEnabled(): bool
    {
        $token = (string) (config('admin.http_basic_notify_telegram_token') ?? '');
        $chatId = config('admin.http_basic_notify_telegram_chat_id');

        return $token !== '' && $chatId !== null && $chatId !== '';
    }

    public function sendTestMessage(): bool
    {
        return $this->sendTestWithDiagnostics()['ok'];
    }

    /**
     * Для artisan: вернуть текст ошибки (токен в URL замазан).
     *
     * @return array{ok: bool, error: ?string}
     */
    public function sendTestWithDiagnostics(): array
    {
        if (! $this->isEnabled()) {
            return [
                'ok' => false,
                'error' => 'В .env не заданы ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_TOKEN и/или ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_CHAT_ID (или пустой кэш config — выполните php artisan config:clear).',
            ];
        }

        try {
            $this->sendRaw('<b>Тест</b>: уведомления Admin HTTP Basic (artisan).');

            return ['ok' => true, 'error' => null];
        } catch (Throwable $e) {
            $msg = self::redactTelegramSecrets($e->getMessage());
            Log::warning('Admin HTTP Basic: тест Telegram не отправлен', [
                'error' => $msg,
                'source' => 'admin.basic_auth.test',
            ]);

            return ['ok' => false, 'error' => $msg];
        }
    }

    public function notifySuccess(Request $request, string $basicUsername): void
    {
        $lines = [
            '<b>✅ HTTP Basic: успех</b>',
            '',
            '<b>Логин:</b> '.e($basicUsername),
        ];
        $this->appendCommonLines($lines, $request);
        $this->send(implode("\n", $lines));
    }

    public function notifyFailure(Request $request, ?string $attemptedUsername, string $reason): void
    {
        $lines = [
            '<b>❌ HTTP Basic: отказ</b>',
            '',
        ];
        if ($reason === 'missing') {
            $lines[] = '<b>Причина:</b> учётные данные не переданы (первый запрос, отмена окна или нет заголовка Authorization).';
        } else {
            $lines[] = '<b>Причина:</b> неверный логин или пароль.';
            if ($attemptedUsername !== null && $attemptedUsername !== '') {
                $lines[] = '<b>Указанный логин:</b> '.e($attemptedUsername);
            }
        }
        $lines[] = '';
        $this->appendCommonLines($lines, $request);
        $this->send(implode("\n", $lines));
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function appendCommonLines(array &$lines, Request $request): void
    {
        $ip = $request->ip();
        $forwarded = $request->header('X-Forwarded-For');
        $ua = $request->header('User-Agent', '—');
        $path = $request->getPathInfo();
        $method = $request->getMethod();
        $when = now()->timezone(config('app.timezone', 'UTC'))->format('Y-m-d H:i:s T');

        $lines[] = '<b>IP:</b> '.e((string) $ip);
        if (is_string($forwarded) && $forwarded !== '') {
            $lines[] = '<b>X-Forwarded-For:</b> '.e($forwarded);
        }
        $lines[] = '<b>Метод / путь:</b> '.e($method).' '.e($path);
        $lines[] = '<b>User-Agent:</b> '.e(mb_substr($ua, 0, 500));
        $lines[] = '<b>Время:</b> '.e($when);
    }

    private function send(string $text): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        try {
            $this->sendRaw($text);
            Log::info('Admin HTTP Basic: сообщение в Telegram отправлено');
        } catch (Throwable $e) {
            Log::warning('Admin HTTP Basic: не удалось отправить в Telegram', [
                'error' => self::redactTelegramSecrets($e->getMessage()),
                'source' => 'admin.basic_auth',
                'has_proxy' => trim((string) config('admin.http_basic_notify_http_proxy', '')) !== '',
            ]);
        }
    }

    /**
     * @throws Throwable
     */
    private function sendRaw(string $htmlText): void
    {
        $token = (string) (config('admin.http_basic_notify_telegram_token') ?? '');
        $chatId = config('admin.http_basic_notify_telegram_chat_id');
        if ($token === '' || $chatId === null || $chatId === '') {
            return;
        }

        $clientConfig = [
            'curl' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ],
            'timeout' => 15,
            'connect_timeout' => 10,
            'http_errors' => false,
        ];

        $proxy = trim((string) config('admin.http_basic_notify_http_proxy', ''));
        if ($proxy !== '') {
            $clientConfig['proxy'] = $proxy;
        }

        $client = new Client($clientConfig);
        $response = $client->post("https://api.telegram.org/bot{$token}/sendMessage", [
            RequestOptions::JSON => [
                'chat_id' => $this->normalizeChatId($chatId),
                'text' => $htmlText,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ],
        ]);

        $body = (string) $response->getBody();
        $data = json_decode($body, true);

        if (! is_array($data) || empty($data['ok'])) {
            Log::warning('Admin HTTP Basic: Telegram ok=false', [
                'http' => $response->getStatusCode(),
                'payload' => $data,
            ]);
            throw new \RuntimeException('Telegram API: '.($data['description'] ?? 'ok=false'));
        }
    }

    /**
     * @param  mixed  $chatId
     * @return int|string
     */
    private function normalizeChatId($chatId)
    {
        if (is_string($chatId) && preg_match('/^-?\d+$/', $chatId) === 1) {
            return (int) $chatId;
        }

        return $chatId;
    }

    private static function redactTelegramSecrets(string $text): string
    {
        return (string) preg_replace('#/bot[^/]+/#', '/bot***REDACTED***/', $text);
    }
}
