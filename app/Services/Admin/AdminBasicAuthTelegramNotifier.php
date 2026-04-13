<?php

namespace App\Services\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Как в проекте proxy: TelegramHttpClientFactory + sendMessage JSON (HTML).
 */
class AdminBasicAuthTelegramNotifier
{
    public const SESSION_KEY_SUCCESS_NOTIFIED = 'admin_basic_telegram_success_notified';

    public function isEnabled(): bool
    {
        $token = (string) (config('http_basic.notify_telegram_token') ?? '');
        $chatId = config('http_basic.notify_telegram_chat_id');

        return $token !== '' && $chatId !== null && $chatId !== '';
    }

    public function sendTestMessage(): bool
    {
        return $this->sendTestWithDiagnostics()['ok'];
    }

    /**
     * @return array{ok: bool, error: ?string}
     */
    public function sendTestWithDiagnostics(): array
    {
        if (! $this->isEnabled()) {
            return [
                'ok' => false,
                'error' => 'В .env задайте ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_TOKEN и ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_CHAT_ID; затем php artisan config:clear.',
            ];
        }

        $r = $this->postHtmlMessage('<b>Тест</b>: уведомления Admin HTTP Basic (artisan).');

        return [
            'ok' => $r['ok'],
            'error' => $r['error'],
        ];
    }

    public function notifySuccess(Request $request, string $basicUsername): void
    {
        $lines = [
            '<b>✅ HTTP Basic: успех</b>',
            '<b>Проект:</b> smm',
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
            '<b>Проект:</b> smm',
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
        $token = (string) (config('http_basic.notify_telegram_token') ?? '');
        $chatId = config('http_basic.notify_telegram_chat_id');

        if ($token === '' || $chatId === null || $chatId === '') {
            Log::debug('Admin HTTP Basic: Telegram notify skipped (ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_TOKEN / CHAT_ID).');

            return;
        }

        $r = $this->postHtmlMessage($text);
        if ($r['ok']) {
            Log::info('Admin HTTP Basic: сообщение в Telegram отправлено');
        } elseif ($r['error'] !== null) {
            Log::warning('Admin HTTP Basic: не удалось отправить в Telegram', [
                'error' => self::redactTelegramSecrets($r['error']),
                'source' => 'admin.basic_auth',
            ]);
        }
    }

    /**
     * @return array{ok: bool, error: ?string}
     */
    private function postHtmlMessage(string $htmlText): array
    {
        $token = (string) (config('http_basic.notify_telegram_token') ?? '');
        $chatId = config('http_basic.notify_telegram_chat_id');

        if ($token === '' || $chatId === null || $chatId === '') {
            return ['ok' => false, 'error' => 'not configured'];
        }

        if (is_string($chatId) && ctype_digit($chatId)) {
            $chatId = (int) $chatId;
        } elseif (is_string($chatId) && preg_match('/^-?\d+$/', $chatId) === 1) {
            $chatId = (int) $chatId;
        }

        try {
            $client = TelegramHttpClientFactory::make();
            $response = $client->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'http_errors' => false,
                'json' => [
                    'chat_id' => $chatId,
                    'text' => $htmlText,
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true,
                ],
            ]);
            $status = $response->getStatusCode();
            $body = (string) $response->getBody();
            if ($status !== 200) {
                Log::warning('Admin HTTP Basic: Telegram API вернул ошибку', [
                    'status' => $status,
                    'body' => $body,
                    'source' => 'admin.basic_auth',
                ]);

                return ['ok' => false, 'error' => "HTTP {$status}: {$body}"];
            }

            $data = json_decode($body, true);
            if (is_array($data) && ! empty($data['ok'])) {
                return ['ok' => true, 'error' => null];
            }

            Log::warning('Admin HTTP Basic: Telegram ok=false', [
                'payload' => $data,
                'source' => 'admin.basic_auth',
            ]);

            return ['ok' => false, 'error' => is_array($data) ? (string) ($data['description'] ?? 'ok=false') : $body];
        } catch (\Throwable $e) {
            $msg = self::redactTelegramSecrets($e->getMessage());
            Log::warning('Admin HTTP Basic: исключение при отправке в Telegram', [
                'error' => $msg,
                'source' => 'admin.basic_auth',
            ]);

            return ['ok' => false, 'error' => $msg];
        }
    }

    private static function redactTelegramSecrets(string $text): string
    {
        return (string) preg_replace('#/bot[^/]+/#', '/bot***REDACTED***/', $text);
    }
}
