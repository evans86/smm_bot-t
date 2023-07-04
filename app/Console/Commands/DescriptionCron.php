<?php

namespace App\Console\Commands;

use App\Services\Activate\CountryService;
use Illuminate\Console\Command;

class DescriptionCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'description:cron';

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
        $orderService = new CountryService();
        $orderService->cronUpdateDescription();
        return 0;
    }
}
