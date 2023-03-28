<?php
namespace App\Http\Controllers\AI_Calibration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Constants\DbConstant As cn;
use App\Helpers\Helper;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\School;
use App\Models\User;
use App\Models\Question;
use App\Models\Exam;
use App\Models\AttemptExams;
use App\Http\Services\AIApiService;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\AICalibrationReport;
use App\Models\CalibrationQuestionLog;
use Illuminate\Support\Facades\View;
use App\Jobs\AICalibrationJob;

class AI_CalibrationController extends Controller
{
    use Common,ResponseFormat;

    protected $AIApiService, 
            $CalibrationRound,
            $StudentCurrentCalibrationAbility,
            $StudentNewCalibrationAbility,
            $NewAICalibrationQuestionDifficulty,
            $CurrentAICalibrationQuestionDifficulty,
            $QuestionResults,$questions_results,
            $DefaultCalibrationPercentage,
            $ExcludeQuestionLimit,
            $IncludedCalibratedQuestions,
            $ExcludeCalibratedQuestions,
            $QuestionCountsArray,
            $CalibrationConstant,
            $Normalized_RMSE_Ability,
            $Normalized_RMSE_Difficulties,
            $MedianCalibrationDifficulties,
            $MedianCalibrationAbility,
            $CalibrationDifficulties,
            $CalibratedQuestionDifficulties,
            $AICalibrationIncludedQuestionSeedLimit,
            $AttemptedQuestionStudentCount,
            $IncludedAICalibrationStudents,
            $ExcludedAICalibrationStudents,
            $UpdatedCalibratedStudentAbility,
            $MedianStudentAbility,
            $AICalibrationReportId,
            $CalibrationMinimumStudentAccuracy,
            $isUpdate,
            $SelectedGlobalConfigDifficultyType,
            $isInitialCondition,
            $SelectedAdjustedCalibrationId;

    public function __construct(){
        ini_set('max_execution_time', -1);

        $this->middleware('auth');
        $this->middleware('preventBackHistory');
        $this->AIApiService = new AIApiService();
        $this->CalibrationRound = 0;
        $this->StudentCurrentCalibrationAbility = array();
        $this->StudentNewCalibrationAbility = array();
        $this->NewAICalibrationQuestionDifficulty = array();
        $this->QuestionResults = array();
        $this->questions_results = array();
        $this->DefaultCalibrationPercentage = Helper::getGlobalConfiguration('ai_calibration_percentage') ?? 2.5;
        $this->ExcludeQuestionLimit = Helper::getGlobalConfiguration('exclude_ai_calibration_question_limit') ?? 60;
        $this->CalibrationMinimumStudentAccuracy = Helper::getGlobalConfiguration('ai_calibration_minimum_student_accuracy') ?? 25;
        $this->QuestionCountsArray = array();
        $this->IncludedCalibratedQuestions = array();
        $this->ExcludeCalibratedQuestions = array();
        $this->CalibrationConstant = null;
        $this->Normalized_RMSE_Ability = 0;
        $this->Normalized_RMSE_Difficulties = 0;
        $this->MedianCalibrationDifficulties = 0;
        $this->MedianCalibrationAbility = 0;
        $this->CalibratedQuestionDifficulties = array();
        $this->AICalibrationIncludedQuestionSeedLimit = Helper::getGlobalConfiguration('ai_calibration_included_question_seed_limit') ?? 20;
        $this->SelectedGlobalConfigDifficultyType  = $this->getGlobalConfiguration('difficulty_selection_type');
        $this->AttemptedQuestionStudentCount = array();
        $this->IncludedAICalibrationStudents = array();
        $this->ExcludedAICalibrationStudents = array();
        $this->UpdatedCalibratedStudentAbility = array();
        $this->MedianStudentAbility = 0;
        $this->AICalibrationReportId = 0;
        $this->isUpdate = true;
        $this->isInitialCondition = false;
        $this->SelectedAdjustedCalibrationId = 0;
    }

    /***
     * USE : AI Calibration Question Log
     */
    public function CalibrationQuestionLog(Request $request,$id){
        try{
            $items = $request->items ?? 10;
            $calibrationLogData = CalibrationQuestionLog::with('AICalibrationReport','question')->where(cn::CALIBRATION_QUESTION_LOG_REPORT_ID_COL,$id)->paginate($items);
            $questionLogType = array(
                ['id' => 'include','name' => __('languages.include')],
                ['id'  => 'exclude','name' => __('languages.exclude')]
            );
            if(isset($request->filter)){
                $Query = CalibrationQuestionLog::with('AICalibrationReport','question');
                if(isset($request->question_log_type) && !empty($request->question_log_type)){
                    $Query->where(cn::CALIBRATION_QUESTION_LOG_QUESTION_LOG_TYPE_COL,$request->question_log_type);
                }
                $calibrationLogData = $Query->where(cn::CALIBRATION_QUESTION_LOG_REPORT_ID_COL,$id)->paginate($items);
            }
            
            return view('backend.ai_calibration.ai_calibration_question_log',compact('calibrationLogData','questionLogType'));
        }catch(Exception $exception){
            return $this->sendError($exception->getMessage(), 422);
        }
    }

    public function DisplayingAbilities($abilities){
        return round((exp($abilities)/(1+exp($abilities)) * 10), 3);
    }

    public function DisplayingDifficulties($difficulties){
        return round((exp($difficulties)/(1+exp($difficulties)) * 10), 3);
    }

    /**
     * USE : Get Student List based on selected school
     */
    public function StudentList(Request $request){
        $optionList = '';
        $studentList = User::where([
                            cn::USERS_ROLE_ID_COL   => cn::STUDENT_ROLE_ID,
                            cn::USERS_STATUS_COL    => 'active'
                        ])
                        ->whereIn(cn::USERS_SCHOOL_ID_COL,$request->school_id)
                        ->get();
        if(!$studentList->isEmpty()){
            foreach($studentList as $student){
                $optionList .= '<option value="'.$student->{cn::USERS_ID_COL}.'" selected>';
                if(app()->getLocale() == 'en'){
                    $optionList .= $student->DecryptNameEn;
                }else{
                    $optionList .= $student->DecryptNameCh;
                }
                if($student->{cn::USERS_CLASS_STUDENT_NUMBER}){
                    $optionList .= '('.$student->{cn::USERS_CLASS_STUDENT_NUMBER}.')';
                }
                $optionList .= '</option>';
            }
        }else{
            $optionList .= '<option value="">'.__('languages.students_not_available').'</option>';
        }
        return $this->sendResponse([$optionList]);
    }

    /**
     * USE : Get Adjusted Calibration data by Calibration Id
     */
    public function GetAdjustedCalibrationData($CalibrationId){
        if($CalibrationId){
            $AdjustedCalibrationList = AICalibrationReport::select(cn::AI_CALIBRATION_REPORT_START_DATE_COL,
            cn::AI_CALIBRATION_REPORT_END_DATE_COL)
            ->where([
                cn::AI_CALIBRATION_REPORT_ID_COL => $CalibrationId
            ])->first();
            return $this->sendResponse($AdjustedCalibrationList);
        }else{
            return $this->sendError('Calibration data not found',422);
        }
    }

