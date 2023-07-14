<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use Illuminate\Support\Facades\Auth;
use App\Constants\DbConstant As cn;
use Exception;
use App\Models\User;
use App\Jobs\UpdateStudentOverAllAbility;
use App\Models\ExamSchoolMapping;
use App\Models\Exam;
use App\Jobs\UpdateMyTeachingReportJob;
use App\Jobs\UpdateMyTeachingTableJob;
use App\Jobs\UpdateUserCreditPointsJob;
use App\Jobs\UpdateQuestionEColumnJob;
use App\Jobs\UpdateExamReferenceNumberJob;
use App\Jobs\SendRemainderUploadStudentNewSchoolCurriculumYearJob;
use App\Jobs\CloneSchoolDataNextCurriculumYear;
use App\Jobs\SetDefaultCurriculumYearStudentJob;
use App\Jobs\UpdateAttemptExamsTableJob;
use App\Http\Controllers\Reports\AlpAiGraphController;
use App\Models\GradeClassMapping;
use App\Models\GradeSchoolMappings;
use App\Models\MyTeachingReport;
use App\Models\AttemptExams;
use App\Models\PeerGroup;
use App\Models\Question;
use Log;
use App\Helpers\Helper;
use App\Models\ExamGradeClassMappingModel;
use App\Models\CurriculumYearStudentMappings;
use App\Models\ClassPromotionHistory;
use App\Models\RemainderUpdateSchoolYearData;
use App\Http\Services\AIApiService;
use Carbon\Carbon;
use App\Models\ParentChildMapping;
use App\Models\CurriculumYear;
use App\Models\AttemptExamStudentMapping;
use App\Models\ExamCreditPointRulesMapping;
use App\Models\SubjectSchoolMappings;
use App\Models\TeachersClassSubjectAssign;
use App\Models\PeerGroupMember;
use App\Models\ClassSubjectMapping;
use App\Models\ExamConfigurationsDetails;
use App\Models\School;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\LearningUnitsProgressReport;
use App\Models\LearningObjectivesProgressReport;
use App\Models\WeatherDetail;
use App\Jobs\UpdateLearningProgressReportJob;
use App\Models\UserCreditPointHistory;
use App\Events\UserActivityLog;
use App\Http\Services\WeatherAPIService;
use App\Jobs\UpdateCountUsedQuestionAnswerJob;

class CronJobController extends Controller
{
    use Common, ResponseFormat;

    protected   $AIApiService,
                $CloneSchoolDataNextCurriculumYear,
                $UpdateMyTeachingReportJob,
                $User,
                $WeatherAPIService;
    
    public function __construct(){
        $this->AIApiService = new AIApiService();
        $this->CloneSchoolDataNextCurriculumYear = new CloneSchoolDataNextCurriculumYear;
        $this->UpdateMyTeachingReportJob = new UpdateMyTeachingReportJob;
        $this->User = new User;
        $this->WeatherAPIService = new WeatherAPIService;
    }

    public function UpdateUserCreditPointTable(){
        $UserCreditPointHistory = UserCreditPointHistory::with('getExam')->get();
        foreach($UserCreditPointHistory as $data){
            if($data->exam_id){
                if($data->getExam->exam_type == 2){
                    UserCreditPointHistory::find($data->id)->Update([
                        cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL => 'exercise',
                        cn::USER_CREDIT_POINT_HISTORY_SELF_LEARNING_TYPE_COL => null
                    ]);
                }
    
                if($data->getExam->exam_type == 3){
                    UserCreditPointHistory::find($data->id)->Update([
                        cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL => 'test',
                        cn::USER_CREDIT_POINT_HISTORY_SELF_LEARNING_TYPE_COL => null
                    ]);
                }
    
                if($data->getExam->exam_type == 1){
                    if($data->getExam->self_learning_test_type == 1){
                        UserCreditPointHistory::find($data->id)->Update([
                            cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL => 'self_learning',
                            cn::USER_CREDIT_POINT_HISTORY_SELF_LEARNING_TYPE_COL => null
                        ]);
                    }
    
                    if($data->getExam->self_learning_test_type == 2){
                        UserCreditPointHistory::find($data->id)->Update([
                            cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL => 'assessment',
                            cn::USER_CREDIT_POINT_HISTORY_SELF_LEARNING_TYPE_COL => null
                        ]);
                    }
                }
            }
        }
    }

    /**
     * USE : Cron job for update learning progress reports
     */
    public function UpdateLearningProgressJob($StudentId = Null){
        if(isset($StudentId) && !empty($StudentId)){
            $AttemptedStudentIds = [$StudentId];
        }else{
            $AttemptedStudentIds = AttemptExams::pluck(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID)->unique()->toArray();
        }
        if(isset($AttemptedStudentIds) && !empty($AttemptedStudentIds)){
            dispatch(new UpdateLearningProgressReportJob($AttemptedStudentIds))->delay(now()->addSeconds(1));
        }
    }

