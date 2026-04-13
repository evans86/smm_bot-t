<?php

namespace App\Services\Admin;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Отправка в Telegram через Guzzle с принудительным IPv4.
 * (Пакет irazasyed/telegram-bot-sdk на Guzzle 7 даёт GuzzleHttp\Promise\unwrap() в деструкторе;
 * на серверах без IPv6 до api.telegram.org curl иначе лезет в IPv6 и падает с «Network is unreachable».)
 */
class AdminBasicAuthTelegramNotifier
{
    private const USER_AGENT_MAX = 500;

    /** Ключ в сессии: успешное уведомление в Telegram уже отправлено (ставится только после успешного sendMessage). */
    public const SESSION_KEY_SUCCESS_NOTIFIED = 'admin_basic_telegram_success_notified';

    public function isEnabled(): bool
    {
        $token = $this->token();
        $chatId = $this->chatId();

        return $token !== '' && $chatId !== '';
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

        if ($this->send(implode("\n", $lines))) {
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

        $this->send(implode("\n", $lines));
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

    private function send(string $text): bool
    {
        $token = $this->token();
        $chatId = $this->chatId();

        if ($token === '' || $chatId === '') {
            return false;
        }

        $client = new Client([
            'curl' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ],
            'timeout' => 20,
            'connect_timeout' => 15,
            'verify' => true,
            'http_errors' => false,
        ]);

        try {
            $response = $client->post("https://api.telegram.org/bot{$token}/sendMessage", [
                RequestOptions::JSON => [
                    'chat_id' => $chatId,
                    'text' => $text,
                ],
            ]);

            $payload = json_decode((string) $response->getBody(), true);
            if (! is_array($payload) || empty($payload['ok'])) {
                Log::warning('Admin HTTP Basic: Telegram sendMessage ok=false', [
                    'http' => $response->getStatusCode(),
                    'response' => $payload,
                ]);

                return false;
            }

            Log::info('Admin HTTP Basic: уведомление в Telegram отправлено');

            return true;
        } catch (Throwable $e) {
            Log::warning('Admin HTTP Basic: Telegram sendMessage ошибка', [
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
