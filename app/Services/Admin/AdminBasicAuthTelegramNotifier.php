<?php

namespace App\Services\Admin;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Уведомления о HTTP Basic — тот же приём, что в OrderService::notifyTelegram / BotLogHelpers:
 * Guzzle, CURLOPT_IPRESOLVE_V4, свой бот и chat_id из .env (другой бот, не cron/modules).
 */
class AdminBasicAuthTelegramNotifier
{
    private const USER_AGENT_MAX = 500;

    /** Один «успех» в Telegram на сессию Laravel; ставится только после удачной отправки. */
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

    /** Как OrderService::notifyTelegram: один бот, один вызов sendMessage. */
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
        ];

        $proxy = trim((string) config('admin.http_basic_notify.http_proxy', ''));
        if ($proxy !== '') {
            $clientConfig['proxy'] = $proxy;
        }

        $client = new Client($clientConfig);

        try {
            $client->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                RequestOptions::JSON => [
                    'chat_id' => $chatId,
                    'text' => $message,
                ],
            ]);

            return true;
        } catch (\Throwable $e) {
            error_log('Admin HTTP Basic Telegram: ' . $e->getMessage());

            return false;
        }
    }
}
