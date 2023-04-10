<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;   
use App\Traits\Common;
use App\Models\GradeSchoolMappings;
use App\Models\GradeClassMapping;
use App\Models\ClassPromotionHistory;
use App\Constants\DbConstant As cn;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Models\Grades;
use App\Models\Exam;
use App\Models\PreConfigurationDiffiltyLevel;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\ResponseFormat;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\Question;
use App\Models\TeachersClassSubjectAssign;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\ExamConfigurationsDetails;
use App\Http\Services\AIApiService;
use App\Helpers\Helper;
use DB;
use App\Models\ExamSchoolMapping;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CronJobController;
use App\Models\AttemptExams;
use App\Models\UploadDocuments;
use App\Models\LearningUnitOrdering;
use App\Models\LearningObjectiveOrdering;
use App\Models\LearningObjectivesSkills;
use App\Events\UserActivityLog;

class RealTimeAIQuestionGeneratorController extends Controller
{
    use common, ResponseFormat;
    protected $currentUserSchoolId, $repeated_rate_config, $CronJobController, $CurrentCurriculumYearId;
    
    public function __construct(){
        $this->AIApiService = new AIApiService();
        $this->CronJobController = new CronJobController;
        $this->CurrentCurriculumYearId = $this->getGlobalConfiguration('current_curriculum_year');
        
        // Store global variable into current user school id
        $this->currentUserSchoolId = null;
        $this->repeated_rate_config = Helper::getGlobalConfiguration('repeated_rate') ?? 0.5 ;
        $this->middleware(function ($request, $next) {
            $this->currentUserSchoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            return $next($request);
        });
    }

    /**
     * USE : Landing Page on Create Self-Learning Testing Zone
     */
    public function CreateSelfLearningTest(Request $request){
        if($request->isMethod('get')){
            $difficultyLevels = PreConfigurationDiffiltyLevel::all();
            $RequiredQuestionPerSkill = [];
            $RequiredQuestionPerSkill = [
                'minimum_question_per_skill' => $this->getGlobalConfiguration('no_of_questions_per_learning_skills'),
                'maximum_question_per_skill' => $this->getGlobalConfiguration('max_no_question_per_learning_objectives')
            ];
            // Get Strand List
            $strandsList = Strands::all();
            $learningObjectivesConfiguration = array();
            if(!empty($strandsList)){
                $LearningUnits = collect($this->GetLearningUnits($strandsList[0]->{cn::STRANDS_ID_COL}));
                $LearningObjectives = $this->GetLearningObjectives($LearningUnits->pluck('id')->toArray());
            }
            return view('backend.student.real_time_generate_question.create_self_learning_test',compact('difficultyLevels','strandsList','LearningUnits','LearningObjectives','RequiredQuestionPerSkill','learningObjectivesConfiguration'));
        }
    }