    /**
     * USE : Create new calibration
     */
    public function CreateAICalibration(Request $request){
        // Defined Default Parameters
        $SchoolList = [];

        // Get all School list
        $SchoolList = School::all();

        //Get Adjusted Calibration List
        $AdjustedCalibrationList = AICalibrationReport::where([
            cn::AI_CALIBRATION_REPORT_STATUS_COL => 'adjusted'
        ])->get();

        // If the method is get then system will redirect to landing page on ali-calibration
        if ($request->isMethod('GET')){
            return view('backend.ai_calibration.create',compact('SchoolList','AdjustedCalibrationList'));
        }

        // Generate AI-Calibration
        if ($request->isMethod('post')){

            $params = $request->all();
            // Get & Set Included questions in AI-Calibration
            $this->GetIncludeExcludeQuestion($params);
            
            // check student attempted question included questions & exclude students
            $this->GetIncludedStudentIds($params);

            if(isset($this->IncludedAICalibrationStudents) && !empty($this->IncludedAICalibrationStudents) && isset($this->IncludedCalibratedQuestions) && !empty($this->IncludedCalibratedQuestions)){
                $AICalibrationReport =  AICalibrationReport::Create([
                                            cn::AI_CALIBRATION_REPORT_CALIBRATION_NUMBER_COL            => $this->GenerateCalibrationNumber(),
                                            cn::AI_CALIBRATION_REPORT_REFERENCE_CALIBRATION_COL         => $params['reference_adjusted_calibration'] ?? null,
                                            cn::AI_CALIBRATION_REPORT_START_DATE_COL                    => $this->DateConvertToYMD($params['start_date']),
                                            cn::AI_CALIBRATION_REPORT_END_DATE_COL                      => $this->DateConvertToYMD($params['end_date']),
                                            cn::AI_CALIBRATION_REPORT_TEST_TYPE_COL                     => $params['test_type'] ?? null,
                                            cn::AI_CALIBRATION_REPORT_SCHOOL_IDS_COL                    => implode(',',$params['schoolIds']) ?? null,
                                            cn::AI_CALIBRATION_REPORT_STUDENT_IDS_COL                   => implode(',',$params['studentIds']) ?? null,
                                            cn::AI_CALIBRATION_REPORT_STATUS_COL                        => 'pending'
                                        ]);

                if(isset($AICalibrationReport) && !empty($AICalibrationReport)){
                    $this->AICalibrationReportId = $AICalibrationReport->id;
                    $params['calibration_report_id'] = $AICalibrationReport->id;
                }

                // After Start calibration process will notify to super admin via email
                $this->SendMailToAdmin('start_calibration');

                // Run the process for calibration process
                dispatch(new AICalibrationJob($params))->delay(now()->addSeconds(1));

                return redirect('ai-calibration/list')->with('success_msg','New Calibration process will start.');
            }else{
                return back()->with('error_msg', __('languages.data_not_found'));
            }
        }
    }

    /**
     * USE : Generate AI-Calibration flow
     */
    public function GenerateAICalibration($params){
        ini_set('max_execution_time', -1);
        $CalibrationReport = array();

        if($params['reference_adjusted_calibration'] == 'initial_conditions'){
            $this->isInitialCondition = true;
        }else{
            $this->SelectedAdjustedCalibrationId = $params['reference_adjusted_calibration'];
        }

        // Get & Set Included questions in AI-Calibration
        $this->GetIncludeExcludeQuestion($params);
        
        // check student attempted question included questions & exclude students
        $this->GetIncludedStudentIds($params);

        if(isset($this->IncludedAICalibrationStudents) && !empty($this->IncludedAICalibrationStudents) && isset($this->IncludedCalibratedQuestions) && !empty($this->IncludedCalibratedQuestions)){
            // Find Student Estimate Competence
            $this->FindAICalibrationDifficultiesAndAbilities($params);

            $this->GetAICalibrationReportData($params);
        }
    }
    
