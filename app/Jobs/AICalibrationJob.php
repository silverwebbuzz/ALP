<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\AI_Calibration\AI_CalibrationController;
use Log;
use App\Events\UserActivityLog;

class AICalibrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $AI_CalibrationController;
    public $params;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->AI_CalibrationController = new AI_CalibrationController;
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $params = $this->params;
        ini_set('max_execution_time', -1);
        Log::info('AI-Calibration Process Start');
        $this->AI_CalibrationController->GenerateAICalibration($params);
        Log::info('AI-Calibration Process Complete');
    }
}