    /**
     * USE : Generate Question for self-learning Test
     */
    public function GenerateQuestionSelfLearningTest(Request $request){
        if(isset($request)){
            if(!isset($request->learning_unit)){
                return $this->sendError('Please select learning objectives', 404);
            }
            $examConfigurationsData = json_encode($request->all());
            $examLanguage = (isset($request->language)) ? $request->language : 'en';
            $difficultyLevels = PreConfigurationDiffiltyLevel::all();
            $result = array();
            $minimumQuestionPerSkill = Helper::getGlobalConfiguration('no_of_questions_per_learning_skills') ?? 2 ;
            $learningUnitArray = array();
            $coded_questions_list_all = array();
            $coded_questions_list = array();
            $difficulty_lvl = $request->difficulty_lvl;
            $selected_levels = array();
            if(isset($difficulty_lvl) && !empty($difficulty_lvl)){
                foreach($difficulty_lvl as $difficulty_value){
                    $selected_levels[] = $difficulty_value-1;
                }
            }
            $no_of_questions = 10;
            if(isset($request->total_no_of_questions) && !empty($request->total_no_of_questions)){
                $no_of_questions = $request->total_no_of_questions;
            }

            if($request->self_learning_test_type==1){
                $QuestionType = array(2,3);
            }else{
                $QuestionType = array(1);
            }

            $MainSkillArray = array();
            if(isset($request->learning_unit) && !empty($request->learning_unit)){
                foreach($request->learning_unit as $learningUnitId => $learningUnitData){
                    $learningObjectiveQuestionArray = array();
                    if(isset($learningUnitData['learning_objective']) && !empty($learningUnitData['learning_objective'])){
                        foreach($learningUnitData['learning_objective'] as $LearningObjectiveId => $data){
                            $objective_mapping_id = StrandUnitsObjectivesMappings::whereIn('strand_id',$request->strand_id)
                                                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$learningUnitId)
                                                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$LearningObjectiveId)
                                                        ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
                            $QuestionSkill = Question::whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$objective_mapping_id)
                                                //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                                ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType)
                                                ->groupBy(cn::QUESTION_E_COL)
                                                ->pluck(cn::QUESTION_E_COL)
                                                ->toArray();
                            if(isset($QuestionSkill) && !empty($QuestionSkill)){
                                $GetQuestionPerObjective = $data['get_no_of_question_learning_objectives'];
                                $qLoop = 0;
                                $qSize = 0;
                                while($qLoop <= $GetQuestionPerObjective){
                                    if($qSize >= $GetQuestionPerObjective){
                                        break;
                                    }
                                    foreach($QuestionSkill as $skillName){
                                        if($qSize >= $GetQuestionPerObjective){
                                            break;
                                        }
                                        $MainSkillArray[] =  array(
                                            'qloop' => $qLoop,
                                            'learning_unit_id' => $learningUnitId,
                                            'learning_objective_id' => $LearningObjectiveId,
                                            'objective_mapping_ids' => $objective_mapping_id,
                                            'learning_objective_skill' => $skillName
                                        );
                                        $qSize++;
                                    }

                                    // Get the learning objectives extra skills
                                    $GetExtraExtraSkillLearningObjectives = LearningObjectivesSkills::where(cn::LEARNING_OBJECTIVES_SKILLS_LEARNING_OBJECTIVE_ID_COL,$LearningObjectiveId)->get();
                                    if(isset($GetExtraExtraSkillLearningObjectives) && !empty($GetExtraExtraSkillLearningObjectives)){
                                        $GetExtraExtraSkillLearningObjectives = $GetExtraExtraSkillLearningObjectives->pluck(cn::LEARNING_OBJECTIVES_SKILLS_LEARNING_SKILL_COL)->toArray();
                                        foreach($GetExtraExtraSkillLearningObjectives as $LearningObjectiveExtraSkill){
                                            $ExplodeSkillCode = explode('-',$LearningObjectiveExtraSkill);
                                            $ExtraSkillQuestionExists = Question::where(cn::QUESTION_NAMING_STRUCTURE_CODE_COL, 'like', '%'.$LearningObjectiveExtraSkill.'%')
                                                                        ->where(cn::QUESTION_E_COL,end($ExplodeSkillCode))
                                                                        //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                                                        ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType)
                                                                        ->count();
                                            if(isset($ExtraSkillQuestionExists) && !empty($ExtraSkillQuestionExists)){
                                                if($qSize >= $GetQuestionPerObjective){
                                                    break;
                                                }
                                                $ExtraSkillStrand = Strands::where('code',$ExplodeSkillCode[1])->first();
                                                $ExtraSkillLearningsUnit = LearningsUnits::where(cn::LEARNING_UNITS_CODE_COL,substr($ExplodeSkillCode[2],0,2))->where('stage_id','<>',3)->first();
                                                $ExtraSkillLearningsObjectives = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_CODE_COL,substr($ExplodeSkillCode[2],2))
                                                                                ->where('learning_unit_id',$ExtraSkillLearningsUnit->id)
                                                                                ->where('stage_id','<>',3)
                                                                                ->first();
                                                $ExtraSkillObjectiveMappingId = StrandUnitsObjectivesMappings::where('strand_id',$ExtraSkillStrand->id)
                                                                                ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$ExtraSkillLearningsUnit->id)
                                                                                ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$ExtraSkillLearningsObjectives->id)
                                                                                ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)
                                                                                ->toArray();
                                                $MainSkillArray[] =  array(
                                                    'qloop'                     => $qLoop,
                                                    'learning_unit_id'          => $ExtraSkillLearningsUnit->id,
                                                    'learning_objective_id'     => $ExtraSkillLearningsObjectives->id,
                                                    'objective_mapping_ids'     => $ExtraSkillObjectiveMappingId,
                                                    'learning_objective_skill'  => $ExplodeSkillCode[3]
                                                );
                                                $qSize++;
                                            }
                                        }
                                    }
                                    $qLoop++;
                                }
                            }
                        }
                    }
                }
            }

            if(isset($MainSkillArray) && !empty($MainSkillArray)){
                foreach($MainSkillArray as $currentSkillArrayId => $skillArray){
                    $questionArray = Question::whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$skillArray['objective_mapping_ids'])
                                        //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                        ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType)
                                        ->where(cn::QUESTION_E_COL,$skillArray['learning_objective_skill'])
                                        ->get()->toArray();
                    if(!empty($questionArray)){
                        foreach($questionArray as $question_key => $question_value){
                            $countNoOfAnswer = $this->CountNoOfAnswerByQuestionId($question_value['id']);
                            $coded_questions_list[] =   array(
                                                            $question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],
                                                            floatval($question_value['PreConfigurationDifficultyLevel']->title),
                                                            0,
                                                            $countNoOfAnswer
                                                        );
                        }
                    }
                    if(isset($coded_questions_list) && !empty($coded_questions_list)){
                        $assigned_questions_list = [];
                        $result_list = [];
                        $requestPayload =   new \Illuminate\Http\Request();
                        $studentAbilities = Auth::user()->{cn::USERS_OVERALL_ABILITY_COL} ?? 0;
                        $requestPayload =   $requestPayload->replace([
                                                'initial_ability'           => floatval($studentAbilities),
                                                'assigned_questions_list'   => $assigned_questions_list,
                                                'result_list'               => $result_list,
                                                'coded_questions_list'      => $coded_questions_list,
                                                'repeated_rate'             => $this->repeated_rate_config
                                            ]);
                        $response = $this->AIApiService->Real_Time_Assign_Question_N_Estimate_Ability($requestPayload);
                        if(isset($response) && !empty($response)){
                            $assigned_questions_list[] = $response[0];
                            $responseQuestionCodes = ($response[0][0]);                            
                            $Question = Question::with(['answers','objectiveMapping'])
                                            ->where(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodes)
                                            ->first();
                            if(isset($Question) && !empty($Question)){
                                unset($MainSkillArray[$currentSkillArrayId]);
                                $countMainSkillArray = count($MainSkillArray);
                                $encodedMainSkillArray = json_encode($MainSkillArray);
                                $assigned_questions_list = json_encode($assigned_questions_list);
                                $result_list = json_encode($result_list);
                                $QuestionNo = 1;
                                $QuestionResponse['question_html'] = (string)View::make('backend.student.real_time_generate_question.question_html',compact('request','examConfigurationsData','Question','examLanguage','assigned_questions_list','encodedMainSkillArray','result_list','QuestionNo','countMainSkillArray'));
                                return $this->sendResponse($QuestionResponse);exit;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * USE : Check student result
     */
    public function CheckStudentAnswerResult($QuestionId,$SelectedAnswer){
        $QuestionAnswerDetail = Question::where(cn::QUESTION_TABLE_ID_COL,$QuestionId)->with('answers')->first();
        if(isset($QuestionAnswerDetail)){
            if($QuestionAnswerDetail->answers->{'correct_answer_en'} == $SelectedAnswer){
                $isAnswer = true;
            }else{
                $isAnswer = false;
            }
        }
        return $isAnswer;
    }

    /**
     * USE : Get Next Question
     */
    public function GenerateQuestionSelfLearningTestNextQuestion(Request $request){
        if(isset($request->encodedMainSkillArray) && !empty($request->encodedMainSkillArray)){
            $examLanguage = (isset($request->language)) ? $request->language : 'en';
            if($request->self_learning_test_type==1){
                $QuestionType = array(2,3);
            }else{
                $QuestionType = array(1);
            }
            $assigned_questions_list = json_decode($request->assigned_questions_list) ?? [];
            $assignedQuestionCodes = array();
            $assignedQuestionCodes = array_column($assigned_questions_list,0);

            $AttemptedQuestionAnswers = json_decode($request->AttemptedQuestionAnswers);
            $AttemptedQuestionResult = $this->CheckStudentAnswerResult($request->currentQuestion,$request->answer);
            $result_list = json_decode($request->result_list);
            $result_list[] = $AttemptedQuestionResult;
            $AttemptedQuestionAnswers[] = array(
                                            'question_id' => $request->currentQuestion,
                                            'answer' => $request->answer,
                                            'answer_result' => $AttemptedQuestionResult,
                                            'language' => 'en',
                                            'duration_second' => $request->current_question_taking_timing
                                        );
            $MainSkillArray = json_decode($request->encodedMainSkillArray);
            if(isset($MainSkillArray) && !empty($MainSkillArray)){
                foreach($MainSkillArray as $currentSkillArrayId => $skillArray){
                    $questionArray = Question::whereNotIn(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$assignedQuestionCodes)
                                    ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$skillArray->objective_mapping_ids)
                                    //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                    ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType)
                                    ->where(cn::QUESTION_E_COL,$skillArray->learning_objective_skill)
                                    ->get()
                                    ->toArray();
                    if(!empty($questionArray)){
                        foreach($questionArray as $question_key => $question_value){
                            $countNoOfAnswer = $this->CountNoOfAnswerByQuestionId($question_value['id']);
                            $coded_questions_list[] =   array(
                                                            $question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],
                                                            floatval($question_value['PreConfigurationDifficultyLevel']->title),
                                                            0,
                                                            $countNoOfAnswer
                                                        );
                        }
                    }
                    if(isset($coded_questions_list) && !empty($coded_questions_list)){
                        $requestPayload =   new \Illuminate\Http\Request();
                        $studentAbilities = Auth::user()->{cn::USERS_OVERALL_ABILITY_COL} ?? 0;
                        $requestPayload =   $requestPayload->replace([
                                                'initial_ability'           => floatval($studentAbilities),
                                                'assigned_questions_list'   => $assigned_questions_list,
                                                'result_list'               => $result_list,
                                                'coded_questions_list'      => $coded_questions_list,
                                                'repeated_rate'             => $this->repeated_rate_config
                                            ]);
                        $response = $this->AIApiService->Real_Time_Assign_Question_N_Estimate_Ability($requestPayload);
                        if(isset($response) && !empty($response)){
                            $assigned_questions_list[] = $response[0];
                            $responseQuestionCodes = ($response[0][0]);
                            $Question = Question::with(['answers','objectiveMapping'])
                                            ->where(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodes)
                                            ->first();
                            if(isset($Question) && !empty($Question)){
                                unset($MainSkillArray->$currentSkillArrayId);
                                $countMainSkillArray = count((array)$MainSkillArray);
                                $encodedMainSkillArray = json_encode($MainSkillArray);
                                $assigned_questions_list = json_encode($assigned_questions_list);
                                $result_list = json_encode($result_list);
                                $encodedAttemptedQuestionAnswers = json_encode($AttemptedQuestionAnswers);
                                $QuestionNo = ($request->QuestionNo+1);
                                $QuestionResponse['question_html'] = (string)View::make('backend.student.real_time_generate_question.next_question_html',compact('request','Question','examLanguage','assigned_questions_list','encodedMainSkillArray','result_list','encodedAttemptedQuestionAnswers','QuestionNo','countMainSkillArray'));
                                return $this->sendResponse($QuestionResponse);exit;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * USE : Store Self learning detail into database
     */
    public function SaveSelfLearningTest(Request $request){
        $AttemptedQuestionAnswers = json_decode($request->AttemptedQuestionAnswers);
        $AttemptedQuestionResult = $this->CheckStudentAnswerResult($request->currentQuestion,$request->answer);
        $result_list = json_decode($request->result_list);
        $AttemptedQuestionAnswers[] = (object) array(
                                        'question_id' => $request->currentQuestion,
                                        'answer' => $request->answer,
                                        'answer_result' => $AttemptedQuestionResult,
                                        'language' => 'en',
                                        'duration_second' => $request->current_question_taking_timing
                                    );
        
        // Get QuestionIds from attempted questions
        $questionIds = implode(",",array_column($AttemptedQuestionAnswers,'question_id'));
        $timeduration = null;
        if($request->self_learning_test_type == 2){
            $TotalTime = 0;
            $QuestionPerSeconds = $this->getGlobalConfiguration('default_second_per_question');
            if(isset($QuestionPerSeconds) && !empty($QuestionPerSeconds) && !empty($questionIds)){
                $totalSeconds = (count(explode(",",$questionIds)) * $QuestionPerSeconds);
                $TotalTime = gmdate("H:i:s", $totalSeconds);
                $timeduration = ($TotalTime) ? $this->timeToSecond($TotalTime): null;
            }
        }

        $examData = [
            cn::EXAM_CURRICULUM_YEAR_ID_COL                 => $this->GetCurriculumYear(), // "CurrentCurriculumYearId" Get value from Global Configuration
            cn::EXAM_CALIBRATION_ID_COL                     => $this->GetCurrentAdjustedCalibrationId(),
            cn::EXAM_TYPE_COLS                              => 1,
            cn::EXAM_REFERENCE_NO_COL                       => $this->GetMaxReferenceNumberExam(1,$request->self_learning_test_type),
            cn::EXAM_TABLE_TITLE_COLS                       => $this->createTestTitle(),
            cn::EXAM_TABLE_FROM_DATE_COLS                   => Carbon::now(),
            cn::EXAM_TABLE_TO_DATE_COLS                     => Carbon::now(),
            cn::EXAM_TABLE_RESULT_DATE_COLS                 => Carbon::now(),
            cn::EXAM_TABLE_PUBLISH_DATE_COL                 => Carbon::now(),
            cn::EXAM_TABLE_TIME_DURATIONS_COLS              => $timeduration,
            cn::EXAM_TABLE_QUESTION_IDS_COL                 => ($questionIds) ?  $questionIds : null,
            cn::EXAM_TABLE_STUDENT_IDS_COL                  => $this->LoggedUserId(),
            cn::EXAM_TABLE_SCHOOL_COLS                      => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
            cn::EXAM_TABLE_LEARNING_OBJECTIVES_CONFIGURATIONS_COL => $request->examConfigurationsData ?? null,
            cn::EXAM_TABLE_IS_UNLIMITED                     => ($request->self_learning_test_type == 1) ? 1 : 0,
            cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL      => $request->self_learning_test_type,
            cn::EXAM_TABLE_CREATED_BY_COL                   => $this->LoggedUserId(),
            cn::EXAM_TABLE_CREATED_BY_USER_COL              => 'student',
            cn::EXAM_TABLE_STATUS_COLS                      => 'publish',
           
        ];
        $exams = Exam::create($examData);

        $this->UserActivityLog(
            Auth::user()->id,
            '<p>'.Auth::user()->DecryptNameEn.' '.__('activity_history.new_test_created').'.'.
            '<p>'.__('activity_history.title_is').$exams->title.'</p>'
        );
        if($exams){
            // Create exam school mapping
            ExamSchoolMapping::create([
                cn::EXAM_SCHOOL_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL => $exams->id,
                cn::EXAM_SCHOOL_MAPPING_STATUS_COL => 'publish'
            ]);

            // find student overall ability from AI-API
            if(isset($AttemptedQuestionAnswers) && !empty($AttemptedQuestionAnswers)){
                $NoOfCorrectAnswers = 0;
                $NoOfWrongAnswers = 0;
                foreach ($AttemptedQuestionAnswers as $key => $Question) {
                    $QuestionId = $Question->question_id;
                    $answer = $Question->answer;
                    if($Question->answer_result){
                        $NoOfCorrectAnswers = ($NoOfCorrectAnswers + 1);
                        $apiData['questions_results'][] = true;
                    }else{
                        $NoOfWrongAnswers = ($NoOfWrongAnswers + 1);
                        $apiData['questions_results'][] = false;
                    }

                    // Get Questions Answers and difficulty level
                    $responseData = $this->GetQuestionNumOfAnswerAndDifficultyValue($Question->question_id,$exams->{cn::EXAM_CALIBRATION_ID_COL});
                    $apiData['num_of_ans_list'][] = $responseData['noOfAnswers'];
                    $apiData['difficulty_list'][] = $responseData['difficulty_value'];
                    $apiData['max_student_num'] = 1;

                }
            }

            $StudentAbility = '';
            if(!empty($apiData)){
                // Get the student ability from calling AIApi
                $StudentAbility = $this->GetAIStudentAbility($apiData);
            }

            $PostData = [
                cn::ATTEMPT_EXAMS_CURRICULUM_YEAR_ID_COL        => $this->GetCurriculumYear(),
                cn::ATTEMPT_EXAMS_CALIBRATION_ID_COL            => $this->GetCurrentAdjustedCalibrationId(),
                cn::ATTEMPT_EXAMS_EXAM_ID                       => $exams->id,
                cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID            => $this->LoggedUserId(),
                cn::ATTEMPT_EXAMS_STUDENT_GRADE_ID              => Auth::user()->grade_id,
                cn::ATTEMPT_EXAMS_STUDENT_CLASS_ID              => Auth::user()->class_id,
                cn::ATTEMPT_EXAMS_LANGUAGE_COL                  => 'en',
                cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL           => (!empty($AttemptedQuestionAnswers)) ? json_encode($AttemptedQuestionAnswers) : null,
                cn::ATTEMPT_EXAMS_WRONG_ANSWER_COL              => '',
                //cn::ATTEMPT_EXAMS_ATTEMPT_FIRST_TRIAL_COL     => $request->attempt_first_trial_data_new,
                //cn::ATTEMPT_EXAMS_ATTEMPT_SECOND_TRIAL_COL    => $wrong_ans_list_json,
                cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS         => $NoOfCorrectAnswers,
                cn::ATTEMPT_EXAMS_TOTAL_WRONG_ANSWERS           => $NoOfWrongAnswers,
                cn::ATTEMPT_EXAMS_EXAM_TAKING_TIMING            => $request->exam_taking_timing,
                cn::ATTEMPT_EXAMS_STUDENT_ABILITY_COL           => ($StudentAbility!='') ? $StudentAbility : null,
                cn::ATTEMPT_EXAMS_SERVER_DETAILS_COL            => json_encode($this->serverData()) ?? null,
                cn::ATTEMPT_EXAMS_BEFORE_EXAM_SURVEY_COL        => $request->before_emoji_id ?? 0,
                cn::ATTEMPT_EXAMS_AFTER_EXAM_SURVEY_COL         => $request->after_emoji_id ?? 0,
            ];
            $save = AttemptExams::create($PostData);
            $this->UserActivityLog(
                Auth::user()->id,
                '<p>'.Auth::user()->DecryptNameEn.' '.__('activity_history.exam_attempted').'.'.
                '<p>'.__('activity_history.title_is').$exams->title.'.'.'</p>'.
                '<p>'.__('activity_history.exam_reference_is').$exams->reference_no.'</p>'.
                '<p>'.__('activity_history.exam_submitting_time_is').' '.date('Y/m/d h:i:s a', time())
            );
            if($save){
                //Update Column Is_my_teaching_sync
                Exam::find($exams->id)->update([cn::EXAM_TABLE_IS_TEACHING_REPORT_SYNC =>'true']);
                
                /** Start Update overall ability for the student **/
                if($exams->exam_type == 3 || ($exams->exam_type == 1 && $exams->self_learning_test_type == 2)){
                    $this->CronJobController->UpdateStudentOverAllAbility();
                }

                /** Update My Teaching Table Via Cron Job */
                $this->CronJobController->UpdateMyTeachingTable(Auth::user()->{cn::USERS_SCHOOL_ID_COL}, $exams->id);

                /** Update Student Credit Points via cron job */
                $this->CronJobController->UpdateStudentCreditPoints($exams->id, Auth::user()->{cn::USERS_ID_COL});

                // Start Learning Progress Learning Unit Job
                $this->CronJobController->UpdateLearningProgressJob(Auth::user()->{cn::USERS_ID_COL});
                // End Learning Progress Learning Unit Job
                
                /** End Update overall ability for the student **/
                $this->StoreAuditLogFunction('','Exams','','','Attempt Exam',cn::EXAM_TABLE_NAME,'');

                $response['redirectUrl'] = 'exams/result/'.$exams->id.'/'.Auth::user()->{cn::USERS_ID_COL};
                return $this->sendResponse($response);exit;
            }
        }
    }

    /***
     * USE : Find the student Ability using AI API
     */
    public function GetAIStudentAbility($apiData){
        $StudentAbility = '';
        $requestPayload = new \Illuminate\Http\Request();
        $requestPayload = $requestPayload->replace([
            'questions_results'=> array($apiData['questions_results']),
            'num_of_ans_list' => $apiData['num_of_ans_list'],
            'difficulty_list' => array_map('floatval', $apiData['difficulty_list']),
            'max_student_num' => 1
        ]);
        $AIApiResponse = $this->AIApiService->getStudentAbility($requestPayload);
        if(isset($AIApiResponse) && !empty($AIApiResponse)){
            $StudentAbility = $AIApiResponse[0];
        }
        return $StudentAbility;
    }

    /**
     * USE : Get Next Question
     */
    public function GenerateQuestionSelfLearningTestChangeLanguage(Request $request){
        if(isset($request->encodedMainSkillArray) && !empty($request->encodedMainSkillArray)){
            $examLanguage = (isset($request->language)) ? $request->language : 'en';
            if($request->self_learning_test_type==1){
                $QuestionType = array(2,3);
            }else{
                $QuestionType = array(1);
            }

            $AttemptedQuestionAnswers = json_decode($request->AttemptedQuestionAnswers);
            $result_list = json_decode($request->result_list);
            $MainSkillArray = json_decode($request->encodedMainSkillArray);
            $Question = Question::with(['answers','objectiveMapping'])->where('id',$request->currentQuestion)->first();
            if(isset($Question) && !empty($Question)){
                $countMainSkillArray = count((array)$MainSkillArray);
                $encodedMainSkillArray = json_encode($MainSkillArray);
                $assigned_questions_list = ($request->assigned_questions_list)? $request->assigned_questions_list : [];
                $result_list = json_encode($result_list);
                $encodedAttemptedQuestionAnswers = json_encode($AttemptedQuestionAnswers);
                $QuestionNo = ($request->QuestionNo);
                $QuestionResponse['question_html'] = (string)View::make('backend.student.real_time_generate_question.next_question_html',compact('request','Question','examLanguage','assigned_questions_list','encodedMainSkillArray','result_list','encodedAttemptedQuestionAnswers','QuestionNo','countMainSkillArray'));
                return $this->sendResponse($QuestionResponse);exit;
            }
        }
    }

    /**
     * USE : Landing Page on Create Self-Learning Exercise
     */
    public function CreateSelfLearningExercise(Request $request){
        $difficultyLevels = PreConfigurationDiffiltyLevel::all();
        $RequiredQuestionPerSkill = [];
        $RequiredQuestionPerSkill = [
            'minimum_question_per_skill' => $this->getGlobalConfiguration('no_of_questions_per_learning_skills'),
            'maximum_question_per_skill' => $this->getGlobalConfiguration('max_no_question_per_learning_objectives')
        ];
        // Get Strand List
        $strandsList = Strands::all();
        $learningObjectivesConfiguration = array();
        if(!empty($strandsList)){
            $LearningUnits = collect($this->GetLearningUnits($strandsList[0]->{cn::STRANDS_ID_COL}));
            $LearningObjectives = $this->GetLearningObjectives($LearningUnits->pluck('id')->toArray());
        }
        return view('backend.student.self_learning.create_self_learning_exercise',compact('difficultyLevels','strandsList','LearningUnits','LearningObjectives','RequiredQuestionPerSkill','learningObjectivesConfiguration'));
    }

    /**
     * USE : Generate Question for self-learning exercise
     */
    public function GenerateQuestionSelfLearningExercise(Request $request){
        if(isset($request)){
            if(!isset($request->learning_unit)){
                return $this->sendError('Please select learning objectives', 404);
            }
            $examConfigurationsData = json_encode($request->all());
            $examLanguage = (isset($request->language)) ? $request->language : 'en';
            $difficultyLevels = PreConfigurationDiffiltyLevel::all();
            $result = array();
            $minimumQuestionPerSkill = Helper::getGlobalConfiguration('no_of_questions_per_learning_skills') ?? 2 ;
            $coded_questions_list = array();
            $no_of_questions = 10;
            if(isset($request->total_no_of_questions) && !empty($request->total_no_of_questions)){
                $no_of_questions = $request->total_no_of_questions;
            }

            if($request->self_learning_test_type==1){
                $QuestionType = array(2,3);
            }else{
                $QuestionType = array(1);
            }

            $MainSkillArray = array();
            if(isset($request->learning_unit) && !empty($request->learning_unit)){
                foreach($request->learning_unit as $learningUnitId => $learningUnitData){
                    $learningObjectiveQuestionArray = array();
                    if(isset($learningUnitData['learning_objective']) && !empty($learningUnitData['learning_objective'])){
                        foreach($learningUnitData['learning_objective'] as $LearningObjectiveId => $data){

                            $objective_mapping_id = StrandUnitsObjectivesMappings::whereIn('strand_id',$request->strand_id)
                                                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$learningUnitId)
                                                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$LearningObjectiveId)
                                                        ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
                            $QuestionSkill = Question::whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$objective_mapping_id)
                                                //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                                ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType)
                                                ->groupBy(cn::QUESTION_E_COL)
                                                ->pluck(cn::QUESTION_E_COL)
                                                ->toArray();
                            if(isset($QuestionSkill) && !empty($QuestionSkill)){
                                $GetQuestionPerObjective = $data['get_no_of_question_learning_objectives'];
                                $qLoop = 0;
                                $qSize = 0;
                                while($qLoop <= $GetQuestionPerObjective){
                                    foreach($QuestionSkill as $skillName){
                                        if($qSize >= $GetQuestionPerObjective){
                                            break;
                                        }
                                        $MainSkillArray[] =  array(
                                            'qloop' => $qLoop,
                                            'learning_unit_id' => $learningUnitId,
                                            'learning_objective_id' => $LearningObjectiveId,
                                            'objective_mapping_ids' => $objective_mapping_id,
                                            'learning_objective_skill' => $skillName
                                        );
                                        $qSize++;
                                    }

                                    // Get the learning objectives extra skills
                                    $GetExtraExtraSkillLearningObjectives = LearningObjectivesSkills::where(cn::LEARNING_OBJECTIVES_SKILLS_LEARNING_OBJECTIVE_ID_COL,$LearningObjectiveId)->get();
                                    if(isset($GetExtraExtraSkillLearningObjectives) && !empty($GetExtraExtraSkillLearningObjectives)){
                                        $GetExtraExtraSkillLearningObjectives = $GetExtraExtraSkillLearningObjectives->pluck(cn::LEARNING_OBJECTIVES_SKILLS_LEARNING_SKILL_COL)->toArray();
                                        foreach($GetExtraExtraSkillLearningObjectives as $LearningObjectiveExtraSkill){
                                            $ExplodeSkillCode = explode('-',$LearningObjectiveExtraSkill);
                                            $ExtraSkillQuestionExists = Question::where(cn::QUESTION_NAMING_STRUCTURE_CODE_COL, 'like', '%'.$LearningObjectiveExtraSkill.'%')
                                                                        ->where(cn::QUESTION_E_COL,end($ExplodeSkillCode))
                                                                        //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                                                        ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType)
                                                                        ->count();
                                            if(isset($ExtraSkillQuestionExists) && !empty($ExtraSkillQuestionExists)){
                                                if($qSize >= $GetQuestionPerObjective){
                                                    break;
                                                }
                                                $ExtraSkillStrand = Strands::where('code',$ExplodeSkillCode[1])->first();
                                                $ExtraSkillLearningsUnit = LearningsUnits::where(cn::LEARNING_UNITS_CODE_COL,substr($ExplodeSkillCode[2],0,2))->where('stage_id','<>',3)->first();
                                                $ExtraSkillLearningsObjectives = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_CODE_COL,substr($ExplodeSkillCode[2],2))
                                                                                ->where('learning_unit_id',$ExtraSkillLearningsUnit->id)
                                                                                ->where('stage_id','<>',3)
                                                                                ->first();
                                                $ExtraSkillObjectiveMappingId = StrandUnitsObjectivesMappings::where('strand_id',$ExtraSkillStrand->id)
                                                                                ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$ExtraSkillLearningsUnit->id)
                                                                                ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$ExtraSkillLearningsObjectives->id)
                                                                                ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)
                                                                                ->toArray();
                                                $MainSkillArray[] =  array(
                                                    'qloop'                     => $qLoop,
                                                    'learning_unit_id'          => $ExtraSkillLearningsUnit->id,
                                                    'learning_objective_id'     => $ExtraSkillLearningsObjectives->id,
                                                    'objective_mapping_ids'     => $ExtraSkillObjectiveMappingId,
                                                    'learning_objective_skill'  => $ExplodeSkillCode[3]
                                                );
                                                $qSize++;
                                            }
                                        }
                                    }

                                    if($qSize >= $GetQuestionPerObjective){
                                        break;
                                    }
                                    $qLoop++;
                                }
                            }
                        }
                    }
                }
            }

            if(isset($MainSkillArray) && !empty($MainSkillArray)){
                foreach($MainSkillArray as $currentSkillArrayId => $skillArray){
                    $questionArray = Question::whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$skillArray['objective_mapping_ids'])
                                        //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                        ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType)
                                        ->where(cn::QUESTION_E_COL,$skillArray['learning_objective_skill'])
                                        ->get()->toArray();
                    if(!empty($questionArray)){
                        foreach($questionArray as $question_key => $question_value){
                            $countNoOfAnswer = $this->CountNoOfAnswerByQuestionId($question_value['id']);
                            $coded_questions_list[] = array(
                                                        $question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],
                                                        floatval($question_value['PreConfigurationDifficultyLevel']->title),
                                                        0,
                                                        $countNoOfAnswer
                                                    );
                        }
                    }
                    if(isset($coded_questions_list) && !empty($coded_questions_list)){
                        $assigned_questions_list = [];
                        $result_list = [];
                        $requestPayload =   new \Illuminate\Http\Request();
                        $studentAbilities = Auth::user()->{cn::USERS_OVERALL_ABILITY_COL} ?? 0;
                        $requestPayload =   $requestPayload->replace([
                                                'initial_ability'           => floatval($studentAbilities),
                                                'assigned_questions_list'   => $assigned_questions_list,
                                                'result_list'               => $result_list,
                                                'coded_questions_list'      => $coded_questions_list,
                                                'repeated_rate'             => $this->repeated_rate_config
                                            ]);
                        $response = $this->AIApiService->Real_Time_Assign_Question_N_Estimate_Ability($requestPayload);
                        if(isset($response) && !empty($response)){
                            $assigned_questions_list[] = $response[0];
                            $responseQuestionCodes = ($response[0][0]);
                            $Question = Question::with(['answers','objectiveMapping'])
                                            ->where(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodes)
                                            ->first();
                            if(isset($Question) && !empty($Question)){
                                unset($MainSkillArray[$currentSkillArrayId]);
                                $countMainSkillArray = count($MainSkillArray);
                                $encodedMainSkillArray = json_encode($MainSkillArray);
                                $assigned_questions_list = json_encode($assigned_questions_list);
                                $result_list = json_encode($result_list);
                                $QuestionNo = 1;

                                // Get General Hints current question
                                $UploadDocumentsData = $this->GetGeneralHintsData($Question->id,$examLanguage);

                                $QuestionResponse['question_html'] = (string)View::make('backend.student.real_time_generate_question.self_learning_exercise.question_html',compact('request','examConfigurationsData','Question','examLanguage','assigned_questions_list','encodedMainSkillArray','result_list','QuestionNo','countMainSkillArray','UploadDocumentsData'));
                                return $this->sendResponse($QuestionResponse);exit;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * USE : Get Next Question
     */
    public function GenerateQuestionSelfLearningExerciseNextQuestion(Request $request){
        if(isset($request->encodedMainSkillArray) && !empty($request->encodedMainSkillArray)){
            $examLanguage = (isset($request->language)) ? $request->language : 'en';
            if($request->self_learning_test_type==1){
                $QuestionType = array(2,3);
            }else{
                $QuestionType = array(1);
            }
            $assigned_questions_list    = json_decode($request->assigned_questions_list) ?? [];
            $assignedQuestionCodes      = array();
            $assignedQuestionCodes      = array_column($assigned_questions_list,0);

            $AttemptedQuestionAnswers   = json_decode($request->AttemptedQuestionAnswers);
            $AttemptedQuestionResult    = $this->CheckStudentAnswerResult($request->currentQuestion,$request->answer);
            $result_list                = json_decode($request->result_list);
            $result_list[]              = $AttemptedQuestionResult;
            $AttemptedQuestionAnswers[] = array(
                                            'question_id' => $request->currentQuestion,
                                            'answer' => $request->answer,
                                            'answer_result' => $AttemptedQuestionResult,
                                            'language' => 'en',
                                            'duration_second' => $request->current_question_taking_timing
                                        );
            $MainSkillArray = json_decode($request->encodedMainSkillArray);
            if(isset($MainSkillArray) && !empty($MainSkillArray)){                
                foreach($MainSkillArray as $currentSkillArrayId => $skillArray){
                    // $questionArray = Question::with('PreConfigurationDifficultyLevel')
                    //                     ->whereNotIn(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$assignedQuestionCodes)
                    //                     ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$skillArray->objective_mapping_ids)
                    //                     //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                    //                     ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType)
                    //                     ->where(cn::QUESTION_E_COL,$skillArray->learning_objective_skill)
                    //                     ->get()->toArray();
                    $questionArray = Question::whereNotIn(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$assignedQuestionCodes)
                                        ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$skillArray->objective_mapping_ids)
                                        //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                        ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType)
                                        ->where(cn::QUESTION_E_COL,$skillArray->learning_objective_skill)
                                        ->get()->toArray();
                    if(!empty($questionArray)){
                        foreach($questionArray as $question_key => $question_value){
                            $countNoOfAnswer = $this->CountNoOfAnswerByQuestionId($question_value['id']);
                            // $coded_questions_list[] = array(
                            //                             $question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],
                            //                             floatval($question_value['pre_configuration_difficulty_level']['title']),
                            //                             0,
                            //                             $countNoOfAnswer
                            //                         );
                            $coded_questions_list[] =   array(
                                                            $question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],
                                                            floatval($question_value['PreConfigurationDifficultyLevel']->title),
                                                            0,
                                                            $countNoOfAnswer
                                                        );
                        }
                    }
                    if(isset($coded_questions_list) && !empty($coded_questions_list)){
                        $requestPayload =   new \Illuminate\Http\Request();
                        $studentAbilities = Auth::user()->{cn::USERS_OVERALL_ABILITY_COL} ?? 0;
                        $requestPayload =   $requestPayload->replace([
                                                'initial_ability'           => floatval($studentAbilities),
                                                'assigned_questions_list'   => $assigned_questions_list,
                                                'result_list'               => $result_list,
                                                'coded_questions_list'      => $coded_questions_list,
                                                'repeated_rate'             => $this->repeated_rate_config
                                            ]);
                        $response = $this->AIApiService->Real_Time_Assign_Question_N_Estimate_Ability($requestPayload);
                        if(isset($response) && !empty($response)){
                            $assigned_questions_list[] = $response[0];
                            $responseQuestionCodes = ($response[0][0]);
                            // $Question = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])
                            //                 ->where(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodes)
                            //                 ->first();
                            $Question = Question::with(['answers','objectiveMapping'])
                                            ->where(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodes)
                                            ->first();
                            if(isset($Question) && !empty($Question)){
                                unset($MainSkillArray->$currentSkillArrayId);
                                $countMainSkillArray = count((array)$MainSkillArray);
                                $encodedMainSkillArray = json_encode($MainSkillArray);
                                $assigned_questions_list = json_encode($assigned_questions_list);
                                $result_list = json_encode($result_list);
                                $encodedAttemptedQuestionAnswers = json_encode($AttemptedQuestionAnswers);

                                // Get General Hints current question
                                $UploadDocumentsData = $this->GetGeneralHintsData($Question->id,$examLanguage);

                                $QuestionNo = ($request->QuestionNo+1);
                                $QuestionResponse['question_html'] = (string)View::make('backend.student.real_time_generate_question.self_learning_exercise.next_question_html',compact('request','Question','examLanguage','assigned_questions_list','encodedMainSkillArray','result_list','encodedAttemptedQuestionAnswers','QuestionNo','countMainSkillArray','UploadDocumentsData'));
                                return $this->sendResponse($QuestionResponse);exit;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * USE : Store Self learning detail into database
     */
    public function SaveSelfLearningExercise(Request $request){
        $AttemptedQuestionAnswers = json_decode($request->AttemptedQuestionAnswers);
        $AttemptedQuestionResult = $this->CheckStudentAnswerResult($request->currentQuestion,$request->answer);
        $result_list = json_decode($request->result_list);
        $AttemptedQuestionAnswers[] = (object) array(
                                        'question_id' => $request->currentQuestion,
                                        'answer' => $request->answer,
                                        'answer_result' => $AttemptedQuestionResult,
                                        'language' => 'en',
                                        'duration_second' => $request->current_question_taking_timing
                                    );
        
        // Get QuestionIds from attempted questions
        $questionIds = implode(",",array_column($AttemptedQuestionAnswers,'question_id'));
        $timeduration = null;
        if($request->self_learning_test_type == 2){
            $TotalTime = 0;
            $QuestionPerSeconds = $this->getGlobalConfiguration('default_second_per_question');
            if(isset($QuestionPerSeconds) && !empty($QuestionPerSeconds) && !empty($questionIds)){
                $totalSeconds = (count(explode(",",$questionIds)) * $QuestionPerSeconds);
                $TotalTime = gmdate("H:i:s", $totalSeconds);
                $timeduration = ($TotalTime) ? $this->timeToSecond($TotalTime): null;
            }
        }

        $examData = [
            cn::EXAM_CURRICULUM_YEAR_ID_COL                 => $this->GetCurriculumYear(), // "CurrentCurriculumYearId" Get value from Global Configuration
            cn::EXAM_CALIBRATION_ID_COL                     => $this->GetCurrentAdjustedCalibrationId(),
            cn::EXAM_TYPE_COLS                              => 1,
            cn::EXAM_REFERENCE_NO_COL                       => $this->GetMaxReferenceNumberExam(1,$request->self_learning_test_type),
            cn::EXAM_TABLE_TITLE_COLS                       => $this->createTestTitle(),
            cn::EXAM_TABLE_FROM_DATE_COLS                   => Carbon::now(),
            cn::EXAM_TABLE_TO_DATE_COLS                     => Carbon::now(),
            cn::EXAM_TABLE_RESULT_DATE_COLS                 => Carbon::now(),
            cn::EXAM_TABLE_PUBLISH_DATE_COL                 => Carbon::now(),
            cn::EXAM_TABLE_TIME_DURATIONS_COLS              => $timeduration,
            cn::EXAM_TABLE_QUESTION_IDS_COL                 => ($questionIds) ?  $questionIds : null,
            cn::EXAM_TABLE_STUDENT_IDS_COL                  => $this->LoggedUserId(),
            cn::EXAM_TABLE_SCHOOL_COLS                      => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
            cn::EXAM_TABLE_IS_UNLIMITED                     => ($request->self_learning_test_type == 1) ? 1 : 0,
            cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL      => $request->self_learning_test_type,
            cn::EXAM_TABLE_LEARNING_OBJECTIVES_CONFIGURATIONS_COL => $request->examConfigurationsData ?? null,
            cn::EXAM_TABLE_CREATED_BY_COL                   => $this->LoggedUserId(),
            cn::EXAM_TABLE_CREATED_BY_USER_COL              => 'student',
            cn::EXAM_TABLE_STATUS_COLS                      => 'publish'
        ];
        $exams = Exam::create($examData);
        $this->UserActivityLog(
            Auth::user()->id,
            '<p>'.Auth::user()->DecryptNameEn.' '.__('activity_history.new_test_created').'.'.
            '<p>'.__('activity_history.title_is').$exams->title.'</p>'
        );
        if($exams){
            // Create exam school mapping
            ExamSchoolMapping::create([
                cn::EXAM_SCHOOL_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL => $exams->id,
                cn::EXAM_SCHOOL_MAPPING_STATUS_COL => 'publish'
            ]);

            // find student overall ability from AI-API
            if(isset($AttemptedQuestionAnswers) && !empty($AttemptedQuestionAnswers)){
                $NoOfCorrectAnswers = 0;
                $NoOfWrongAnswers = 0;
                foreach ($AttemptedQuestionAnswers as $key => $Question) {
                    $QuestionId = $Question->question_id;
                    $answer = $Question->answer;
                    if($Question->answer_result){
                        $NoOfCorrectAnswers = ($NoOfCorrectAnswers + 1);
                        $apiData['questions_results'][] = true;
                    }else{
                        $NoOfWrongAnswers = ($NoOfWrongAnswers + 1);
                        $apiData['questions_results'][] = false;
                    }

                    // Get Questions Answers and difficulty level
                    $responseData = $this->GetQuestionNumOfAnswerAndDifficultyValue($Question->question_id,$exams->{cn::EXAM_CALIBRATION_ID_COL});
                    $apiData['num_of_ans_list'][] = $responseData['noOfAnswers'];
                    $apiData['difficulty_list'][] = $responseData['difficulty_value'];
                    $apiData['max_student_num'] = 1;
                }
            }

            $StudentAbility = '';
            if(!empty($apiData)){
                // Get the student ability from calling AIApi
                $StudentAbility = $this->GetAIStudentAbility($apiData);
            }

            $PostData = [
                cn::ATTEMPT_EXAMS_CURRICULUM_YEAR_ID_COL        => $this->GetCurriculumYear(),
                cn::ATTEMPT_EXAMS_CALIBRATION_ID_COL            => $this->GetCurrentAdjustedCalibrationId(),
                cn::ATTEMPT_EXAMS_EXAM_ID                       => $exams->id,
                cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID            => $this->LoggedUserId(),
                cn::ATTEMPT_EXAMS_STUDENT_GRADE_ID              => Auth::user()->grade_id,
                cn::ATTEMPT_EXAMS_STUDENT_CLASS_ID              => Auth::user()->class_id,
                cn::ATTEMPT_EXAMS_LANGUAGE_COL                  => 'en',
                cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL           => (!empty($AttemptedQuestionAnswers)) ? json_encode($AttemptedQuestionAnswers) : null,
                cn::ATTEMPT_EXAMS_WRONG_ANSWER_COL              => '',
                //cn::ATTEMPT_EXAMS_ATTEMPT_FIRST_TRIAL_COL     => $request->attempt_first_trial_data_new,
                //cn::ATTEMPT_EXAMS_ATTEMPT_SECOND_TRIAL_COL    => $wrong_ans_list_json,
                cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS         => $NoOfCorrectAnswers,
                cn::ATTEMPT_EXAMS_TOTAL_WRONG_ANSWERS           => $NoOfWrongAnswers,
                cn::ATTEMPT_EXAMS_EXAM_TAKING_TIMING            => $request->exam_taking_timing,
                cn::ATTEMPT_EXAMS_STUDENT_ABILITY_COL           => ($StudentAbility!='') ? $StudentAbility : null,
                cn::ATTEMPT_EXAMS_SERVER_DETAILS_COL            => json_encode($this->serverData()) ?? null
            ];
            $save = AttemptExams::create($PostData);
            $this->UserActivityLog(
                Auth::user()->id,
                '<p>'.Auth::user()->DecryptNameEn.' '.__('activity_history.exam_attempted').'.'.
                '<p>'.__('activity_history.title_is').$exams->title.'.'.'</p>'.
                '<p>'.__('activity_history.exam_reference_is').$exams->reference_no.'</p>'.
                '<p>'.__('activity_history.exam_submitting_time_is').' '.date('Y/m/d h:i:s a', time())
            );
            if($save){
                //Update Column Is_my_teaching_sync
                Exam::find($exams->id)->update([cn::EXAM_TABLE_IS_TEACHING_REPORT_SYNC =>'true']);
                
                /** Start Update overall ability for the student **/
                if($exams->exam_type == 3 || ($exams->exam_type == 1 && $exams->self_learning_test_type == 2)){
                    $this->CronJobController->UpdateStudentOverAllAbility();
                }

                /** Update My Teaching Table Via Cron Job */
                $this->CronJobController->UpdateMyTeachingTable(Auth::user()->{cn::USERS_SCHOOL_ID_COL}, $exams->id);

                /** Update Student Credit Points via cron job */
                $this->CronJobController->UpdateStudentCreditPoints($exams->id, Auth::user()->id);
                
                /** End Update overall ability for the student **/
                $this->StoreAuditLogFunction('','Exams','','','Attempt Exam',cn::EXAM_TABLE_NAME,'');

                $response['redirectUrl'] = 'exams/result/'.$exams->id.'/'.Auth::user()->{cn::USERS_ID_COL};
                return $this->sendResponse($response);exit;
            }
        }
    }

    /**
     * USE : Get Next Question
     */
    public function GenerateQuestionSelfLearningExerciseChangeLanguage(Request $request){
        if(isset($request->encodedMainSkillArray) && !empty($request->encodedMainSkillArray)){
            $examLanguage = (isset($request->language)) ? $request->language : 'en';
            if($request->self_learning_test_type==1){
                $QuestionType = array(2,3);
            }else{
                $QuestionType = array(1);
            }

            $AttemptedQuestionAnswers = json_decode($request->AttemptedQuestionAnswers);
            $result_list = json_decode($request->result_list);
            $MainSkillArray = json_decode($request->encodedMainSkillArray);
            $Question = Question::with(['answers','objectiveMapping'])
                            ->where('id',$request->currentQuestion)
                            ->first();
            if(isset($Question) && !empty($Question)){
                $countMainSkillArray = count((array)$MainSkillArray);
                $encodedMainSkillArray = json_encode($MainSkillArray);
                $assigned_questions_list = ($request->assigned_questions_list)? $request->assigned_questions_list : [];
                $result_list = json_encode($result_list);
                $encodedAttemptedQuestionAnswers = json_encode($AttemptedQuestionAnswers);
                $QuestionNo = ($request->QuestionNo);

                // Get General Hints current question
                $UploadDocumentsData = $this->GetGeneralHintsData($Question->id,$examLanguage);

                $QuestionResponse['question_html'] = (string)View::make('backend.student.real_time_generate_question.self_learning_exercise.next_question_html',compact('request','Question','examLanguage','assigned_questions_list','encodedMainSkillArray','result_list','encodedAttemptedQuestionAnswers','QuestionNo','countMainSkillArray','UploadDocumentsData'));
                return $this->sendResponse($QuestionResponse);exit;
            }
        }
    }

    /**
     * USE : Get general Hints data by question
     */
    public function GetGeneralHintsData($QuestionId,$language){
        $UploadDocumentsData = array();
        $Question = Question::find($QuestionId);
        if($language == 'en'){
            if(isset($Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}) && $Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}!=""){
                $UploadDocumentsData = UploadDocuments::find($Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN});
            }else{
                $arrayOfQuestion = explode('-',$Question->{cn::QUESTION_QUESTION_CODE_COL});
                if(count($arrayOfQuestion) == 8){
                    unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                    $newQuestionCode = implode('-',$arrayOfQuestion);
                    $newQuestionData = Question::where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                    if(isset($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}) && !empty($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN})){
                        $UploadDocumentsData = UploadDocuments::find($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN});
                    }
                }
            }
        }else{
            if(isset($Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}) && $Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}!=""){
                $UploadDocumentsData = UploadDocuments::find($Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH});
            }else{
                $arrayOfQuestion = explode('-',$Question->{cn::QUESTION_QUESTION_CODE_COL});
                if(count($arrayOfQuestion) == 8){
                    unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                    $newQuestionCode = implode('-',$arrayOfQuestion);
                    $newQuestionData = Question::where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                    if(isset($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}) && !empty($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH})){
                        $UploadDocumentsData = UploadDocuments::find($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH});
                    }
                }
            }
        }
        return $UploadDocumentsData;
    }

    /**
     * USE : Preview Self-learning configurations
     */
    public function PreviewSelfLearningConfigurations($examId, Request $request){
        $ExamData = Exam::where('id',$examId)->first();
        if(isset($ExamData) && !empty($ExamData)){
            $difficultyLevels = PreConfigurationDiffiltyLevel::all();
            // Decode test configurations
            if(isset($ExamData->learning_objectives_configuration) && !empty($ExamData->learning_objectives_configuration)){
                $LearningObjectiveConfigurations = json_decode($ExamData->learning_objectives_configuration);
            }
            
            // Get Strand List
            $strandsList = Strands::all();
            $learningObjectivesConfiguration = array();
            if(!empty($strandsList)){
                $LearningUnits = LearningsUnits::whereIn(cn::LEARNING_UNITS_STRANDID_COL,$LearningObjectiveConfigurations->strand_id)->where('stage_id','<>',3)->get();
                if(!empty($LearningUnits)){
                    $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->where('stage_id','<>',3)->whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$LearningObjectiveConfigurations->learning_unit_id)->get();
                }
            }

            // Find the question list for review of the questions
            $questionListHtml = '';
            $question_list = Question::with(['answers','objectiveMapping'])
                                ->whereIn(cn::QUESTION_TABLE_ID_COL,explode(',',$ExamData->question_ids))
                                ->get();
            $questionListHtml = (string)View::make('backend.question_generator.school.question_list_preview',compact('question_list','difficultyLevels'));

            // Set the page title : 1 = Exercise, 2 = Testing Zone
            if($ExamData->self_learning_test_type == 1){
                $pageTitle = __('languages.self_learning');
            }else{
                $pageTitle = __('languages.ai_based_assessment');
            }
            //$pageTitle = ($ExamData->self_learning_test_type == 1) ? __('languages.preview').' '.__('languages.self_learning_exercise') : __('languages.preview').' '.__('languages.self_learning_test');
            return view('backend.student.self_learning.preview_self_learning',compact('pageTitle','LearningObjectiveConfigurations','difficultyLevels','strandsList','LearningUnits','LearningObjectives','questionListHtml','learningObjectivesConfiguration'));
        }
    }
}