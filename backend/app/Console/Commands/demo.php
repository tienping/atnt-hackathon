<?php

namespace App\Console\Commands;

use App\Http\Controllers\MobileController;
use Illuminate\Console\Command;

class demo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:start';

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
     * @return mixed
     */
    public function handle()
    {
        $this->info('[10.00am] Time for medication.');
        $this->info('-------------------------------');
        sleep(2);
        $this->info('[10.10am] Content weight remains, no action trigger.');
        $this->info('-------------------------------');
        sleep(2);
        $this->info('[10.10am] Sound and light alert triggered.');
        $this->info('-------------------------------');
        sleep(2);
        $this->info('[10.20am] Content weight remains, no action trigger.');
        $this->info('-------------------------------');
        sleep(2);
        $controller = new MobileController();
        $controller->send(MobileController::MESSAGE_REMINDER);
        $this->info('[10.20am] SMS reminder alert triggered.');
        $this->info('-------------------------------');
        sleep(2);
        $this->info('[10.22am] Action triggered. Save to database.');
        $this->info('-------------------------------');
        $this->info('[10.20am] Checking content qty is less than 20%. (YES)');
        $this->info('-------------------------------');
        sleep(2);
        $controller = new MobileController();
        $controller->send(MobileController::MESSAGE_RESTOCK);
        $this->info('[10.20am] SMS replenishment alert triggered.');
        $this->info('-------------------------------');
        $this->info('Go to analysis.');
    }
}
