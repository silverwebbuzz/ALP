<?php


namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Artisan;
use Carbon\Carbon;
use App\Traits\Common;
use App\Helpers\Helper;
use App\Http\Controllers\CronJobController;
use App\Console\Commands\UpdateWeatherDetail;
use Log;

class Kernel extends ConsoleKernel
{
    use Common;

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //Commands\CloneSchoolData::class,
        \App\Console\Commands\UpdateWeatherDetail::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule){
        // Run schedule command for cloning school data after change every new curriculum year
        // if(date('d-m') == '15-07'){
        //     $schedule->command('clone:school-data');
        // }

        // Update Curriculum Year In Global Configurations
        // if(date('d-m h:i') == '25-11 00:00'){
        //     $this->UpdateGlobalConfigurationCurriculumYear();
        // }

        // Send Reminder Email for every schools
        // $CronJobController = new CronJobController();
        // $CronJobController->SendRemainderUploadStudentNewSchoolCurriculumYear();
        
        $schedule->command('WeatherDetail')->everyMinute();

        //$schedule->command('update:WeatherDetail')->everySeconds(0.5);

        // Run Automatic queue command run into background process
        \Artisan::call('queue:work');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}