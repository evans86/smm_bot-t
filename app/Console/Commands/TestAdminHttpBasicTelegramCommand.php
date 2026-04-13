<?php

namespace App\Console\Commands;

use App\Services\Admin\AdminBasicAuthTelegramNotifier;
use Illuminate\Console\Command;

class TestAdminHttpBasicTelegramCommand extends Command
{
    protected $signature = 'admin:http-basic-telegram-test';

    protected $description = 'Проверить Telegram-бота для уведомлений HTTP Basic (те же TOKEN и CHAT_ID из .env)';

    public function handle(AdminBasicAuthTelegramNotifier $notifier): int
    {
        if (! $notifier->isEnabled()) {
            $this->error('Задайте в .env: ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_TOKEN и ADMIN_HTTP_BASIC_NOTIFY_TELEGRAM_CHAT_ID');
            $this->line('После правок: php artisan config:clear');

            return 1;
        }

        $this->line('Отправка тестового сообщения…');

        if ($notifier->sendTestMessage()) {
            $this->info('Готово. Проверьте чат в Telegram.');
            $this->line('Если пусто — смотрите storage/logs/laravel.log (ищите «Admin HTTP Basic Telegram»).');

            return 0;
        }

        $this->error('Отправка не удалась. Смотрите storage/logs/laravel.log');

        return 1;
    }
}
