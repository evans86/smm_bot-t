<?php

namespace App\Console\Commands;

use App\Services\Admin\TelegramHttpClientFactory;
use Illuminate\Console\Command;

class AdminTelegramTestCommand extends Command
{
    protected $signature = 'admin:telegram-test
                            {--message= : Текст тестового сообщения (по умолчанию — метка времени)}';

    protected $description = 'Тест отправки в Telegram (ADMIN_HTTP_BASIC_NOTIFY_* из .env), как в проекте proxy';

    public function handle(): int
    {
        $token = (string) (config('http_basic.notify_telegram_token') ?? '');
        $chatId = config('http_basic.notify_telegram_chat_id');

        $this->line('Конфиг: http_basic.notify_telegram_token / notify_telegram_chat_id');
        $this->line('Токен: '.$this->maskToken($token));
        $this->line('chat_id: '.($chatId === null || $chatId === '' ? '(пусто)' : (string) $chatId));
        $proxy = config('http_basic.notify_telegram_http_proxy');
        $this->line('HTTP proxy: '.(is_string($proxy) && trim($proxy) !== '' ? $this->maskProxy((string) $proxy) : '(не задан)'));
        $this->newLine();

        if ($token === '' || $chatId === null || $chatId === '') {
            $this->error('Задайте ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_TOKEN и ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_CHAT_ID в .env, затем php artisan config:clear');

            return 1;
        }

        if (is_string($chatId) && ctype_digit($chatId)) {
            $chatId = (int) $chatId;
        }

        $text = $this->option('message');
        if (! is_string($text) || $text === '') {
            $text = 'Тест admin:telegram-test — '.now()->timezone(config('app.timezone', 'UTC'))->format('Y-m-d H:i:s T');
        }

        $this->info('Отправка...');
        $this->line('Текст: '.$text);
        $this->newLine();

        try {
            $client = TelegramHttpClientFactory::make();
            $response = $client->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'http_errors' => false,
                'json' => [
                    'chat_id' => $chatId,
                    'text' => $text,
                ],
            ]);

            $status = $response->getStatusCode();
            $bodyRaw = (string) $response->getBody();
            $decoded = json_decode($bodyRaw, true);

            $this->line('HTTP статус: '.$status);
            if (is_array($decoded)) {
                $this->line('Ответ API (JSON):');
                $this->line(json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            } else {
                $this->line('Сырой ответ: '.$bodyRaw);
            }

            $ok = is_array($decoded) && ! empty($decoded['ok']);
            if ($status === 200 && $ok) {
                $this->newLine();
                $this->info('Успех: сообщение принято Telegram.');

                return 0;
            }

            $this->newLine();
            $this->error('Telegram вернул ошибку (см. description в JSON выше).');

            return 1;
        } catch (\Throwable $e) {
            $this->error('Исключение: '.$e->getMessage());

            return 1;
        }
    }

    private function maskToken(string $token): string
    {
        if ($token === '') {
            return '(пусто)';
        }
        if (strlen($token) <= 8) {
            return '***';
        }

        return substr($token, 0, 4).'…'.substr($token, -4);
    }

    private function maskProxy(string $proxy): string
    {
        if (preg_match('#^(https?://)([^:]+):([^@]+)@(.+)$#', $proxy, $m)) {
            return $m[1].$m[2].':***@'.$m[4];
        }

        return $proxy;
    }
}
