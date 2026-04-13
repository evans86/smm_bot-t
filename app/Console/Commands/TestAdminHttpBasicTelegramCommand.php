<?php

namespace App\Console\Commands;

use App\Services\Admin\AdminBasicAuthTelegramNotifier;
use Illuminate\Console\Command;

class TestAdminHttpBasicTelegramCommand extends Command
{
    protected $signature = 'admin:http-basic-telegram-test';

    protected $description = 'Проверить отправку в Telegram для уведомлений HTTP Basic (те же TOKEN и CHAT_ID, что и в вебе)';

    public function handle(AdminBasicAuthTelegramNotifier $notifier): int
    {
        $proxy = trim((string) config('admin.http_basic_notify_http_proxy', ''));
        $this->line('Параметры: прокси '.($proxy !== '' ? 'задан' : 'не задан').' (ADMIN_HTTP_BASIC_NOTIFY_HTTP_PROXY).');

        $result = $notifier->sendTestWithDiagnostics();

        if ($result['ok']) {
            $this->info('Сообщение ушло. Проверьте чат в Telegram.');
            $this->line('Подробности при сбоях: storage/logs/laravel.log (ищите «Admin HTTP Basic»).');

            return 0;
        }

        $this->error('Отправка не удалась.');
        if (! empty($result['error'])) {
            $this->line('');
            $this->warn($result['error']);
        }
        $this->line('');
        $this->line('Частые причины:');
        $this->line(' — с сервера нет маршрута до api.telegram.org (таймаут) → задайте ADMIN_HTTP_BASIC_NOTIFY_HTTP_PROXY');
        $this->line(' — после правок .env: php artisan config:clear');
        $this->line(' — сравнение с оболочкой: curl -4 -m 15 "https://api.telegram.org/bot<TOKEN>/getMe"');

        return 1;
    }
}
