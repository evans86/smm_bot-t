<?php

namespace App\Services\Admin;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Один бот из .env; отправка как в OrderService (Guzzle + IPv4), плюс проверка JSON ok у Telegram.
 */
class AdminBasicAuthTelegramNotifier
{
    private const USER_AGENT_MAX = 500;

    /** Один «успех» в Telegram на сессию Laravel; ставится только после подтверждённой отправки. */
    public const SESSION_KEY_SUCCESS_NOTIFIED = 'admin_basic_telegram_success_notified';

    public function isEnabled(): bool
    {
        return $this->token() !== '' && $this->chatId() !== '';
    }

    private function token(): string
    {
        return trim((string) config('admin.http_basic_notify.telegram_token', ''));
    }

    private function chatId(): string
    {
        return trim((string) config('admin.http_basic_notify.telegram_chat_id', ''));
    }

    /**
     * Для ручной проверки: php artisan admin:http-basic-telegram-test
     */
    public function sendTestMessage(): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        return $this->sendMessage('Тест: уведомления Admin HTTP Basic (artisan).');
    }

    public function notifySuccess(Request $request, string $basicLogin): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        $lines = [
            '✅ Admin HTTP Basic: успешный вход',
            'Логин: ' . $basicLogin,
        ];
        $lines = array_merge($lines, $this->contextLines($request));

        if ($this->sendMessage(implode("\n", $lines))) {
            $this->markSuccessNotifiedInSession($request);
        }
    }

    public function notifyInvalid(Request $request, string $attemptedLogin): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        $lines = [
            '⚠️ Admin HTTP Basic: неверный логин или пароль',
            'Указанный логин: ' . $attemptedLogin,
        ];
        $lines = array_merge($lines, $this->contextLines($request));

        $this->sendMessage(implode("\n", $lines));
    }

    private function markSuccessNotifiedInSession(Request $request): void
    {
        $session = $request->getSession();
        if ($session === null) {
            return;
        }

        $session->put(self::SESSION_KEY_SUCCESS_NOTIFIED, true);
        $session->save();
    }

    /**
     * @return list<string>
     */
    private function contextLines(Request $request): array
    {
        $ua = (string) $request->userAgent();
        if (strlen($ua) > self::USER_AGENT_MAX) {
            $ua = substr($ua, 0, self::USER_AGENT_MAX) . '…';
        }

        $xff = $request->headers->get('X-Forwarded-For');
        $tz = (string) config('app.timezone', 'UTC');
        $time = Carbon::now($tz)->toDateTimeString();

        $lines = [
            'IP: ' . $request->ip(),
        ];
        if ($xff !== null && $xff !== '') {
            $lines[] = 'X-Forwarded-For: ' . $xff;
        }
        $lines[] = 'Запрос: ' . $request->getMethod() . ' ' . $request->getRequestUri();
        $lines[] = 'User-Agent: ' . ($ua !== '' ? $ua : '(пусто)');
        $lines[] = 'Время (' . $tz . '): ' . $time;

        return $lines;
    }

    private function sendMessage(string $text): bool
    {
        $botToken = $this->token();
        $chatId = $this->chatId();
        if ($botToken === '' || $chatId === '') {
            return false;
        }

        $message = $text === '' ? '[Empty message]' : $text;

        $clientConfig = [
            'curl' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ],
            'timeout' => 10,
            'connect_timeout' => 5,
            'http_errors' => false,
        ];

        $proxy = trim((string) config('admin.http_basic_notify.http_proxy', ''));
        if ($proxy !== '') {
            $clientConfig['proxy'] = $proxy;
        }

        $client = new Client($clientConfig);

        try {
            $response = $client->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                RequestOptions::JSON => [
                    'chat_id' => $this->normalizeChatIdForJson($chatId),
                    'text' => $message,
                ],
            ]);

            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            if (! is_array($data) || empty($data['ok'])) {
                Log::warning('Admin HTTP Basic Telegram: API ответил ok=false', [
                    'http' => $response->getStatusCode(),
                    'telegram' => $data,
                    'body_raw' => strlen($body) > 500 ? substr($body, 0, 500) . '…' : $body,
                ]);

                return false;
            }

            Log::info('Admin HTTP Basic Telegram: сообщение отправлено');

            return true;
        } catch (Throwable $e) {
            Log::warning('Admin HTTP Basic Telegram: исключение при sendMessage', [
                'message' => $e->getMessage(),
                'has_proxy' => $proxy !== '',
            ]);

            return false;
        }
    }

    /**
     * @return int|string
     */
    private function normalizeChatIdForJson(string $chatId)
    {
        if (preg_match('/^-?\d+$/', $chatId) === 1) {
            return (int) $chatId;
        }

        return $chatId;
    }
}
