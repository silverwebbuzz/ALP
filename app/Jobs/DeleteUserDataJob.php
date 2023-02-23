<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Constants\DbConstant As cn;
use App\Http\Controllers\CronJobController;
use Log;

class DeleteUserDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $CronJobController;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($CronJobController)
    {
       $this->CronJobController = $CronJobController;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Job Start Delete User Data');
        $CronJobController = new CronJobController();
        $CronJobController->RemoveUserDataFromAllTables($this->CronJobController);
        Log::info('Job End Delete User Data');
    }
}
