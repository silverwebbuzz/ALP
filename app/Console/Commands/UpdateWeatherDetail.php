<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CronJobController;
use Log;

class UpdateWeatherDetail extends Command
{
    protected $signature = 'WeatherDetail';

    protected $description = 'This command run every 4 hours and update weather information into database';

    protected $CronJobController;

    public function __construct()
    {
        parent::__construct();
        $this->CronJobController = new CronJobController;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Schedule Run Start: Update Weather Info');
        $this->CronJobController->UpdateWeatherDetails();
        Log::info('Schedule Run Stop: Update Weather Info');
    }
}