    /**
     * USE : Set & Get Include Exclude question list in our AI-Calibration
     */
    public function GetIncludeExcludeQuestion($params){
        if(isset($params['studentIds']) && !empty($params['studentIds'])){
            foreach($params['studentIds'] as $StudentId){
                $AttemptExams =  AttemptExams::whereHas('exam', function($query) use($params){
                    if($params['test_type'] == 1){
                        $query->where(cn::EXAM_TYPE_COLS,3);
                    }
                    if($params['test_type'] == 2){
                        $query->where(cn::EXAM_TYPE_COLS,1)->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2);
                    }
                    if($params['test_type'] == 3){
                        $query->where(cn::EXAM_TYPE_COLS,3)
                        ->orWhere(function($q) use($params){
                            $q->where(cn::EXAM_TYPE_COLS,1)->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2);
                        });
                    }
                })
                ->with('exam')
                ->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$StudentId)
                ->whereBetween(cn::ATTEMPT_EXAMS_CREATED_AT, [date('Y-m-d',strtotime(str_replace("/","-",$params['start_date']))), date('Y-m-d',strtotime(str_replace("/","-",$params['end_date'])))])
                ->get();
                $ExamIds = $AttemptExams->pluck('exam_id');
                if(!$ExamIds->isEmpty()){
                    foreach($ExamIds as $examId){
                        $ExamData = Exam::find($examId);
                        if(isset($ExamData) && !empty($ExamData)){
                            if(!empty($ExamData->question_ids)){
                                $questionIds = explode(',',$ExamData->question_ids);
                                $QuestionList = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)->get();
                                if(isset($QuestionList) && !empty($QuestionList)){
                                    foreach($QuestionList as $QuestionKey => $question){
                                        $getSeedQuestionId = $this->FindSeedQuestionId($question->id);
                                        if(array_key_exists($getSeedQuestionId,$this->QuestionCountsArray)){
                                            $this->QuestionCountsArray[$getSeedQuestionId] = ($this->QuestionCountsArray[$getSeedQuestionId] + 1);
                                        }else{
                                            $this->QuestionCountsArray[$getSeedQuestionId] = 1;
                                        }
                                        // if(array_key_exists($question->id,$this->QuestionCountsArray)){
                                        //     $this->QuestionCountsArray[$question->id] = ($this->QuestionCountsArray[$question->id] + 1);
                                        // }else{
                                        //     $this->QuestionCountsArray[$question->id] = 1;
                                        // }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Set exclude questions in array
        if(isset($this->QuestionCountsArray) && !empty($this->QuestionCountsArray)){
            $this->IncludedCalibratedQuestions = array_filter($this->QuestionCountsArray, function($value){
                                                    return ($value > $this->ExcludeQuestionLimit);
                                                });                                                
            $this->ExcludeCalibratedQuestions = array_filter($this->QuestionCountsArray, function($value){
                                                    return ($value < $this->ExcludeQuestionLimit);
                                                });
        }
    }

    /**
     * Use : Find included students
     */
    public function GetIncludedStudentIds($params){
        $StudentTestResult = [];
        if(isset($params['studentIds']) && !empty($params['studentIds'])){
            foreach($params['studentIds'] as $StudentId){
                $AttemptExams =  AttemptExams::whereHas('exam', function($query) use($params){
                                    if($params['test_type'] == 1){
                                        $query->where(cn::EXAM_TYPE_COLS,3);
                                    }
                                    if($params['test_type'] == 2){
                                        $query->where(cn::EXAM_TYPE_COLS,1)->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2);
                                    }
                                    if($params['test_type'] == 3){
                                        $query->where(cn::EXAM_TYPE_COLS,3)
                                        ->orWhere(function($q) use($params){
                                            $q->where(cn::EXAM_TYPE_COLS,1)->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2);
                                        });
                                    }
                                })
                                ->with('exam')
                                ->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$StudentId)
                                ->whereBetween(cn::ATTEMPT_EXAMS_CREATED_AT, [date('Y-m-d',strtotime(str_replace("/","-",$params['start_date']))), date('Y-m-d',strtotime(str_replace("/","-",$params['end_date'])))])
                                ->get();
                $ExamIds = $AttemptExams->pluck(cn::ATTEMPT_EXAMS_EXAM_ID);
                if(!$ExamIds->isEmpty()){
                    foreach($ExamIds as $examId){
                        $AttemptExamData = $AttemptExams->where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)->first();
                        $ExamData = Exam::find($examId);
                        if(isset($ExamData) && !empty($ExamData)){
                            if(!empty($ExamData->question_ids)){
                                $questionIds = explode(',',$ExamData->question_ids);
                                $QuestionList = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)->get();
                                if(isset($QuestionList) && !empty($QuestionList)){
                                    foreach($QuestionList as $QuestionKey => $question){
                                        // Check this question is included in the AI-Calibrated
                                        $getSeedQuestionId = $this->FindSeedQuestionId($question->id);
                                        //if(array_key_exists($question->id,$this->IncludedCalibratedQuestions)){
                                        if(array_key_exists($getSeedQuestionId,$this->IncludedCalibratedQuestions)){
                                            if(array_key_exists($StudentId,$this->AttemptedQuestionStudentCount)){
                                                $this->AttemptedQuestionStudentCount[$StudentId] = ($this->AttemptedQuestionStudentCount[$StudentId] + 1);
                                            }else{
                                                $this->AttemptedQuestionStudentCount[$StudentId] = 1;
                                            }

                                            //Store student result
                                            $Answerdetail = $question->answers;
                                            if(isset($AttemptExamData['question_answers'])){
                                                $filterattempQuestionAnswer = array_filter(json_decode($AttemptExamData['question_answers']), function ($var) use($question){
                                                    if($var->question_id == $question['id']){
                                                        return $var ?? [];
                                                    }
                                                });
                                            }
                                            if(isset($filterattempQuestionAnswer) && !empty($filterattempQuestionAnswer)){
                                                foreach($filterattempQuestionAnswer as $fanswer){
                                                    if($fanswer->answer == $Answerdetail->{'correct_answer_'.$fanswer->language}){
                                                        $StudentTestResult[$StudentId][] = true;
                                                    }else{
                                                        $StudentTestResult[$StudentId][] = false;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Set exclude questions in array
        if(isset($this->AttemptedQuestionStudentCount) && !empty($this->AttemptedQuestionStudentCount)){
            $IncludeStudents = array_filter($this->AttemptedQuestionStudentCount, function($value){
                                                    return ($value > $this->AICalibrationIncludedQuestionSeedLimit);
                                                });
            if(isset($IncludeStudents) && !empty($IncludeStudents)){
                // Start Logic : for exclude student lower accuracy
                foreach(array_keys($IncludeStudents) as $sid){
                    $CountTrueResult = count(array_filter($StudentTestResult[$sid]));
                    if((($CountTrueResult / count($StudentTestResult[$sid])) * 100) >= $this->CalibrationMinimumStudentAccuracy){
                        $this->IncludedAICalibrationStudents[] = $sid;
                    }else{
                        $this->ExcludedAICalibrationStudents[] = $sid;
                    }
                }
                // End Logic : For exclude student lower accuracy
                
                //$this->IncludedAICalibrationStudents = array_keys($IncludeStudents);
            }
                                                
            $ExcludedStudents = array_filter($this->AttemptedQuestionStudentCount, function($value){
                                                    return ($value < $this->AICalibrationIncludedQuestionSeedLimit);
                                                });
            if(isset($ExcludedStudents) && !empty($ExcludedStudents)){
                $this->ExcludedAICalibrationStudents = array_keys($ExcludedStudents);
            }
        }
    }

    /**
     * USE : Get the Seed Question difficulty
     */
    public function FindQuestionSeedDifficultyValue($QuestionId){
        $DifficultyValue = 0;
        $QuestionData = Question::find($QuestionId);
        $QuestionCodeArray = explode('-',$QuestionData->{cn::QUESTION_NAMING_STRUCTURE_CODE_COL});
        if(count($QuestionCodeArray) != 7){
            $SeedQuestionCode = $QuestionCodeArray[0].'-'.$QuestionCodeArray[1].'-'.$QuestionCodeArray[2].'-'.$QuestionCodeArray[3].'-'.$QuestionCodeArray[4].'-'.$QuestionCodeArray[5].'-'.$QuestionCodeArray[6];
            $Question = Question::where(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$SeedQuestionCode)->first();
            if($this->isInitialCondition){
                $DifficultyValue = $Question->{cn::QUESTION_PRE_CONFIGURE_DIFFICULTY_VALUE};
            }

            if(!empty($this->SelectedAdjustedCalibrationId)){
                $DifficultyValue = $this->GetDifficultiesValueByCalibrationId($this->SelectedAdjustedCalibrationId, $Question->{cn::QUESTION_TABLE_ID_COL});
            }

            // if($this->SelectedGlobalConfigDifficultyType == 2){
            //     $DifficultyValue = $Question->{cn::QUESTION_AI_DIFFICULTY_VALUE};
            // }else{
            //     $DifficultyValue = $Question->{cn::QUESTION_PRE_CONFIGURE_DIFFICULTY_VALUE};
            // }
        }else{
            if($this->isInitialCondition){
                $DifficultyValue = $QuestionData->{cn::QUESTION_PRE_CONFIGURE_DIFFICULTY_VALUE};
            }

            if(!empty($this->SelectedAdjustedCalibrationId)){
                $DifficultyValue = $this->GetDifficultiesValueByCalibrationId($this->SelectedAdjustedCalibrationId, $QuestionData->{cn::QUESTION_TABLE_ID_COL});
            }

            // if($this->SelectedGlobalConfigDifficultyType == 2){
            //     $DifficultyValue = $QuestionData->{cn::QUESTION_AI_DIFFICULTY_VALUE};
            // }else{
            //     $DifficultyValue = $QuestionData->{cn::QUESTION_PRE_CONFIGURE_DIFFICULTY_VALUE};
            // }
        }
        return $DifficultyValue;
    }

    /**
     * USE : Find Seed Question based on question id
     */
    public function FindSeedQuestionId($QuestionId){
        $QuestionData = Question::find($QuestionId);
        $QuestionCodeArray = explode('-',$QuestionData->{cn::QUESTION_NAMING_STRUCTURE_CODE_COL});
        if(count($QuestionCodeArray) != 7){
            $SeedQuestionCode = $QuestionCodeArray[0].'-'.$QuestionCodeArray[1].'-'.$QuestionCodeArray[2].'-'.$QuestionCodeArray[3].'-'.$QuestionCodeArray[4].'-'.$QuestionCodeArray[5].'-'.$QuestionCodeArray[6];
            $Question = Question::where(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$SeedQuestionCode)->first();
            return $Question->{cn::QUESTION_TABLE_ID_COL} ?? 0;
        }else{
            return $QuestionData->{cn::QUESTION_TABLE_ID_COL} ?? 0;
        }
    }

    /**
     * USE : Find Question Seed Code based on Question id
     */
    public function FindQuestionSeedCode($QuestionId){
        $QuestionData = Question::find($QuestionId);
        $QuestionCodeArray = $QuestionData->{cn::QUESTION_NAMING_STRUCTURE_CODE_COL};
        return $QuestionCodeArray;
    }

    /**
     * USE : Find data step-4 to step-10
     */
    public function FindAICalibrationDifficultiesAndAbilities($params){
        $PreConfigurationDifficultyLevel = array();
        $PreConfigurationDifficultyLevelData = PreConfigurationDiffiltyLevel::get()->toArray();
        if(isset($PreConfigurationDifficultyLevelData)){
            $PreConfigurationDifficultyLevel = array_column($PreConfigurationDifficultyLevelData,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
        }
        
        if(isset($this->IncludedAICalibrationStudents) && !empty($this->IncludedAICalibrationStudents)){
            Log::info('Round Start is :'. $this->CalibrationRound);
            $this->QuestionResults = array();            
            foreach($this->IncludedAICalibrationStudents as $StudentId){
                $AttemptExams =  AttemptExams::whereHas('exam', function($query) use($params){
                                    if($params['test_type'] == 1){
                                        $query->where(cn::EXAM_TYPE_COLS,3);
                                    }
                                    if($params['test_type'] == 2){
                                        $query->where(cn::EXAM_TYPE_COLS,1)->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2);
                                    }
                                    if($params['test_type'] == 3){
                                        $query->where(cn::EXAM_TYPE_COLS,3)
                                        ->orWhere(function($q) use($params){
                                            $q->where(cn::EXAM_TYPE_COLS,1)->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2);
                                        });
                                    }
                                })
                                ->with('exam')
                                ->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$StudentId)
                                ->whereBetween(cn::ATTEMPT_EXAMS_CREATED_AT, [date('Y-m-d',strtotime(str_replace("/","-",$params['start_date']))), date('Y-m-d',strtotime(str_replace("/","-",$params['end_date'])))])
                                ->get();
                $ExamIds = $AttemptExams->pluck(cn::ATTEMPT_EXAMS_EXAM_ID);
                if(!$ExamIds->isEmpty()){
                    $estimate_student_competence = array();
                    // Store result one by one exam
                    foreach($ExamIds as $examId){
                        $AttemptExamData = $AttemptExams->where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)->first();
                        $ExamData = Exam::find($examId);
                        if(isset($ExamData) && !empty($ExamData)){
                            if(!empty($ExamData->question_ids)){
                                $questionIds = explode(',',$ExamData->question_ids);
                                $QuestionList = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)->get();
                                if(isset($QuestionList) && !empty($QuestionList)){
                                    foreach($QuestionList as $QuestionKey => $question){
                                        // Check this question is included in the AI-Calibrated
                                        $getSeedQuestionId = $this->FindSeedQuestionId($question->id);
                                        if(array_key_exists($getSeedQuestionId,$this->IncludedCalibratedQuestions)){

                                        //if(array_key_exists($question->id,$this->IncludedCalibratedQuestions)){
                                            $estimate_student_competence['num_of_ans_list'][] = $this->CountNoOfAnswerByQuestionId($question->id);
                                            $countQuestions = count($QuestionList);
                                            $Answerdetail = $question->answers;
                                            if(isset($AttemptExamData['question_answers'])){
                                                $filterattempQuestionAnswer = array_filter(json_decode($AttemptExamData['question_answers']), function ($var) use($question){
                                                    if($var->question_id == $question['id']){
                                                        return $var ?? [];
                                                    }
                                                });
                                            }
                                            if(isset($filterattempQuestionAnswer) && !empty($filterattempQuestionAnswer)){
                                                foreach($filterattempQuestionAnswer as $fanswer){
                                                    // Save the question result
                                                    if($fanswer->answer == $Answerdetail->{'correct_answer_'.$fanswer->language}){
                                                        $estimate_student_competence['questions_results'][] = true;
                                                        $this->QuestionResults[$question->id][$StudentId] = true;
                                                    }else{
                                                        $estimate_student_competence['questions_results'][] = false;
                                                        $this->QuestionResults[$question->id][$StudentId] = false;
                                                    }

                                                    //if(isset($this->NewAICalibrationQuestionDifficulty) && !empty($this->NewAICalibrationQuestionDifficulty) && !empty($this->NewAICalibrationQuestionDifficulty[$question->id])){
                                                    if(isset($this->NewAICalibrationQuestionDifficulty) && !empty($this->NewAICalibrationQuestionDifficulty) && !empty($this->NewAICalibrationQuestionDifficulty[$getSeedQuestionId])){
                                                        $getSeedQuestionId = $this->FindSeedQuestionId($question->id);
                                                        $this->CurrentAICalibrationQuestionDifficulty[$getSeedQuestionId] = $this->NewAICalibrationQuestionDifficulty[$getSeedQuestionId];
                                                        $estimate_student_competence['difficulty_list'][] = $this->NewAICalibrationQuestionDifficulty[$getSeedQuestionId];
                                                        // $this->CurrentAICalibrationQuestionDifficulty[$question->id] = $this->NewAICalibrationQuestionDifficulty[$question->id];
                                                        // $estimate_student_competence['difficulty_list'][] = $this->NewAICalibrationQuestionDifficulty[$question->id];
                                                    }else{
                                                        // Pre-Defined Question Difficulty
                                                        if(isset($PreConfigurationDifficultyLevel) && !empty($PreConfigurationDifficultyLevel) && isset($PreConfigurationDifficultyLevel[$question->{cn::QUESTION_DIFFICULTY_LEVEL_COL}])){
                                                            //$estimate_student_competence['difficulty_list'][] = $PreConfigurationDifficultyLevel[$question->{cn::QUESTION_DIFFICULTY_LEVEL_COL}];
                                                            $estimate_student_competence['difficulty_list'][] = $this->FindQuestionSeedDifficultyValue($question->id);
                                                        }else{
                                                            $estimate_student_competence['difficulty_list'][] = 0;
                                                        }
                                                    }
                                                }
                                            }
                                        }                                        
                                    }
                                }
                            }
                        }
                    }

                    // Call The AI-API ang get the student estimate-competence-ai-calibration ability
                    if(isset($estimate_student_competence) && !empty($estimate_student_competence)){
                        $requestPayload = new \Illuminate\Http\Request();
                        $requestPayload =   $requestPayload->replace([
                                                'questions_results'     => array($estimate_student_competence['questions_results']),
                                                'num_of_ans_list'       => $estimate_student_competence['num_of_ans_list'],
                                                'difficulty_list'       => array_map('floatval',$estimate_student_competence['difficulty_list']),
                                                'max_student_num'       => 1
                                            ]);
                        
                        $userData = User::with('schools')->find($StudentId);
                        //Log::info('Student Name : '.$userData->DecryptNameEn);
                        //Log::info('School Name : '.$userData->schools->DecryptSchoolNameEn);
                        //Log::info('Class Student Number : '.$userData->class_student_number);
                        $StudentCompetenceAbility = $this->AIApiService->estimate_student_competence($requestPayload);
                        if(!empty($StudentCompetenceAbility)){
                            //Log::info('Student Name Calculated Ability : '.$StudentCompetenceAbility[0]);
                            $this->StudentNewCalibrationAbility[$StudentId] = $StudentCompetenceAbility[0];
                        }
                    }
                }
            }

            // Find Question-AI-Calibration Difficulty
            if(isset($this->QuestionResults) && !empty($this->QuestionResults)){
                $this->NewAICalibrationQuestionDifficulty = array();
                foreach($this->QuestionResults as $QueId => $QueResult){
                    $estimate_question_difficulty = array();
                    foreach($QueResult as $StuId => $studResult){
                        $estimate_question_difficulty['questions_results'][] = $studResult;
                        $estimate_question_difficulty['competence_list'][] = $this->StudentNewCalibrationAbility[$StuId];
                    }
                    // Call The AI-API and get the question estimate-question-difficulty
                    if(isset($estimate_question_difficulty) && !empty($estimate_question_difficulty)){
                        $requestPayload = new \Illuminate\Http\Request();
                        $requestPayload = $requestPayload->replace([
                            'questions_results'     => array($estimate_question_difficulty['questions_results']),
                            'num_of_ans_list'       => array($this->CountNoOfAnswerByQuestionId($QueId)),
                            'competence_list'       => $estimate_question_difficulty['competence_list'],
                            "max_question_num"      => count($estimate_question_difficulty['questions_results']),
                            "max_student_num"       => count($estimate_question_difficulty['competence_list'])
                        ]);
                        $getSeedQuestionId = $this->FindSeedQuestionId($QueId);
                        //Log::info('Current Question Id :'.$QueId);
                        //Log::info('Seed Question Id :'.$getSeedQuestionId);
                        $EstimateCalibrationQuestionDifficulty = $this->AIApiService->estimate_question_difficulty($requestPayload);
                        if(!empty($EstimateCalibrationQuestionDifficulty)){
                            // Find seed question id by current question id
                            if($getSeedQuestionId){
                                //Log::info('Question Seed Code :'.$this->FindQuestionSeedCode($getSeedQuestionId));
                                //Log::info('Question Seed Code Calculated Difficulty:'.$EstimateCalibrationQuestionDifficulty[0]);
                                $this->NewAICalibrationQuestionDifficulty[$getSeedQuestionId] = $EstimateCalibrationQuestionDifficulty[0];
                            }
                            //$this->NewAICalibrationQuestionDifficulty[$QueId] = $EstimateCalibrationQuestionDifficulty[0];
                        }
                    }
                }
            }
        }

        // If the calibration round id not 0 then we will find the  RMSE-A & RMSE-D
        if($this->CalibrationRound!=0){
            if(!empty($this->StudentCurrentCalibrationAbility) && !empty($this->StudentNewCalibrationAbility) && !empty($this->CurrentAICalibrationQuestionDifficulty) && !empty($this->NewAICalibrationQuestionDifficulty)){

                $NormalizedStudentCurrentCalibrationAbility = array();
                foreach(array_values($this->StudentCurrentCalibrationAbility) as $ab){
                    $NormalizedStudentCurrentCalibrationAbility[] = $this->getNormalizedAbility($ab);
                }

                $NormalizedStudentNewCalibrationAbility = array();
                foreach(array_values($this->StudentNewCalibrationAbility) as $newAbility){
                    $NormalizedStudentNewCalibrationAbility[] = $this->getNormalizedAbility($newAbility);
                }
                
                /**
                 * USE : First find the RMSE-A for the student AI-Calibration
                 */
                $requestPayload = new \Illuminate\Http\Request();
                $requestPayload =   $requestPayload->replace([
                                        'current_data'  => $NormalizedStudentCurrentCalibrationAbility,
                                        'new_data'      => $NormalizedStudentNewCalibrationAbility
                                    ]);
                $this->Normalized_RMSE_Ability = $this->AIApiService->RMSE($requestPayload);
                
                $NormalizedCurrentAICalibrationQuestionDifficulty = array();
                foreach(array_map('floatval',array_values($this->CurrentAICalibrationQuestionDifficulty)) as $currentDifficulty){
                    $NormalizedCurrentAICalibrationQuestionDifficulty[] = $this->getNormalizedDifficulty($currentDifficulty);
                }

                $NormalizedNewAICalibrationQuestionDifficulty = array();
                foreach(array_values($this->NewAICalibrationQuestionDifficulty) as $newDifficulty){
                    $NormalizedNewAICalibrationQuestionDifficulty[] = $this->getNormalizedDifficulty($newDifficulty);
                }

                /**
                 * USE : Find RMSE-D for the question difficulties
                 */
                $requestPayload = new \Illuminate\Http\Request();
                $requestPayload =   $requestPayload->replace([
                                        'current_data'  => $NormalizedCurrentAICalibrationQuestionDifficulty,
                                        'new_data'      => $NormalizedNewAICalibrationQuestionDifficulty
                                    ]);
                $this->Normalized_RMSE_Difficulties = $this->AIApiService->RMSE($requestPayload);

                Log::info('AI-Calibration Round Number is :'. $this->CalibrationRound);
                Log::info('AI-Calibration Normalized RMSE-Ability is :'. $this->Normalized_RMSE_Ability);
                Log::info('AI-Calibration Normalized RMSE-Difficulties is :'. $this->Normalized_RMSE_Difficulties);
            }
        }


        if($this->CalibrationRound==0){
            // Set new student ai calibration for the particular student
            $this->StudentCurrentCalibrationAbility = $this->StudentNewCalibrationAbility;

            // Increse the calibration round value
            $this->CalibrationRound = ($this->CalibrationRound + 1);
            
            // If the "CalibrationRound" is 0 then call to callback current function // Go to step 4
            $this->FindAICalibrationDifficultiesAndAbilities($params);

        }elseif(round($this->Normalized_RMSE_Ability,2) >= $this->DefaultCalibrationPercentage || round($this->Normalized_RMSE_Difficulties,2) >= $this->DefaultCalibrationPercentage){

            // Increse the calibration round value
            $this->CalibrationRound = ($this->CalibrationRound + 1);

            // Set new student ai calibration for the particular student
            $this->StudentCurrentCalibrationAbility = $this->StudentNewCalibrationAbility;
            
            //Log::info('Calibration Round Is : '.$this->CalibrationRound);
            if($this->NewAICalibrationQuestionDifficulty){
                $CalibratedQuestionDifficulties = array();
                foreach($this->NewAICalibrationQuestionDifficulty as $QId => $DifficultiesValue){
                    $CalibratedQuestionDifficulties[$this->FindQuestionSeedCode($QId)] = (($DifficultiesValue) - ($this->CalibrationConstant));
                }
            }
            //Log::info('difficulties of all questions : '.json_encode($CalibratedQuestionDifficulties));

            if($this->StudentCurrentCalibrationAbility){
                foreach($this->StudentCurrentCalibrationAbility as $SId => $AbilityValue){
                    $userData = User::with('schools')->find($SId);
                    $UpdatedCalibratedStudentAbility[$userData->class_student_number] = (($AbilityValue) - ($this->CalibrationConstant));
                }
            }

            //Log::info('Abilities of all students : '.json_encode($UpdatedCalibratedStudentAbility));

            // If the our above condition is true then call to callback current function // Go to step 4
            $this->FindAICalibrationDifficultiesAndAbilities($params);
        }else{
            // Set new student ai calibration for the particular student
            $this->StudentCurrentCalibrationAbility = $this->StudentNewCalibrationAbility;

            /**********************************
             * Start Step-9
             * *******************************/
            // Find median calibration difficulties
            $this->MedianCalibrationDifficulties    = $this->FindMedian($this->NewAICalibrationQuestionDifficulty);

            // Find median calibration ability
            $this->MedianStudentAbility         = $this->FindMedian($this->StudentCurrentCalibrationAbility);

            // Find Calibration Constant
            //$this->CalibrationConstant = $this->MedianStudentAbility;
            $this->CalibrationConstant = 0;

            /***********************************
             * End Step-9
            ************************************/

            /**********************************
            * Start Step-10
            *******************************/
            if($this->NewAICalibrationQuestionDifficulty){
                $this->CalibratedQuestionDifficulties = array();
                foreach($this->NewAICalibrationQuestionDifficulty as $QId => $DifficultiesValue){
                    $this->CalibratedQuestionDifficulties[$QId] = (($DifficultiesValue) - ($this->CalibrationConstant));
                }
            }
            if($this->StudentCurrentCalibrationAbility){
                foreach($this->StudentCurrentCalibrationAbility as $SId => $AbilityValue){
                    $this->UpdatedCalibratedStudentAbility[$SId] = (($AbilityValue) - ($this->CalibrationConstant));
                }
            }
            $this->MedianCalibrationAbility = $this->FindMedian($this->UpdatedCalibratedStudentAbility);
            /**********************************
             * End Step-10
            ********************************/
        }
    }

    /**
     * USE : get Single AI-Calibration report data
     */
    public function GetCalibrationReportDetail(Request $request,$id){
        $CalibrationReport = AICalibrationReport::find($id);
        $QuestionIds = explode(',',$CalibrationReport->included_question_ids);
        if(isset($QuestionIds) && !empty($QuestionIds)){
            foreach($QuestionIds as $QuestionId){
                $QuestionData = Question::find($QuestionId);
                if(isset($QuestionData) && !empty($QuestionData)){
                    $CalibratedQuestionDifficulties = json_decode($CalibrationReport->calibrated_question_difficulties,true);
                    $DifficultyWiseQuestionDifficulty[$QuestionData->dificulaty_level][] = $CalibratedQuestionDifficulties[$QuestionId];
                    $QuestionArray = [];
                    $QuestionArray['question_code']                     = $QuestionData->{cn::QUESTION_NAMING_STRUCTURE_CODE_COL};
                    //$QuestionArray['previous_question_difficulties']    = $this->DisplayingDifficulties($QuestionData->{cn::QUESTION_AI_DIFFICULTY_VALUE}).' ('.$QuestionData->{cn::QUESTION_AI_DIFFICULTY_VALUE}.')';
                    $PreviousDifficulty = $this->GetPreviousDifficultiesValueByCalibrationId($id,$QuestionData->{cn::QUESTION_TABLE_ID_COL});
                    $QuestionArray['previous_question_difficulties']    = $this->DisplayingDifficulties($PreviousDifficulty).' ('.$PreviousDifficulty.')';
                    $QuestionArray['new_question_difficulties']         = $this->DisplayingDifficulties($CalibratedQuestionDifficulties[$QuestionId]).' ('.$CalibratedQuestionDifficulties[$QuestionId].')';
                    $QuestionArray['difference_percentage']             = (($this->DisplayingDifficulties($CalibratedQuestionDifficulties[$QuestionId]) - ($this->DisplayingDifficulties($PreviousDifficulty))) * 10);
                    $AICalibrationReport['calibration_questions'][]     = $QuestionArray;
                }
            }
        }

        // Find Median difficulty level & Standard Deviation difficulty levels
        if(isset($DifficultyWiseQuestionDifficulty) && !empty($DifficultyWiseQuestionDifficulty)){                
            foreach($DifficultyWiseQuestionDifficulty as $level => $values){
                // Median difficulty levels
                $AICalibrationReport['median_difficulty_levels'][$level] = $this->FindMedian($values);

                // Find Standard Deviation
                $AICalibrationReport['standard_deviation_difficulty_levels'][$level] = $this->Standard_Deviation($values);
            }
        }

        $IncludedStudentIds = explode(',',$CalibrationReport->included_student_ids);
        if(isset($IncludedStudentIds) && !empty($IncludedStudentIds)){
            foreach($IncludedStudentIds as $StudentId){
                $StudentData = User::withTrashed()->with(['schools','curriculum_year_mapping'])->find($StudentId);
                if(isset($StudentData) && !empty($StudentData)){
                    $CalibratedStudentAbility = json_decode($CalibrationReport->calibrated_student_ability,true);
                    $StudentArray = [];
                    $StudentArray['student_id'] = $StudentId;
                    $StudentArray['school_name'] = $StudentData->schools->DecryptSchoolNameEn;
                    $StudentArray['email'] = $StudentData->email;
                    $StudentArray['permanent_reference_number'] = $StudentData->permanent_reference_number ?? null;
                    $StudentArray['calibration_abilities'] = $this->DisplayingAbilities($CalibratedStudentAbility[$StudentId]).' ('.$CalibratedStudentAbility[$StudentId].')';
                    $AICalibrationReport['calibration_students'][] = $StudentArray;
                }
            }
        }
        $isUpdatedCalibration = false;
        if(CalibrationQuestionLog::where('calibration_report_id',$id)->exists()){
            $isUpdatedCalibration = true;
        }
        return view('backend.ai_calibration.calibration_report_data',compact('CalibrationReport','AICalibrationReport','isUpdatedCalibration'));
    }

    /**
     * USE : Get AI-Calibration report data
     */
    public function GetAICalibrationReportData($params){
        if(isset($params) && !empty($params)){
            // Get the report data
            $CalibrationReport['start_date']                    = $this->DateConvertToYMD($params['start_date']);
            $CalibrationReport['end_date']                      = $this->DateConvertToYMD($params['end_date']);
            $CalibrationReport['no_of_involved_school']         = count($params['schoolIds']);
            $CalibrationReport['no_of_involved_student']        = count($this->IncludedAICalibrationStudents);
            $CalibrationReport['no_of_involved_question_seed']  = count($this->IncludedCalibratedQuestions);

            // Set test type options
            if($params['test_type'] == 1){
                $CalibrationReport['test_type'] = 'Tests';
            }
            if($params['test_type'] == 2){
                $CalibrationReport['test_type'] = 'Testing Zone';
            }
            if($params['test_type'] == 3){
                $CalibrationReport['test_type'] = 'Tests & Testing Zone';
            }

            // Set ai-calibration constant
            $CalibrationReport['calibration_constant'] = $this->CalibrationConstant;
            $CalibrationReport['median_calibration_abilities'] = $this->DisplayingAbilities($this->MedianStudentAbility).'('.$this->MedianStudentAbility.')';

            // Involved question seeds
            $DifficultyWiseQuestionDifficulty = array();
            if(isset($this->IncludedCalibratedQuestions) && !empty($this->IncludedCalibratedQuestions)){
                $QuestionIds = array_keys($this->IncludedCalibratedQuestions);
                if(isset($QuestionIds) && !empty($QuestionIds)){
                    foreach($QuestionIds as $QuestionId){
                        $QuestionData = Question::find($QuestionId);
                        if(isset($QuestionData) && !empty($QuestionData)){
                            $DifficultyWiseQuestionDifficulty[$QuestionData->{cn::QUESTION_DIFFICULTY_LEVEL_COL}][] = $this->CalibratedQuestionDifficulties[$QuestionId];
                            $QuestionArray = [];
                            $QuestionArray['question_code']                     = $QuestionData->naming_structure_code;
                            $QuestionArray['previous_question_difficulties']    = $this->DisplayingDifficulties($QuestionData->{cn::QUESTION_AI_DIFFICULTY_VALUE}).' ('.$QuestionData->{cn::QUESTION_AI_DIFFICULTY_VALUE}.')';
                            $QuestionArray['new_question_difficulties']         = $this->DisplayingDifficulties($this->CalibratedQuestionDifficulties[$QuestionId]).' ('.$this->CalibratedQuestionDifficulties[$QuestionId].')';
                            $QuestionArray['difference_percentage']             = (($this->DisplayingDifficulties($this->CalibratedQuestionDifficulties[$QuestionId]) - ($this->DisplayingDifficulties($QuestionData->{cn::QUESTION_AI_DIFFICULTY_VALUE}))) * 10);
                            $CalibrationReport['calibration_questions'][]       = $QuestionArray;
                        }
                    }
                }
            }

            // Find Median difficulty level & Standard Deviation difficulty levels
            if(isset($DifficultyWiseQuestionDifficulty) && !empty($DifficultyWiseQuestionDifficulty)){                
                foreach($DifficultyWiseQuestionDifficulty as $level => $values){
                    // Median difficulty levels
                    $CalibrationReport['median_difficulty_levels'][$level]              = $this->FindMedian($values);

                    // Find Standard Deviation
                    $CalibrationReport['standard_deviation_difficulty_levels'][$level]  = $this->Standard_Deviation($values);
                }
            }
            
            // Involved students report data
            if(isset($this->IncludedAICalibrationStudents) && !empty($this->IncludedAICalibrationStudents)){
                foreach($this->IncludedAICalibrationStudents as $StudentId){
                    $StudentData = User::withTrashed()->with(['schools','curriculum_year_mapping'])->find($StudentId);
                    if(isset($StudentData) && !empty($StudentData)){
                        $StudentArray = [];
                        $StudentArray['student_id']                     = $StudentId;
                        $StudentArray['school_name']                    = $StudentData->schools->DecryptSchoolNameEn;
                        $StudentArray['email']                          = $StudentData->email;
                        $StudentArray['permanent_reference_number']     = $StudentData->permanent_reference_number ?? null;
                        $StudentArray['calibration_abilities']          = $this->DisplayingAbilities($this->UpdatedCalibratedStudentAbility[$StudentId]).' ('.$this->UpdatedCalibratedStudentAbility[$StudentId].')';
                        $CalibrationReport['calibration_students'][]    = $StudentArray;
                    }
                }
            }



            //Save the calibration data in database table
            $AICalibrationReport =  AICalibrationReport::find($params['calibration_report_id'])->Update([
                                        cn::AI_CALIBRATION_REPORT_REFERENCE_CALIBRATION_COL         => $params['reference_adjusted_calibration'] ?? null,
                                        cn::AI_CALIBRATION_REPORT_SCHOOL_IDS_COL                    => implode(',',$params['schoolIds']) ?? null,
                                        cn::AI_CALIBRATION_REPORT_STUDENT_IDS_COL                   => implode(',',$params['studentIds']) ?? null,
                                        cn::AI_CALIBRATION_REPORT_TEST_TYPE_COL                     => $params['test_type'] ?? null,
                                        cn::AI_CALIBRATION_REPORT_EXCLUDED_QUESTION_IDS_COL         => implode(',',array_keys($this->ExcludeCalibratedQuestions)) ?? null,
                                        cn::AI_CALIBRATION_REPORT_INCLUDED_QUESTION_IDS_COL         => implode(',',array_keys($this->IncludedCalibratedQuestions)) ?? null,
                                        cn::AI_CALIBRATION_REPORT_INCLUDED_STUDENT_IDS_COL          => implode(',',$this->IncludedAICalibrationStudents) ?? null,
                                        cn::AI_CALIBRATION_REPORT_MEDIAN_CALIBRATION_DIFFICULTIES_COL => $this->MedianCalibrationDifficulties ?? null,
                                        cn::AI_CALIBRATION_REPORT_MEDIAN_STUDENT_ABILITY_COL        => $this->MedianStudentAbility ?? null,
                                        cn::AI_CALIBRATION_REPORT_CALIBRATION_CONSTANT_COL          => $this->CalibrationConstant ?? null,
                                        cn::AI_CALIBRATION_REPORT_CURRENT_QUESTION_DIFFICULTIES_COL => json_encode($this->NewAICalibrationQuestionDifficulty) ?? null,
                                        cn::AI_CALIBRATION_REPORT_CALIBRATED_QUESTION_DIFFICULTIES_COL => json_encode($this->CalibratedQuestionDifficulties) ?? null,
                                        cn::AI_CALIBRATION_REPORT_CURRENT_STUDENT_ABILITY_COL       => json_encode($this->StudentCurrentCalibrationAbility) ?? null,
                                        cn::AI_CALIBRATION_REPORT_CALIBRATED_STUDENT_ABILITY_COL    => json_encode($this->UpdatedCalibratedStudentAbility) ?? null,
                                        cn::AI_CALIBRATION_REPORT_MEDIAN_CALIBRATION_ABILITY_COL    => $this->MedianCalibrationAbility ?? null,
                                        cn::AI_CALIBRATION_REPORT_REPORT_DATA_COL                   => json_encode($CalibrationReport) ?? null,
                                        cn::AI_CALIBRATION_REPORT_MEDIAN_DIFFICULTY_LEVELS_COL      => json_encode($CalibrationReport['median_difficulty_levels']) ?? null,
                                        cn::AI_CALIBRATION_REPORT_STANDARD_DEVIATION_DIFFICULTY_LEVELS_COL => json_encode($CalibrationReport['standard_deviation_difficulty_levels']) ?? null,
                                        cn::AI_CALIBRATION_REPORT_STATUS_COL                        => 'complete'
                                    ]);
            if(isset($AICalibrationReport) && !empty($AICalibrationReport)){                
                // After complete calibration process will notify to super admin via email
                $this->SendMailToAdmin('complete_calibration');
            }
        }
        return $CalibrationReport;
    }

    /**
     * USE : Find the median value
     */
    public function FindMedian($data){
        $Median = 0;

        // Get array values
        $ReIndexArray = array_values($data);

        //sorting array
        sort($ReIndexArray);

        // Get array length
        $length = count($ReIndexArray);

        // Divide length by 2
        $half_length = $length / 2;

        // Convert to integer
        $median_index = (int) $half_length;

        // Get median_1
        $median_1 = $ReIndexArray[($median_index-1)];

        // Get Median_2
        $median_2 = $ReIndexArray[($median_index)];

        // find median value
        $Median = ((($median_1) + ($median_2)) / 2);
        
        return $Median;
    }

    /**
     * USE : Find Standard Deviation for question difficulty
     */
    public function Standard_Deviation($arr){
        $arr = array_values($arr);
        sort($arr);
        $num_of_elements = count($arr);
        $variance = 0.0;
        // calculating mean using array_sum() method
        $average = array_sum($arr)/$num_of_elements;
        foreach($arr as $i){
            // sum of squares of differences between 
            // all numbers and means.
            $variance += pow(($i - $average), 2);
        }
        return (float)sqrt($variance/$num_of_elements);
    }

    /**
     * USE : GenerateCalibrationNumber
     */
    public function GenerateCalibrationNumber(){
        $CalibrationNumber = 100001;
        $MaxReferenceNumber = AICalibrationReport::max(cn::AI_CALIBRATION_REPORT_CALIBRATION_NUMBER_COL);
        if(!empty($MaxReferenceNumber)){
            $MaxReferenceNumber = ($MaxReferenceNumber + 1);
        }else{
            $MaxReferenceNumber = $CalibrationNumber;
        }
        return $MaxReferenceNumber;
    }

    /**
     * USE : ExecuteCalibrationAdjustment
     */
    public function ExecuteCalibrationAdjustment($CalibrationReportId, Request $request){
        if(isset($CalibrationReportId) && !empty($CalibrationReportId)){

            $AICalibrationReport            = AICalibrationReport::find($CalibrationReportId)->toArray();
            $CalibrationQuestionDifficulty  = (array) json_decode($AICalibrationReport['calibrated_question_difficulties']);
            $MedianDifficultyLevel          = (array) json_decode($AICalibrationReport['median_difficulty_levels']);

            // Action perform on Included Questions
            if(isset($AICalibrationReport) && !empty($AICalibrationReport[cn::AI_CALIBRATION_REPORT_INCLUDED_QUESTION_IDS_COL])){
                $IncludedQuestionIds = explode(',',$AICalibrationReport[cn::AI_CALIBRATION_REPORT_INCLUDED_QUESTION_IDS_COL]);
                if(isset($IncludedQuestionIds) && !empty($IncludedQuestionIds)){
                    foreach($IncludedQuestionIds as $IncludeQuestionId){
                        $QuestionData = Question::find($IncludeQuestionId);
                        $CalibrationLog = array();
                        $CalibrationLog['question_log_type']            = 'include';
                        $CalibrationLog['calibration_report_id']        = $CalibrationReportId;
                        $CalibrationLog['question_id']                  = $IncludeQuestionId;
                        $CalibrationLog['seed_question_id']             = $IncludeQuestionId;
                        $CalibrationLog['previous_ai_difficulty']       = $QuestionData->{cn::QUESTION_AI_DIFFICULTY_VALUE};
                        $CalibrationLog['calibration_difficulty']       = $CalibrationQuestionDifficulty[$IncludeQuestionId];
                        $CalibrationLog['change_difference']            = (($this->DisplayingDifficulties($CalibrationQuestionDifficulty[$IncludeQuestionId]) - $this->DisplayingDifficulties($QuestionData->{cn::QUESTION_AI_DIFFICULTY_VALUE})) * 10);
                        $CalibrationLog['median_of_difficulty_level']   = $MedianDifficultyLevel[$QuestionData->{cn::QUESTION_DIFFICULTY_LEVEL_COL}];

                        // Store data in calibration log file
                        CalibrationQuestionLog::Create($CalibrationLog);

                        if($this->isUpdate){
                            Question::find($IncludeQuestionId)->Update([cn::QUESTION_AI_DIFFICULTY_VALUE => $CalibrationQuestionDifficulty[$IncludeQuestionId]]);
                        }

                        // Update Seed Child Questions
                        $this->UpdateDifficultyChildQuestions($IncludeQuestionId,$CalibrationReportId,'include'); // $IncludeQuestionId = Seed Question Id
                    }
                }
            }

            AICalibrationReport::find($CalibrationReportId)->Update([
                cn::AI_CALIBRATION_REPORT_STATUS_COL => 'adjusted',
                cn::AI_CALIBRATION_REPORT_UPDATE_EXCLUDE_QUESTION_DIFFICULTY_COL => $request->isUpdateNonIncludedQuestions
            ]);

            // Action perform on excluded questions
            $ExcludedQuestionCalibrationLogs = array();
            $isUpdateNonIncludedQuestions = $request->isUpdateNonIncludedQuestions;
            if($isUpdateNonIncludedQuestions == 'yes'){
                if(isset($AICalibrationReport) && !empty($AICalibrationReport[cn::AI_CALIBRATION_REPORT_EXCLUDED_QUESTION_IDS_COL])){
                    $ExcludedQuestionIds = explode(',',$AICalibrationReport[cn::AI_CALIBRATION_REPORT_EXCLUDED_QUESTION_IDS_COL]);
                    if(isset($ExcludedQuestionIds) && !empty($ExcludedQuestionIds)){
                        foreach($ExcludedQuestionIds as $ExcludeQuestionId){
                            $QuestionData = Question::find($ExcludeQuestionId);
                            $CalibrationLog = array();
                            $CalibrationLog['question_log_type']            = 'exclude';
                            $CalibrationLog['calibration_report_id']        = $CalibrationReportId;
                            $CalibrationLog['question_id']                  = $ExcludeQuestionId;
                            $CalibrationLog['seed_question_id']             = $ExcludeQuestionId;
                            $CalibrationLog['previous_ai_difficulty']       = $QuestionData->{cn::QUESTION_AI_DIFFICULTY_VALUE};
                            $CalibrationLog['calibration_difficulty']       = $MedianDifficultyLevel[$QuestionData->{cn::QUESTION_DIFFICULTY_LEVEL_COL}];
                            $CalibrationLog['change_difference']            = (($this->DisplayingDifficulties($MedianDifficultyLevel[$QuestionData->{cn::QUESTION_DIFFICULTY_LEVEL_COL}]) - $this->DisplayingDifficulties($QuestionData->{cn::QUESTION_AI_DIFFICULTY_VALUE})) * 10);
                            $CalibrationLog['median_of_difficulty_level']   = $MedianDifficultyLevel[$QuestionData->{cn::QUESTION_DIFFICULTY_LEVEL_COL}];

                            // Store data in calibration log file
                            CalibrationQuestionLog::Create($CalibrationLog);

                            if($this->isUpdate){
                                Question::find($ExcludeQuestionId)->Update([cn::QUESTION_AI_DIFFICULTY_VALUE => $MedianDifficultyLevel[$QuestionData->{cn::QUESTION_DIFFICULTY_LEVEL_COL}]]);
                            }

                            // Update Seed Child Questions
                            $this->UpdateDifficultyChildQuestions($ExcludeQuestionId,$CalibrationReportId,'exclude'); // $ExcludeQuestionId = Seed Question Id
                        }
                    }
                }
            }
            $redirectUrl = 'ai-calibration/question-log/'.$CalibrationReportId;
            return $this->sendResponse(['redirect_url' => $redirectUrl]);
        }
    }

    /**
     * USE : Update Difficulty Child Questions
     */
    public function UpdateDifficultyChildQuestions($SeedQuestionId, $CalibrationReportId, $LogType){
        if(isset($SeedQuestionId)){
            // Fetch Calibration data
            $AICalibrationReport = AICalibrationReport::find($CalibrationReportId)->toArray();
            $CalibrationQuestionDifficulty = (array) json_decode($AICalibrationReport['calibrated_question_difficulties']);
            $MedianDifficultyLevel = (array) json_decode($AICalibrationReport['median_difficulty_levels']);

            $SeedQuestion = Question::find($SeedQuestionId);
            if(isset($SeedQuestion) && !empty($SeedQuestion)){
                $SeedQuestionCode = $SeedQuestion->{cn::QUESTION_NAMING_STRUCTURE_CODE_COL};
                // Find Child Questions
                $ChildQuestionList = Question::where(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,'like','%'.$SeedQuestionCode.'%')
                            ->where(cn::QUESTION_QUESTION_TYPE_COL,'!=',4)
                            ->get();
                if($ChildQuestionList){
                    foreach($ChildQuestionList as $Question){
                        $QuestionData = $ChildQuestionList->where('id',$Question->{cn::QUESTION_TABLE_ID_COL})->first();
                        if($QuestionData){
                            $CalibrationLog = array();
                            $CalibrationLog['question_log_type']            = $LogType;
                            $CalibrationLog['calibration_report_id']        = $CalibrationReportId;
                            $CalibrationLog['question_id']                  = $QuestionData->{cn::QUESTION_TABLE_ID_COL};
                            $CalibrationLog['seed_question_id']             = $SeedQuestionId;
                            $CalibrationLog['previous_ai_difficulty']       = $QuestionData->{cn::QUESTION_AI_DIFFICULTY_VALUE};

                            if($LogType == 'include'){
                                $CalibrationLog['calibration_difficulty']   = $CalibrationQuestionDifficulty[$SeedQuestionId];
                                $CalibrationLog['change_difference']        = (($this->DisplayingDifficulties($CalibrationQuestionDifficulty[$SeedQuestionId]) - $this->DisplayingDifficulties($QuestionData->{cn::QUESTION_AI_DIFFICULTY_VALUE})) * 10);
                            }

                            if($LogType == 'exclude'){
                                $CalibrationLog['calibration_difficulty']   = $MedianDifficultyLevel[$SeedQuestion->{cn::QUESTION_DIFFICULTY_LEVEL_COL}];
                                $CalibrationLog['change_difference']        = (($this->DisplayingDifficulties($MedianDifficultyLevel[$SeedQuestion->{cn::QUESTION_DIFFICULTY_LEVEL_COL}]) - $this->DisplayingDifficulties($QuestionData->{cn::QUESTION_AI_DIFFICULTY_VALUE})) * 10);
                            }
                            
                            $CalibrationLog['median_of_difficulty_level']   = $MedianDifficultyLevel[$SeedQuestion->{cn::QUESTION_DIFFICULTY_LEVEL_COL}];

                            // Store data in calibration log file
                            CalibrationQuestionLog::Create($CalibrationLog);

                            if($this->isUpdate){
                                if($LogType == 'include'){
                                    Question::find($Question->{cn::QUESTION_TABLE_ID_COL})->Update([cn::QUESTION_AI_DIFFICULTY_VALUE => $CalibrationQuestionDifficulty[$SeedQuestionId]]);
                                }
                                if($LogType == 'exclude'){
                                    Question::find($Question->{cn::QUESTION_TABLE_ID_COL})->Update([cn::QUESTION_AI_DIFFICULTY_VALUE => $MedianDifficultyLevel[$SeedQuestion->{cn::QUESTION_DIFFICULTY_LEVEL_COL}]]);
                                }
                            }
                        }
                    }
                }
            }
        }
        
    }

    /**
     * USE : Calibration List
     */
    public function CalibrationList(Request $request){
        $items = $request->items ?? 10;
        $CalibrationData = AICalibrationReport::orderBy(cn::AI_CALIBRATION_REPORT_ID_COL,'DESC')->paginate($items);
        if(isset($request->filter) && !empty($request->filter)){
            $Query = AICalibrationReport::query();
            if($request->Search){
                $Query->where(cn::AI_CALIBRATION_REPORT_CALIBRATION_NUMBER_COL,$request->Search);
            }
            if($request->from_date){
                $from_date = $this->DateConvertToYMD($request->from_date);
                $Query->where(cn::AI_CALIBRATION_REPORT_START_DATE_COL,'>=',$from_date);
            }
            if($request->to_date){
                $to_date = $this->DateConvertToYMD($request->to_date);
                $Query->where(cn::AI_CALIBRATION_REPORT_END_DATE_COL,'<=',$to_date);
            }
            $CalibrationData = $Query->orderBy(cn::AI_CALIBRATION_REPORT_ID_COL,'DESC')->paginate($items);
        }
        return view('backend.ai_calibration.list',compact('CalibrationData'));
    }

    /**
     * USE : Notify using Mail
     */
    public function SendMailToAdmin($MailType){
        $EmailData = array();
        $Email = 'prajapatimanoj1432@gmail.com';
        switch($MailType){
            case 'start_calibration':
                $Subject = "Calibration Process Start";
                $sendEmail = $this->sendMails('email.start_calibration', $EmailData, $Email, $Subject, [], []);
                break;
            case 'complete_calibration':
                $Subject = "Calibration process complete";
                $sendEmail = $this->sendMails('email.complete_calibration', $EmailData, $Email, $Subject, [], []);
                break;
            default:
                break;
        }        
    }
}