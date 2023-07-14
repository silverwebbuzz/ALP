<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Constants\DbConstant As cn;
use App\Models\User;
use Log;
use App\Http\Controllers\UsersController;
use App\Events\UserActivityLog;

class DeleteStudentDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $studentId,$schoolId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){
        Log::info('Job Start Delete Student Data');
        $UserController = new UsersController();
        $UserController->removeStudentData($this->studentId);
        Log::info('Job End Delete Student Data');

    }
}
