<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Question;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Constants\DbConstant as cn;
use App\Traits\Common;
use App\Helpers\Helper;
use Log;
use App\Events\UserActivityLog;

class UpdateQuestionAIDifficultyColumnJob implements ShouldQueue
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
        //$QuestionLists = Question::skip(0)->take(5000)->get();
        $QuestionLists = Question::withTrashed()->get();
        if(!empty($QuestionLists)){
            foreach($QuestionLists as $question){
                if($this->isSeedQuestion($question->naming_structure_code)){
                    //Get all Question Like Seeder Question naming Structure
                    $getSimilarQuestionsNamingStructure = Question::withTrashed()->where('naming_structure_code','Like','%'.$question->naming_structure_code.'%')->get();
                    if(!empty($getSimilarQuestionsNamingStructure)){
                        foreach($getSimilarQuestionsNamingStructure as $similarQuestion){
                            // Get Non Seeder Questions
                            $preConfigureDifficultyData = PreConfigurationDiffiltyLevel::where('difficulty_level',$question->dificulaty_level)->first();
                            $postData = [
                                cn::QUESTION_PRE_CONFIGURE_DIFFICULTY_VALUE => $preConfigureDifficultyData->title,
                                cn::QUESTION_AI_DIFFICULTY_VALUE => $preConfigureDifficultyData->title,
                            ];
                            $updateQuestion = Question::withTrashed()->find($similarQuestion->id)->update($postData);
                            if($updateQuestion){
                                Log::info('Question ID: '.$similarQuestion->id);
                                $i+=1;
                            }
                        }
                    }
                }
            }
            Log::info('Total : '.$i);
        }
    }
}