    /**
     * USE : Update Learning Progress Report cron job
     */
    public function UpdateLearningProgress($AttemptedStudentIds){
        ini_set('max_execution_time', -1);
        $ReportTypes = $this->ReportTypes();

        // Find all the students
        $StudentList =  User::withTrashed()
                        ->where([
                            cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                        ])
                        ->whereIn(cn::USERS_ID_COL,$AttemptedStudentIds)
                        ->orderBy(cn::USERS_ID_COL,'desc')
                        ->get();
        if(isset($StudentList) && !empty($StudentList)){
            // Get pre-configured data for the questions
            $PreConfigurationDifficultyLevel = array();
            $PreConfigurationDiffiltyLevelData = PreConfigurationDiffiltyLevel::get()->toArray();
            if(isset($PreConfigurationDiffiltyLevelData)){
                $PreConfigurationDifficultyLevel = array_column($PreConfigurationDiffiltyLevelData,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
            }
            
            foreach($StudentList as $Student){
                Log::info('Student Id :'. $Student->id);
                $StudentId = $Student->id;
                if(isset($ReportTypes) && !empty($ReportTypes)){
                    foreach($ReportTypes as $ReportType){
                        $ProgressReportLearningUnit = array();
                        $ProgressReportLearningObjectives = array();
                        // Get all strands
                        $StrandList = Strands::all();
                        if(!empty($StrandList)){
                            foreach($StrandList as $strand){
                                $strandId = $strand->id;
                                $LearningUnits = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->id)->where('stage_id','<>',3)->get()->toArray(); //$this->GetLearningUnits($strandId);
                                $learningUnitsIds = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->id)->where('stage_id','<>',3)->pluck(cn::LEARNING_UNITS_ID_COL)->toArray();
                                if(!empty($learningUnitsIds)){
                                    foreach($learningUnitsIds as $learningUnitId){
                                        $LearningsObjectivesLbl = LearningsObjectives::IsAvailableQuestion()->where('stage_id','<>',3)->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitId)->pluck('title_'.app()->getLocale(),cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                                        $learningObjectivesIds = LearningsObjectives::IsAvailableQuestion()
                                                                ->where('stage_id','<>',3)
                                                                ->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitId)
                                                                ->pluck(cn::LEARNING_OBJECTIVES_ID_COL)
                                                                ->toArray();
                                        if(isset($learningObjectivesIds) && !empty($learningObjectivesIds)){
                                            $no_of_learning_objectives = count($learningObjectivesIds);
                                            $countNoOfAchievedLearningObjectives = 0;
                                            foreach($learningObjectivesIds as $learningObjectivesId){
                                                $learningObjectivesData = LearningsObjectives::where('stage_id','<>',3)->find($learningObjectivesId);
                                                $StudentLearningObjectiveAbility = 0;
                                                $countLearningObjectivesQuestion = 0;
                                                $StrandUnitsObjectivesMappingsId =  StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandId)
                                                                                    ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$learningUnitId)
                                                                                    ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$learningObjectivesId)
                                                                                    ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
                                                if(isset($StrandUnitsObjectivesMappingsId) && !empty($StrandUnitsObjectivesMappingsId)){
                                                    $QuestionsList = Question::with('answers')
                                                                    ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$StrandUnitsObjectivesMappingsId)
                                                                    ->orderBy(cn::QUESTION_TABLE_ID_COL)
                                                                    ->get()
                                                                    ->toArray();
                                                    if(isset($QuestionsList) && !empty($QuestionsList)){
                                                        $QuestionsDataList = array_column($QuestionsList,cn::QUESTION_TABLE_ID_COL);
                                                        $StudentAttemptedExamIds =  AttemptExams::where([
                                                                                        cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID => $StudentId
                                                                                    ])->pluck(cn::ATTEMPT_EXAMS_EXAM_ID);

                                                        if(isset($StudentAttemptedExamIds) && !empty($StudentAttemptedExamIds)){
                                                            $ExamList = Exam::with(['attempt_exams' => fn($query) => $query->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, $StudentId)])
                                                                        ->whereHas('attempt_exams', function($q) use($StudentId){
                                                                            $q->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, '=', $StudentId);
                                                                        })
                                                                        ->where(function ($query) use ($ReportType){
                                                                            if(isset($ReportType) && $ReportType == 'all'){
                                                                                $query->where(cn::EXAM_TYPE_COLS,3)
                                                                                ->orWhere(function($q){
                                                                                    $q->where(cn::EXAM_TYPE_COLS,1)->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2);
                                                                                });
                                                                            }
                                                                            if(isset($ReportType) && $ReportType == 'test'){
                                                                                $query->where(cn::EXAM_TYPE_COLS,3);  // 3 = test type = 'Test'
                                                                            }
                                                                            if(isset($ReportType) && $ReportType == 'testing_zone'){
                                                                                $query->where(cn::EXAM_TYPE_COLS,1)->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2);
                                                                            }
                                                                        })
                                                                        ->whereIn(cn::EXAM_TABLE_ID_COLS,$StudentAttemptedExamIds)
                                                                        ->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')
                                                                        ->get()
                                                                        ->toArray();
                                                            if(isset($ExamList) && !empty($ExamList)){
                                                                $ApiRequestData = array();
                                                                foreach($ExamList as $ExamData){
                                                                    if($countLearningObjectivesQuestion > $this->getGlobalConfiguration('question_window_size_of_learning_objective')){
                                                                        break;
                                                                    }
                                                                    if($countLearningObjectivesQuestion < $this->getGlobalConfiguration('question_window_size_of_learning_objective')){
                                                                        if(isset($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                                            if(isset($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                                                $filterAttemptQuestionAnswer = json_decode($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL],true);
                                                                            }
                                                                        }
                                                                        foreach($filterAttemptQuestionAnswer as $filterAttemptQuestionAnswerKey => $filterAttemptQuestionAnswerValue){
                                                                            if(in_array($filterAttemptQuestionAnswerValue['question_id'],$QuestionsDataList)){
                                                                                $countLearningObjectivesQuestion += count(array_intersect(explode(',',$ExamData['question_ids']),$QuestionsDataList));
                                                                                $QuestionsDataListFinal[] = $filterAttemptQuestionAnswerValue['question_id'];
                                                                                $QuestionList = Question::with('answers')->where(cn::QUESTION_TABLE_ID_COL,$filterAttemptQuestionAnswerValue['question_id'])->get()->toArray();        
                                                                                if(isset($PreConfigurationDifficultyLevel) && !empty($PreConfigurationDifficultyLevel) && isset($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]])){
                                                                                    $ApiRequestData['difficulty_list'][] = number_format($this->GetDifficultiesValueByCalibrationId($ExamData[cn::EXAM_CALIBRATION_ID_COL],$QuestionList[0]['id']), 4, '.', '');
                                                                                }else{
                                                                                    $ApiRequestData['difficulty_list'][] = 0;
                                                                                }
        
                                                                                $AnswerCount = 0;
                                                                                for($ans = 1; $ans <= 4; $ans++){
                                                                                    if(trim($QuestionList[0]['answers']['answer'.$ans.'_en']) != ""){
                                                                                        $AnswerCount++;
                                                                                    }
                                                                                }
                                                                                $ApiRequestData['num_of_ans_list'][] = $AnswerCount;
                                                                                if($filterAttemptQuestionAnswerValue['answer'] == $QuestionList[0]['answers']['correct_answer_'.$ExamData['attempt_exams'][0]['language']]){
                                                                                    $ApiRequestData['questions_results'][] = true;
                                                                                }else{
                                                                                    $ApiRequestData['questions_results'][] = false;
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                                $Ability = 0;
                                                                if(isset($ApiRequestData) && !empty($ApiRequestData)){
                                                                    $requestPayload = new \Illuminate\Http\Request();
                                                                    $requestPayload = $requestPayload->replace([
                                                                        'questions_results'=> array($ApiRequestData['questions_results']),
                                                                        'num_of_ans_list' => $ApiRequestData['num_of_ans_list'],
                                                                        'difficulty_list' => array_map('floatval', $ApiRequestData['difficulty_list']),
                                                                        'max_student_num' => 1
                                                                    ]);
                                                                    $data = $this->AIApiService->getStudentProgressReport($requestPayload);
                                                                    if(isset($data) && !empty($data) && isset($data[0]) && !empty($data[0])){
                                                                        $Ability = $data[0];
                                                                    }
                                                                }

                                                                if($countLearningObjectivesQuestion > $this->getGlobalConfiguration('min_no_question_per_study_progress')){
                                                                    $StudentLearningObjectiveAbility = $Ability ?? 0;
                                                                    $ProgressReportLearningObjectives[$strandId][$learningUnitId][$learningObjectivesData->id] = array(
                                                                        'learning_objective_id' => $learningObjectivesData->id,
                                                                        'ability' => $StudentLearningObjectiveAbility,
                                                                        'normalizedAbility' => Helper::getNormalizedAbility($StudentLearningObjectiveAbility),
                                                                        'study_status' => Helper::getAbilityType($StudentLearningObjectiveAbility),
                                                                        'studyStatusColor' => Helper::getGlobalConfiguration(Helper::getAbilityType($StudentLearningObjectiveAbility))
                                                                    );
                                                                }else{
                                                                    // Store array into student ability
                                                                    $ProgressReportLearningObjectives[$strandId][$learningUnitId][$learningObjectivesData->id] = array(
                                                                        'learning_objective_id' => $learningObjectivesData->id,
                                                                        'ability' => $StudentLearningObjectiveAbility,
                                                                        'normalizedAbility' => $StudentLearningObjectiveAbility,
                                                                        'study_status' => Helper::getAbilityType($StudentLearningObjectiveAbility),
                                                                        'studyStatusColor' => Helper::getGlobalConfiguration(Helper::getAbilityType($StudentLearningObjectiveAbility))
                                                                    );
                                                                }
                                                            }else{
                                                                $ProgressReportLearningObjectives[$strandId][$learningUnitId][$learningObjectivesData->id] = array(
                                                                    'learning_objective_id' => $learningObjectivesData->id,
                                                                    'ability' => $StudentLearningObjectiveAbility,
                                                                    'normalizedAbility' => $StudentLearningObjectiveAbility,
                                                                    'study_status' => Helper::getAbilityType($StudentLearningObjectiveAbility),
                                                                    'studyStatusColor' => Helper::getGlobalConfiguration('incomplete_color')
                                                                );
                                                            }
                                                        }
                                                    }else{
                                                        $ProgressReportLearningObjectives[$strandId][$learningUnitId][$learningObjectivesData->id] = array(
                                                            'learning_objective_id' => $learningObjectivesData->id,
                                                            'ability' => $StudentLearningObjectiveAbility,
                                                            'normalizedAbility' => $StudentLearningObjectiveAbility,
                                                            'studystatus' => Helper::getAbilityType($StudentLearningObjectiveAbility),
                                                            'studyStatusColor' => Helper::getGlobalConfiguration('incomplete_color')
                                                        );
                                                    }
                                                }

                                                if($this->CheckLearningObjectivesMastered($StudentLearningObjectiveAbility)){
                                                    $countNoOfAchievedLearningObjectives++;
                                                }
                                            }

                                            $ProgressReportLearningUnit[$strandId][$learningUnitId] = array(
                                                'no_of_learning_objectives' => $no_of_learning_objectives,
                                                'no_of_achieved_objectives' => $countNoOfAchievedLearningObjectives,
                                                'achieved_percentage' => (($countNoOfAchievedLearningObjectives/$no_of_learning_objectives) * 100),
                                                'to_be_achieved_percentage' => (100 - (($countNoOfAchievedLearningObjectives/$no_of_learning_objectives) * 100))
                                            );
                                        }
                                    }
                                }
                            }
                        }

                        // Store Learning Objectives progress data
                        $this->SaveLearningObjectivesProgress($ProgressReportLearningObjectives,$StudentId,$ReportType);

                        // Store progress data in database
                        $this->SaveLearningUnitProgress($ProgressReportLearningUnit,$StudentId,$ReportType);
                    }
                }
            }
        }
    }

    /**
     * USE : Save learning units progress data in database
     */
    public function SaveLearningUnitProgress($ProgressData,$StudentId,$ReportType){
        switch($ReportType){
            case 'all':
                if(LearningUnitsProgressReport::where(cn::LEARNING_UNITS_PROGRESS_REPORT_STUDENT_ID,$StudentId)->exists()){
                    LearningUnitsProgressReport::where(cn::LEARNING_UNITS_PROGRESS_REPORT_STUDENT_ID,$StudentId)
                    ->Update([
                        cn::LEARNING_UNITS_PROGRESS_REPORT_LEARNING_PROGRESS_ALL_COL => json_encode($ProgressData)
                    ]);
                }else{
                    LearningUnitsProgressReport::Create([
                        cn::LEARNING_UNITS_PROGRESS_REPORT_STUDENT_ID => $StudentId,
                        cn::LEARNING_UNITS_PROGRESS_REPORT_LEARNING_PROGRESS_ALL_COL => json_encode($ProgressData)
                    ]);
                }
                break;
            case 'test':
                if(LearningUnitsProgressReport::where(cn::LEARNING_UNITS_PROGRESS_REPORT_STUDENT_ID,$StudentId)->exists()){
                    LearningUnitsProgressReport::where(cn::LEARNING_UNITS_PROGRESS_REPORT_STUDENT_ID,$StudentId)
                    ->Update([
                        cn::LEARNING_UNITS_PROGRESS_REPORT_LEARNING_PROGRESS_TEST_COL => json_encode($ProgressData)
                    ]);
                }else{
                    LearningUnitsProgressReport::Create([
                        cn::LEARNING_UNITS_PROGRESS_REPORT_STUDENT_ID => $StudentId,
                        cn::LEARNING_UNITS_PROGRESS_REPORT_LEARNING_PROGRESS_TEST_COL => json_encode($ProgressData)
                    ]);
                }
                break;
            case 'testing_zone':
                if(LearningUnitsProgressReport::where(cn::LEARNING_UNITS_PROGRESS_REPORT_STUDENT_ID,$StudentId)->exists()){
                    LearningUnitsProgressReport::where(cn::LEARNING_UNITS_PROGRESS_REPORT_STUDENT_ID,$StudentId)
                    ->Update([
                        cn::LEARNING_UNITS_PROGRESS_REPORT_LEARNING_PROGRESS_TESTING_ZONE_COL => json_encode($ProgressData)
                    ]);
                }else{
                    LearningUnitsProgressReport::Create([
                        cn::LEARNING_UNITS_PROGRESS_REPORT_STUDENT_ID => $StudentId,
                        cn::LEARNING_UNITS_PROGRESS_REPORT_LEARNING_PROGRESS_TESTING_ZONE_COL => json_encode($ProgressData)
                    ]);
                }
                break;
        }
    }

    /**
     * USE : Save learning objectives progress data in database
     */
    public function SaveLearningObjectivesProgress($ProgressData,$StudentId,$ReportType){
        switch($ReportType){
            case 'all':
                if(LearningObjectivesProgressReport::where(cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_STUDENT_ID,$StudentId)->exists()){
                    LearningObjectivesProgressReport::where(cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_STUDENT_ID,$StudentId)
                    ->Update([
                        cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_LEARNING_PROGRESS_ALL_COL => json_encode($ProgressData)
                    ]);
                }else{
                    LearningObjectivesProgressReport::Create([
                        cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_STUDENT_ID => $StudentId,
                        cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_LEARNING_PROGRESS_ALL_COL => json_encode($ProgressData)
                    ]);
                }
                break;
            case 'test':
                if(LearningObjectivesProgressReport::where(cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_STUDENT_ID,$StudentId)->exists()){
                    LearningObjectivesProgressReport::where(cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_STUDENT_ID,$StudentId)
                    ->Update([
                        cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_LEARNING_PROGRESS_TEST_COL => json_encode($ProgressData)
                    ]);
                }else{
                    LearningObjectivesProgressReport::Create([
                        cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_STUDENT_ID => $StudentId,
                        cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_LEARNING_PROGRESS_TEST_COL => json_encode($ProgressData)
                    ]);
                }
                break;
            case 'testing_zone':
                if(LearningObjectivesProgressReport::where(cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_STUDENT_ID,$StudentId)->exists()){
                    LearningObjectivesProgressReport::where(cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_STUDENT_ID,$StudentId)
                    ->Update([
                        cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_LEARNING_PROGRESS_TESTING_ZONE_COL => json_encode($ProgressData)
                    ]);
                }else{
                    LearningObjectivesProgressReport::Create([
                        cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_STUDENT_ID => $StudentId,
                        cn::LEARNING_OBJECTIVES_PROGRESS_REPORT_LEARNING_PROGRESS_TESTING_ZONE_COL => json_encode($ProgressData)
                    ]);
                }
                break;
        }
    }

    public function ReportTypes(){
        return [
            'all',
            'test',
            'testing_zone'
        ];
    }

    /**
     * USE : Update My Teaching Table via cron job urls
     *  Update via all records
     */
    public function updateMyTeachingReports(){
        dispatch(new UpdateMyTeachingReportJob)->delay(now()->addSeconds(1));   
    }

    /**
     * USE : Update My Teaching Table after student attempt exam
     *  update via school id and exam id
     */
    public function UpdateMyTeachingTable($schoolId, $examId){
        if(!empty($schoolId) && !empty($examId)){
            dispatch(new UpdateMyTeachingTableJob($schoolId, $examId))->delay(now()->addSeconds(1));
        }
    }

    /**
     * USE : Update All Student Over All Ability
     */
    public function UpdateAllStudentAbility(){
        $Students = $this->User->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->get();
        if(!$Students->isEmpty()){
            foreach($Students as $student){
                dispatch(new UpdateStudentOverAllAbility($student))->delay(now()->addSeconds(1));
            }
        }
    }

    /**
     * USE : Update Single Student Over All Ability
     */
    function UpdateStudentOverAllAbility(){
        dispatch(new UpdateStudentOverAllAbility(Auth::user()))->delay(now()->addSeconds(1));
    }

    function UpdateStudentOverAllAbilityNew($student){
        if(isset($student) && !empty($student)){
            dispatch(new UpdateStudentOverAllAbility($student))->delay(now()->addSeconds(1));
        }
    }

    /**
     * USE : Remove duplicate assigned student
     */
    public function RemoveDuplicateStudent(){
        $ExamList = Exam::all();
        if(!empty($ExamList)){
            foreach($ExamList as $exam){
                if(isset($exam->{cn::EXAM_TABLE_STUDENT_IDS_COL}) && !empty($exam->{cn::EXAM_TABLE_STUDENT_IDS_COL})){
                    $studentIds = implode(',',array_unique(explode(',',$exam->{cn::EXAM_TABLE_STUDENT_IDS_COL})));
                    Exam::find($exam->id)->Update([cn::EXAM_TABLE_STUDENT_IDS_COL => $studentIds]);
                }
            }
        }
    }

    /**
     * USE : Assign Credit Point to student via system
     */
    public function UpdateStudentCreditPoints($ExamId, $StudentId){
        if(!empty($ExamId) && !empty($StudentId)){
            $SchoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            dispatch(new UpdateUserCreditPointsJob($ExamId, $StudentId, $SchoolId))->delay(now()->addSeconds(1));
        }
    }

    /**
     * USE : Assign credit points manually to students.
     */
    public function AssignCreditPointsManually(){
        $AttemptExams = AttemptExams::get();
        foreach($AttemptExams as $data){
            $SchoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            dispatch(new UpdateUserCreditPointsJob($data->{cn::ATTEMPT_EXAMS_EXAM_ID}, $data->{cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID}, $SchoolId))->delay(now()->addSeconds(1));
        }
    }

    /**
     * USE : Update Questions based on question codes
     */
    public function updateQuestionEColumn(){
        dispatch(new UpdateQuestionEColumnJob())->delay(now()->addSeconds(1));
    }

    /**
     * USE : Update Exam Reference Number Cronjob
     */
    public function UpdateExamReferenceNumber(){
        dispatch(new UpdateExamReferenceNumberJob())->delay(now()->addSeconds(1));
        echo "Job Completed Successfully";
    }

    public function UpdateAttemptExamTable(){
        dispatch(new UpdateAttemptExamsTableJob())->delay(now()->addSeconds(1));
        echo "Job Completed Successfully";
    }

    /**
     * USE : Set Default existing student curriculum year
     */
    public function SetDefaultCurriculumYear(){
        //dispatch(new SetDefaultCurriculumYearStudentJob())->delay(now()->addSeconds(1));
        $StudentList = User::withTrashed()->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->get();
        if(isset($StudentList) && !empty($StudentList)){
            foreach($StudentList as $student){
                CurriculumYearStudentMappings::updateOrCreate([
                    cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL => $student->{cn::USERS_ID_COL},
                    cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID,
                    cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL => $student->{cn::USERS_SCHOOL_ID_COL},
                    cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL => (!empty($student->{cn::USERS_GRADE_ID_COL})) ? $student->{cn::USERS_GRADE_ID_COL} : null,
                    cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL => (!empty($student->{cn::USERS_CLASS_ID_COL})) ? $student->{cn::USERS_CLASS_ID_COL} : null,
                    // cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL => (!empty($student->CurriculumYearGradeId)) ? $student->CurriculumYearGradeId : null,
                    // cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL => (!empty($student->CurriculumYearClassId)) ? $student->CurriculumYearClassId : null,
                    cn::CURRICULUM_YEAR_STUDENT_NUMBER_WITHIN_CLASS_COL => $student->{cn::STUDENT_NUMBER_WITHIN_CLASS} ?? Null,
                    cn::CURRICULUM_YEAR_STUDENT_CLASS => $student->{cn::USERS_CLASS} ?? NUll,
                    cn::CURRICULUM_YEAR_CLASS_STUDENT_NUMBER => $student->{cn::USERS_CLASS_STUDENT_NUMBER} ?? NULL
                ]);

                ClassPromotionHistory::Create([
                    //cn::CLASS_PROMOTION_HISTORY_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                    cn::CLASS_PROMOTION_HISTORY_CURRICULUM_YEAR_ID_COL => 23,
                    cn::CLASS_PROMOTION_HISTORY_SCHOOL_ID_COL => $student->{cn::USERS_SCHOOL_ID_COL},
                    cn::CLASS_PROMOTION_HISTORY_STUDENT_ID_COL => $student->{cn::USERS_ID_COL},
                    cn::CLASS_PROMOTION_HISTORY_CURRENT_GRADE_ID_COL => null,
                    cn::CLASS_PROMOTION_HISTORY_CURRENT_CLASS_ID_COL => null,
                    cn::CLASS_PROMOTION_HISTORY_PROMOTED_GRADE_ID_COL => (!empty($student->{cn::USERS_GRADE_ID_COL})) ? $student->{cn::USERS_GRADE_ID_COL} : null,
                    cn::CLASS_PROMOTION_HISTORY_PROMOTED_CLASS_ID_COL => (!empty($student->{cn::USERS_CLASS_ID_COL})) ? $student->{cn::USERS_CLASS_ID_COL} : null,
                    // cn::CLASS_PROMOTION_HISTORY_PROMOTED_GRADE_ID_COL => (!empty($student->CurriculumYearGradeId)) ? $student->CurriculumYearGradeId : null,
                    // cn::CLASS_PROMOTION_HISTORY_PROMOTED_CLASS_ID_COL => (!empty($student->CurriculumYearClassId)) ? $student->CurriculumYearClassId : null,
                    cn::CLASS_PROMOTION_HISTORY_PROMOTED_BY_USER_ID_COL => 1
                ]);
            }
            echo 'Updated successfully';
        }
    }

    /**
     * USE : Update Question Option From A to B In Attempted Exam Update Option.
     */
    public function UpdateStudentSelectedAnswer(){
        ini_set('max_execution_time', -1);
        $questionId= 747;
        $ExamIds = Exam::whereRaw("find_in_set($questionId,question_ids)")->withTrashed()->get()->pluck('id')->toArray();
        //$apiData = [];
        if(isset($ExamIds) && !empty($ExamIds)){
            foreach($ExamIds as $ExamId){
                $examDetail = Exam::find($ExamId);
                $CalibrationId = $examDetail->{cn::EXAM_CALIBRATION_ID_COL};
                $AttemptedAnswerData = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$ExamId)->get();
                if(isset($AttemptedAnswerData) && !empty($AttemptedAnswerData)){
                    foreach($AttemptedAnswerData as $AttemptedAnswer){
                        $questionAnswersData = json_decode($AttemptedAnswer->{cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL},true);
                        $AttemptFirstTrialData = json_decode($AttemptedAnswer->{cn::ATTEMPT_EXAMS_ATTEMPT_FIRST_TRIAL_COL},true);
                        $AttemptSecondTrialData = json_decode($AttemptedAnswer->{cn::ATTEMPT_EXAMS_ATTEMPT_SECOND_TRIAL_COL},true);

                        // Update First-trial column
                        if(isset($AttemptFirstTrialData) && !empty($AttemptFirstTrialData)){
                            foreach($AttemptFirstTrialData as $firstTrialKey => $firstTrialData){
                                if($firstTrialData['answer']==1){
                                    $AttemptFirstTrialData[$firstTrialKey]['answer'] = 2;
                                }
                                if($firstTrialData['answer']==2){
                                    $AttemptFirstTrialData[$firstTrialKey]['answer'] = 1;
                                }
                            }
                        }

                        // Update Second-trial column
                        if(isset($AttemptSecondTrialData) && !empty($AttemptSecondTrialData)){
                            foreach($AttemptSecondTrialData as $secondTrialKey => $secondTrialData){
                                if($secondTrialData['answer']==1){
                                    $AttemptSecondTrialData[$secondTrialKey]['answer'] = 2;
                                }
                                if($secondTrialData['answer']==2){
                                    $AttemptSecondTrialData[$secondTrialKey]['answer'] = 1;
                                }
                            }
                        }

                        //update Question Answer Data
                        if(!empty($questionAnswersData)){
                            $NoOfCorrectAnswers = 0;
                            $NoOfWrongAnswers = 0;
                            $apiData = [];
                            foreach($questionAnswersData as $key => $questionAnswer){
                                if($questionAnswer['answer']==1){
                                    $questionAnswersData[$key]['answer'] = 2;
                                }
                                if($questionAnswer['answer']==2){
                                    $questionAnswersData[$key]['answer'] = 1;
                                }
                                // Get Questions Answers and difficulty level
                                $responseData = $this->GetQuestionNumOfAnswerAndDifficultyValue($questionAnswer['question_id'],$CalibrationId);
                                $apiData['num_of_ans_list'][] = $responseData['noOfAnswers'];
                                $apiData['difficulty_list'][] = $responseData['difficulty_value'];
                                $apiData['max_student_num'] = 1;
                            
                                //For check answer
                                $answer = $questionAnswer['answer'];
                                $QuestionAnswerDetail = Question::where(cn::QUESTION_TABLE_ID_COL,$questionAnswer['question_id'])->with('answers')->first();
                                if(isset($QuestionAnswerDetail)){
                                    if($QuestionAnswerDetail->answers->{'correct_answer_'.$questionAnswer['language']} == $answer){
                                        $NoOfCorrectAnswers = ($NoOfCorrectAnswers + 1);
                                        $apiData['questions_results'][] = true;
                                    }else{
                                        $NoOfWrongAnswers = ($NoOfWrongAnswers + 1);
                                        $apiData['questions_results'][] = false;
                                    }
                                } 
                            }
                            $StudentAbility = '';
                            if(!empty($apiData)){                    
                                // Get the student ability from calling AIApi
                                $StudentAbility = $this->GetAIStudentAbility($apiData);
                            }

                            $PostData = [                    
                                cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL => (!empty($questionAnswersData)) ? json_encode($questionAnswersData) : null,
                                cn::ATTEMPT_EXAMS_ATTEMPT_FIRST_TRIAL_COL => (!empty($AttemptFirstTrialData)) ? json_encode($AttemptFirstTrialData) : null,
                                cn::ATTEMPT_EXAMS_ATTEMPT_SECOND_TRIAL_COL => (!empty($AttemptSecondTrialData)) ? json_encode($AttemptSecondTrialData) : null,
                                cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS => $NoOfCorrectAnswers,
                                cn::ATTEMPT_EXAMS_TOTAL_WRONG_ANSWERS => $NoOfWrongAnswers,
                                cn::ATTEMPT_EXAMS_STUDENT_ABILITY_COL => ($StudentAbility!='') ? $StudentAbility : null
                            ];

                            $Update = AttemptExams::find($AttemptedAnswer->id)->Update($PostData);
                            if($Update){
                                /** Start Update overall ability for the student **/
                                $this->UpdateStudentOverAllAbility();

                                /** Update My Teaching Table Via Cron Job */
                                $userData = User::find($AttemptedAnswer->student_id);
                                $this->UpdateMyTeachingTable($userData->{cn::USERS_SCHOOL_ID_COL}, $ExamId);

                                if($examDetail->exam_type == 2 || ($examDetail->exam_type == 1 && $examDetail->self_learning_test_type == 1)){
                                    /** Update Student Credit Points via cron job */
                                    $this->UpdateStudentCreditPoints($ExamId, $AttemptedAnswer->student_id);
                                }
                            }
                        }
                    }
                }
            }
        }else{
            echo 'No any exams in use this question';
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
     * USE : Run cron-job for email reminder for every schools
     */
    public function SendRemainderUploadStudentNewSchoolCurriculumYear(){
        if(in_array($this->CurrentDate(),$this->getMondayDates(date('Y'),date('09')))){
            dispatch(new SendRemainderUploadStudentNewSchoolCurriculumYearJob())->delay(now()->addSeconds(1));
        }
    }

    /**
     * USE : Copy & Clone school year data to next curriculum year
     */
    public function CopyCloneCurriculumYearSchoolData(){
        //$this->info('Hourly Update has been send successfully');
        Log::info('Schedule Run Start: Copy and Clone School Data');
        dispatch($this->CloneSchoolDataNextCurriculumYear)->delay(now()->addSeconds(1));
        Log::info('Schedule Run Successfully: Copy and Clone School Data');
    }

    /**
     * USE : Update Curriculum year in global configuration automatically after running the cron job
     */
    public function UpdateGlobalConfigurationNextCurriculumYear(){
        $this->UpdateGlobalConfigurationCurriculumYear();
    }

    /***
     * USE : Remove Users Record From Child table.
     */
    public function RemoveUserDataFromAllTables($userIds){
        if(!empty($userIds)){
            $UserIdsArr = explode(',',$userIds);
            foreach($UserIdsArr as $id){
                $userData = User::withTrashed()->find($id);
                if(isset($userData) && !empty($userData)){
                    $UserRole = $userData->{cn::USERS_ROLE_ID_COL};
                    $SchoolId = $userData->{cn::USERS_SCHOOL_ID_COL};
                    if($UserRole){
                        switch($UserRole){
                            case cn::TEACHER_ROLE_ID ://2
                                $ExamIDs = Exam::where(cn::EXAM_TABLE_CREATED_BY_COL,$id)->pluck(cn::EXAM_TABLE_ID_COLS)->toArray();
                                $PeerGroupIds = PeerGroup::where(cn::PEER_GROUP_CREATED_BY_USER_ID_COL,$id)->pluck(cn::PEER_GROUP_ID_COL)->toArray();
                                if(!empty($PeerGroupIds)){
                                    $PeerGroupMember = PeerGroupMember::whereIn(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL,$PeerGroupIds)->delete();
                                }
                                PeerGroup::where(cn::PEER_GROUP_CREATED_BY_USER_ID_COL,$id)->delete();
                                Exam::where(cn::EXAM_TABLE_CREATED_BY_COL,$id)->delete();
                                TeachersClassSubjectAssign::where(cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL,$id)->delete();
                                MyTeachingReport::whereIn(cn::TEACHING_REPORT_EXAM_ID_COL,$ExamIDs)->delete();
                                AttemptExams::whereIn(cn::ATTEMPT_EXAMS_EXAM_ID,$ExamIDs)->delete();
                                User::where(cn::USERS_ID_COL,$id)->delete();
                                break;
                            case cn::STUDENT_ROLE_ID ://3
                                $ExamIDs = Exam::where(function($query) use($id){
                                    $query->where(cn::EXAM_TABLE_STUDENT_IDS_COL,$id)
                                        ->orWhere(function($q) use($id){
                                            $q->whereRaw("find_in_set($id,student_ids)");
                                        });
                                })->pluck(cn::EXAM_TABLE_ID_COLS)
                                ->toArray();
                            
                                if(!empty($ExamIDs)){
                                    foreach($ExamIDs as $exam){
                                        $UniqueStudentIdArray = [];
                                        $examData = Exam::find($exam);
                                        $checkStudentData = explode(',',$examData->student_ids);
                                        if(count($checkStudentData) > 1){
                                            $key = array_search($id, $checkStudentData);
                                            if ($key !== false) {
                                                unset($checkStudentData[$key]);
                                            }
                                            Exam::where(cn::EXAM_TABLE_ID_COLS,$exam)
                                            ->update([
                                                cn::EXAM_TABLE_STUDENT_IDS_COL => !empty($checkStudentData) ? implode(',',$checkStudentData) : Null
                                            ]);
                                            ExamGradeClassMappingModel::where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$exam)->update([
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL   => !empty($checkStudentData) ? implode(',',$checkStudentData) : Null
                                            ]);
                                            MyTeachingReport::where(cn::TEACHING_REPORT_EXAM_ID_COL,$exam)->update([
                                                cn::TEACHING_REPORT_STUDENT_IDS_COL   => !empty($checkStudentData) ? implode(',',$checkStudentData) : Null
                                            ]);
                                        }else{
                                            Exam::where(cn::EXAM_TABLE_ID_COLS,$exam)->delete();
                                            ExamGradeClassMappingModel::where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$exam)->delete();
                                            MyTeachingReport::where(cn::TEACHING_REPORT_EXAM_ID_COL,$exam)->delete();
                                        }
                                    }
                                }
                                //Remove form Comma separate
                                AttemptExams::where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$id)->delete();
                                AttemptExamStudentMapping::where(cn::ATTEMPT_EXAM_STUDENT_MAPPING_STUDENT_ID_COL,$id)->delete();
                                PeerGroupMember::where(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL,$id)->delete();
                                ClassPromotionHistory::where(cn::CLASS_PROMOTION_HISTORY_STUDENT_ID_COL,$id)->delete();
                                CurriculumYearStudentMappings::where(cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL,$id)->delete();
                                ParentChildMapping::where(cn::PARANT_CHILD_MAPPING_STUDENT_ID_COL,$id)->delete();
                                MyTeachingReport::where(cn::TEACHING_REPORT_STUDENT_IDS_COL,$id)->delete();
                                User::where(cn::USERS_ID_COL,$id)->delete();
                                break; 

                            case cn::SCHOOL_ROLE_ID ://5
                                $AllUsers = $this->getRoleBasedUserForParticularSchool([
                                            cn::TEACHER_ROLE_ID,
                                            cn::STUDENT_ROLE_ID,
                                            cn::PARENT_ROLE_ID,
                                            cn::SCHOOL_ROLE_ID,
                                            cn::EXTERNAL_RESOURCE_ROLE_ID,
                                            cn::PRINCIPAL_ROLE_ID,
                                            cn::PANEL_HEAD_ROLE_ID,
                                            cn::CO_ORDINATOR_ROLE_ID
                                        ],$SchoolId);
                                $userID = $this->getRoleBasedUserForParticularSchool(cn::STUDENT_ROLE_ID,$SchoolId);
                                if(!empty($userID)){
                                    $ExamIDs = Exam::where(function($query) use($SchoolId){
                                                    $query->where(cn::EXAM_TABLE_SCHOOL_COLS,$SchoolId)
                                                        ->orWhere(function($q) use($SchoolId){
                                                            $q->whereRaw("find_in_set($SchoolId,school_id)");
                                                        });
                                                })->pluck(cn::EXAM_TABLE_ID_COLS)
                                                ->toArray();
                                    if(!empty($ExamIDs)){
                                        foreach($ExamIDs as $exam){
                                            $UniqueStudentIdArray = [];
                                            $examData = Exam::find($exam);
                                            $checkSchoolData = explode(',',$examData->school_id);
                                            $checkStudentData = explode(',',$examData->student_ids);
                                            if(count($checkSchoolData) > 1){
                                                $key = array_search($SchoolId, $checkSchoolData);
                                                if ($key !== false) {
                                                    unset($checkSchoolData[$key]);
                                                    if(!empty($checkStudentData)){
                                                        $UniqueStudentIdArray = array_diff($checkStudentData,$userID);
                                                    }
                                                }
                                                Exam::where(cn::EXAM_TABLE_ID_COLS,$exam)
                                                ->update([
                                                    cn::EXAM_TABLE_SCHOOL_COLS => !empty($checkSchoolData) ? implode(',',$checkSchoolData) : Null,
                                                    cn::EXAM_TABLE_STUDENT_IDS_COL => !empty($UniqueStudentIdArray) ? implode(',',$UniqueStudentIdArray) : Null
                                                ]);
                                            }else{
                                                Exam::where(cn::EXAM_TABLE_ID_COLS,$exam)->delete();
                                            }
                                        }
                                    }
                                }
                                // Remove Child Record From Table
                                AttemptExams::whereIn(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$userID)->delete();
                                AttemptExamStudentMapping::whereIn(cn::ATTEMPT_EXAM_STUDENT_MAPPING_STUDENT_ID_COL,$userID)->delete();
                                PeerGroupMember::whereIn(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL,$userID)->delete();
                                //ExamConfigurationsDetails::whereIn('created_by_user_id',$userID)->delete();
                            
                                ClassSubjectMapping::where(cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL,$SchoolId)->delete();
                                ClassPromotionHistory::where(cn::CLASS_PROMOTION_HISTORY_STUDENT_ID_COL,$SchoolId)->delete();
                                CurriculumYearStudentMappings::where(cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL,$SchoolId)->delete();
                                ExamCreditPointRulesMapping::where(cn::EXAM_CREDIT_POINT_RULES_MAPPING_SCHOOL_ID_COL,$SchoolId)->delete();
                                ExamGradeClassMappingModel::where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$SchoolId)->delete();
                                ExamSchoolMapping::where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$SchoolId)->delete();
                                GradeSchoolMappings::where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$SchoolId)->delete();
                                GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$SchoolId)->delete();
                                PeerGroup::where(cn::PEER_GROUP_SCHOOL_ID_COL,$SchoolId)->delete();
                                RemainderUpdateSchoolYearData::where(cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_SCHOOL_ID_COL,$SchoolId)->delete();
                                SubjectSchoolMappings::where(cn::SUBJECT_MAPPING_SCHOOL_ID_COL,$SchoolId)->delete();
                                TeachersClassSubjectAssign::where(cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL,$SchoolId)->delete();
                                MyTeachingReport::where(cn::TEACHING_REPORT_STUDENT_IDS_COL,$SchoolId)->delete();
                                School::where(cn::SCHOOL_ID_COLS,$SchoolId);
                                User::whereIn(cn::USERS_ID_COL,$AllUsers)->delete();
                                break;
                            case cn::PRINCIPAL_ROLE_ID ://7
                                $ExamIDs = Exam::where(cn::EXAM_TABLE_CREATED_BY_COL,$id)->pluck(cn::EXAM_TABLE_ID_COLS)->toArray();
                                MyTeachingReport::whereIn(cn::TEACHING_REPORT_EXAM_ID_COL,$ExamIDs)->delete();
                                AttemptExams::whereIn(cn::ATTEMPT_EXAMS_EXAM_ID,$ExamIDs)->delete();
                                Exam::where(cn::EXAM_TABLE_CREATED_BY_COL,$id)->delete();
                                User::where(cn::USERS_ID_COL,$id)->delete();
                                break;
                            case cn::PANEL_HEAD_ROLE_ID :
                            case cn::CO_ORDINATOR_ROLE_ID :
                                $ExamIDs = Exam::where(cn::EXAM_TABLE_CREATED_BY_COL,$id)->pluck(cn::EXAM_TABLE_ID_COLS)->toArray();
                                MyTeachingReport::whereIn(cn::TEACHING_REPORT_EXAM_ID_COL,$ExamIDs)->delete();
                                AttemptExams::whereIn(cn::ATTEMPT_EXAMS_EXAM_ID,$ExamIDs)->delete();
                                $PeerGroupIds = PeerGroup::where(cn::PEER_GROUP_CREATED_BY_USER_ID_COL,$id)->pluck(cn::PEER_GROUP_ID_COL)->toArray();
                                if(!empty($PeerGroupIds)){
                                    $PeerGroupMember = PeerGroupMember::whereIn(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL,$PeerGroupIds)->delete();
                                }
                                PeerGroup::where(cn::PEER_GROUP_CREATED_BY_USER_ID_COL,$id)->delete();
                                User::where(cn::USERS_ID_COL,$id)->delete();
                                break;
                            case cn::PARENT_ROLE_ID ://4
                            case cn::EXTERNAL_RESOURCE_ROLE_ID ://6
                                if($UserRoleId == cn::PARENT_ROLE_ID){   
                                    ParentChildMapping::where(cn::PARANT_CHILD_MAPPING_PARENT_ID_COL,$UserId)->delete();
                                }
                                User::where(cn::USERS_ID_COL,$UserId)->delete();
                                break;
                        }
                    }
                }
            }
        } 
    }

    /**
     * USE : Get Hong-Kong weather details and update into database
     */
    public function UpdateWeatherDetails(){
        Log::info('Weather API Run Start');
        $WeatherInfo = $this->WeatherAPIService->GetWeatherInfo();
        if(isset($WeatherInfo) && !empty($WeatherInfo)){
            $WeatherDetail = WeatherDetail::first();
            if(isset($WeatherDetail) && !empty($WeatherDetail)){
                WeatherDetail::find($WeatherDetail->{cn::WEATHER_DETAIL_ID_COL})->Update([
                    cn::WEATHER_DETAIL_WEATHER_INFO_COL => json_encode($WeatherInfo, TRUE)
                ]);
            }else{
                WeatherDetail::Create([
                    cn::WEATHER_DETAIL_WEATHER_INFO_COL => json_encode($WeatherInfo, TRUE)
                ]);
            }
            Log::info('Weather Information Updated into database');
        }
        Log::info('Weather API Run Successfully');
    }

    /**
     * Use : UpdateCountUsedQuestionAnswer
     */
    public function UpdateCountUsedQuestionAnswer(){
        ini_set('max_execution_time', -1);
        dispatch(new UpdateCountUsedQuestionAnswerJob())->delay(now()->addSeconds(1));
        echo 'Question Answer Counts Updated successfully';
    }
}