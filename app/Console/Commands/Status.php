<?php

namespace App\Console\Commands;

use App\Services\Activate\OrderService;
use Illuminate\Console\Command;

class Status extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $orderService = new OrderService();
        $orderService->cronUpdateStatus();
        return 0;
    }
}
