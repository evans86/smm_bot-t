<?php

namespace App\Console\Commands;

use App\Services\Activate\OrderService;
use App\Services\Activate\ProxyService;
use Illuminate\Console\Command;

class UpdateStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:status';

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
        $orderService = new ProxyService();
        $orderService->cronUpdateStatus();
        return 0;
    }
}
