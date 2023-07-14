<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\CronJobController;
use Log;
use App\Events\UserActivityLog;

class UpdateLearningProgressReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $CronJobController;
    protected $AttemptedStudentIds;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($AttemptedStudentIds)
    {
        $this->CronJobController = new CronJobController;
        $this->AttemptedStudentIds = $AttemptedStudentIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('max_execution_time', -1);
        Log::info('Update Learning Process Report Start');
        $this->CronJobController->UpdateLearningProgress($this->AttemptedStudentIds);
        Log::info('Update Learning Process Report Completed successfully');
    }
}
