<?php

namespace App\Services\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

/**
 * Как в проекте vpn: Telegram\Bot\Api, HTML, отдельные notifySuccess / notifyFailure.
 */
class AdminBasicAuthTelegramNotifier
{
    /** Совпадает с vpn (сессионный флаг «уже уведомляли об успехе»). */
    public const SESSION_KEY_SUCCESS_NOTIFIED = 'admin_basic_telegram_success_notified';

    public function isEnabled(): bool
    {
        $token = (string) (config('admin.http_basic_notify_telegram_token') ?? '');
        $chatId = config('admin.http_basic_notify_telegram_chat_id');

        return $token !== '' && $chatId !== null && $chatId !== '';
    }

    /**
     * Для ручной проверки: php artisan admin:http-basic-telegram-test
     */
    public function sendTestMessage(): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        try {
            $this->rawSend('<b>Тест</b>: уведомления Admin HTTP Basic (artisan).');

            return true;
        } catch (\Throwable $e) {
            Log::warning('Admin HTTP Basic: тест Telegram не отправлен', [
                'error' => $e->getMessage(),
                'source' => 'admin.basic_auth.test',
            ]);

            return false;
        }
    }

    /**
     * Успешное прохождение HTTP Basic.
     */
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

    /**
     * Неудачная попытка (неверные данные; запрос без Authorization не уведомляем — см. middleware).
     *
     * @param  'invalid'  $reason
     */
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
            $this->rawSend($text);
        } catch (\Throwable $e) {
            Log::warning('Admin HTTP Basic: не удалось отправить уведомление в Telegram', [
                'error' => $e->getMessage(),
                'source' => 'admin.basic_auth',
            ]);
        }
    }

    /**
     * @throws \Throwable
     */
    private function rawSend(string $text): void
    {
        $token = (string) (config('admin.http_basic_notify_telegram_token') ?? '');
        $chatId = config('admin.http_basic_notify_telegram_chat_id');

        if ($token === '' || $chatId === null || $chatId === '') {
            return;
        }

        $api = new Api($token);
        $api->sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ]);
    }
}
