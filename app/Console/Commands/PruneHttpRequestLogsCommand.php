<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneHttpRequestLogsCommand extends Command
{
    protected $signature = 'http-request-logs:prune {--days= : Сколько дней хранить (по умолчанию из конфига)}';

    protected $description = 'Удалить записи http_request_logs старше порога';

    public function handle(): int
    {
        $days = $this->option('days');
        $days = $days !== null ? (int) $days : (int) config('http_request_log.retention_days', 30);

        if ($days < 1) {
            $this->error('days должно быть >= 1');

            return 1;
        }

        $cutoff = now()->subDays($days);
        $deleted = DB::table('http_request_logs')->where('created_at', '<', $cutoff)->delete();
        $this->info("Удалено записей: {$deleted} (старше {$days} дн.)");

        return 0;
    }
}
