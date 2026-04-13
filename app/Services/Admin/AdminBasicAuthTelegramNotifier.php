<?php

namespace App\Services\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Throwable;

class AdminBasicAuthTelegramNotifier
{
    private const USER_AGENT_MAX = 500;

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

        $this->send(implode("\n", $lines));
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

    private function send(string $text): void
    {
        $token = $this->token();
        $chatId = $this->chatId();

        if ($token === '' || $chatId === '') {
            return;
        }

        try {
            $telegram = new Api($token);
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
            ]);
        } catch (Throwable $e) {
            Log::warning('Admin HTTP Basic: Telegram sendMessage ошибка', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
