<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use App\Traits\Common;
use App\Constants\DbConstant as cn;
use App\Models\AttemptExams;

class UpdateCountUsedQuestionAnswerJob implements ShouldQueue
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
        Log::info('Update Count Used Question Answer Job Start');
        $AttemptExamData = AttemptExams::all();
        if(isset($AttemptExamData) && !empty($AttemptExamData)){
            foreach($AttemptExamData as $AttemptData){
                if(isset($AttemptData->question_answers) && !empty($AttemptData->question_answers)){
                    $question_answers = json_decode($AttemptData->question_answers,TRUE);
                    if(isset($question_answers) && !empty($question_answers)){
                        foreach($question_answers as $data){
                            // Update Question count in database how many times used this question
                            $this->UpdateUsedQuestionAnswerCounts($data['question_id'],'question_count');

                            // Update Answer count in database how many times selected this answer by students
                            $this->UpdateUsedQuestionAnswerCounts($data['question_id'],'answer_count',($data['answer'] ?? 5));
                        }
                    }
                }                
            }
        }
        Log::info('Update Count Used Question Answer Job Completed Successfully');
    }
}
