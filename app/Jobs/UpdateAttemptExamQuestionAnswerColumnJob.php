<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Constants\DbConstant as cn;
use App\Traits\Common;
use App\Models\AttemptExams;
use Log;
use App\Events\UserActivityLog;

class UpdateAttemptExamQuestionAnswerColumnJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Common;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('max_execution_time', -1);
        $i = 1;
        $AttemptedExamsList = AttemptExams::all();
        if(!empty($AttemptedExamsList)){
            foreach($AttemptedExamsList as $attemptExam){
                $jsonAnswer = $this->setQuestionDifficultyTypeAndDifficultyValue($attemptExam->question_answers);
                $jsonFirstAttempt = !empty($attemptExam->attempt_first_trial) ? $this->setQuestionDifficultyTypeAndDifficultyValue($attemptExam->attempt_first_trial) : NULL;
                $jsonSecondAttempt = !empty($attemptExam->attempt_second_trial) ? $this->setQuestionDifficultyTypeAndDifficultyValue($attemptExam->attempt_second_trial) : NULL;
                $updateAttemptAnswer =  AttemptExams::find($attemptExam->id)->update([
                                            'question_answers' => $jsonAnswer,
                                            'attempt_first_trial' => $jsonFirstAttempt,
                                            'attempt_second_trial' => $jsonSecondAttempt
                                        ]);
                if($updateAttemptAnswer){
                    Log::info('Attempt ID: '.$attemptExam->id);
                    $i+=1;
                }else{
                    Log::info('Something went wrong from Attempt Answer ID: '.$attemptExam->id);
                }
            }
            Log::info("Total Record is Updated:".$i);
        }
    }
}
