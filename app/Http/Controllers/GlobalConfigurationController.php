<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Common;
use App\Exceptions\CustomException;
use App\Models\GlobalConfiguration;
use App\Models\CurriculumYear;
use App\Constants\DbConstant as cn;
use App\Http\Controllers\CronJobController;
use App\Helpers\Helper;
use App\Events\UserActivityLog;

class GlobalConfigurationController extends Controller
{
    use Common;

    protected $CronJobController;

    public function __construct(){
        $this->CronJobController = new CronJobController;
    }

    /**
     * USE : Get & Update Global Configurations
     */
    public function ConfigurationUpdate(Request $request){
        $postdata ='';
        $arrayFromTo = [];
        if ($request->isMethod('GET')){
            $GlobalConfiguration = GlobalConfiguration::all();
            $getCurriculumYear = CurriculumYear::all();
            $ConfigurationArray = [];
            if(isset($GlobalConfiguration) && !empty($GlobalConfiguration)){
                foreach($GlobalConfiguration as $configuration){
                    $ConfigurationArray[$configuration->key] = $configuration->value;
                }
            }
            return view('backend.settings.global_configuration',compact('ConfigurationArray','getCurriculumYear'));
        }

        // If Request Type is Patch and update the key bay config value
        if($request->isMethod('PATCH')){
            $isRunLearningProgressCronJob = false;
            $struggling = json_decode(Helper::getGlobalConfiguration('struggling'),TRUE);
            $beginning = json_decode(Helper::getGlobalConfiguration('beginning'),TRUE);
            $approaching = json_decode(Helper::getGlobalConfiguration('approaching'),TRUE);
            $proficient = json_decode(Helper::getGlobalConfiguration('proficient'),TRUE);
            $advanced = json_decode(Helper::getGlobalConfiguration('advanced'),TRUE);
            if(
                Helper::getGlobalConfiguration('study_status_master') != $request->study_status_master ||
                Helper::getGlobalConfiguration('min_no_question_per_study_progress') != $request->min_no_question_per_study_progress ||
                Helper::getGlobalConfiguration('question_window_size_of_learning_objective') != $request->question_window_size_of_learning_objective ||
                $struggling['from'] != $request->struggling_from ||
                $struggling['to'] != $request->struggling_to ||
                Helper::getGlobalConfiguration('struggling_color') != $request->struggling_color ||
                $beginning['from'] != $request->beginning_from ||
                $beginning['to'] != $request->beginning_to ||
                Helper::getGlobalConfiguration('beginning_color') != $request->beginning_color ||
                $approaching['from'] != $request->approaching_from ||
                $approaching['to'] != $request->approaching_to ||
                Helper::getGlobalConfiguration('approaching_color') != $request->approaching_color ||
                $proficient['from'] != $request->proficient_from ||
                $proficient['to'] != $request->proficient_to ||
                Helper::getGlobalConfiguration('proficient_color') != $request->proficient_color ||
                $advanced['from'] != $request->advanced_from ||
                $advanced['to'] != $request->advanced_to ||
                Helper::getGlobalConfiguration('advanced_color') != $request->advanced_color ||
                Helper::getGlobalConfiguration('incomplete_color') != $request->incomplete_color
            ){
                $isRunLearningProgressCronJob = true;
            }

            foreach(config()->get('GlobalConfigurationKey') as $configKey){                
                if(GlobalConfiguration::where(cn::GLOBAL_CONFIGURATION_KEY_COL, $configKey)->exists()){
                    if(in_array($configKey,array('question_difficulty_easy','question_difficulty_medium','question_difficulty_hard','question_difficulty_level1','question_difficulty_level2','question_difficulty_level3','question_difficulty_level4','question_difficulty_level5'))){
                        $postdata = [
                            'from' => $request->{$configKey.'_from'},
                            'to'   => $request->{$configKey.'_to'},
                            'color' => $request->{$configKey.'_color'},
                        ];
                    }else{
                        $postdata = [
                            'from' => $request->{$configKey.'_from'},
                            'to'   => $request->{$configKey.'_to'},
                        ];
                    }
                    if(in_array($configKey,array('question_difficulty_level1','question_difficulty_level2','question_difficulty_level3','question_difficulty_level4','question_difficulty_level5','question_difficulty_easy','question_difficulty_medium','question_difficulty_hard','struggling','beginning','approaching','proficient','advanced'))){
                        GlobalConfiguration::where(cn::GLOBAL_CONFIGURATION_KEY_COL, $configKey)->Update([
                            cn::GLOBAL_CONFIGURATION_KEY_COL => $configKey,
                            cn::GLOBAL_CONFIGURATION_VALUE_COL => json_encode($postdata)
                        ]);
                    }else{
                        GlobalConfiguration::where(cn::GLOBAL_CONFIGURATION_KEY_COL, $configKey)->Update([
                            cn::GLOBAL_CONFIGURATION_KEY_COL => $configKey,
                            cn::GLOBAL_CONFIGURATION_VALUE_COL => $request->{$configKey}
                        ]);
                    }
                }else{
                    if(in_array($configKey,array('question_difficulty_easy','question_difficulty_medium','question_difficulty_hard','struggling','beginning','approaching','proficient','advanced'))){
                        if(in_array($configKey,array('question_difficulty_level1','question_difficulty_level2','question_difficulty_level3','question_difficulty_level4','question_difficulty_level5','question_difficulty_easy','question_difficulty_medium','question_difficulty_hard','question_difficulty_level1','question_difficulty_level2','question_difficulty_level3','question_difficulty_level4','question_difficulty_level5'))){
                            $postdata = [
                                'from' => $request->{$configKey.'_from'},
                                'to'   => $request->{$configKey.'_to'},
                                'color' => $request->{$configKey.'_color'},
                            ];
                        }else{
                            $postdata = [
                                'from' => $request->{$configKey.'_from'},
                                'to'   => $request->{$configKey.'_to'},
                            ];
                        }

                        GlobalConfiguration::Create([
                            cn::GLOBAL_CONFIGURATION_KEY_COL => $configKey,
                            cn::GLOBAL_CONFIGURATION_VALUE_COL => json_encode($postdata)
                        ]);
                    }else{
                        if(is_array($request->{$configKey})){
                            GlobalConfiguration::Create([
                                cn::GLOBAL_CONFIGURATION_KEY_COL => $configKey,
                                cn::GLOBAL_CONFIGURATION_VALUE_COL => json_encode($request->{$configKey})
                            ]);
                        }else{
                            GlobalConfiguration::Create([
                                cn::GLOBAL_CONFIGURATION_KEY_COL => $configKey,
                                cn::GLOBAL_CONFIGURATION_VALUE_COL => $request->{$configKey}
                            ]);
                        }
                    }
                }
            }

            /**
             * Check status is true then run cron job for learning progress report
             */
            if($isRunLearningProgressCronJob == true){
                $this->CronJobController->UpdateLearningProgressJob();
            }
        }
        return redirect('global-configuration')->with('success_msg', __('languages.global_configuration_updated_successfully'));
    }
}
