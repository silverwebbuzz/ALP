<?php

namespace App\Traits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Constants\DbConstant as cn;
use App\Models\UserActivities;
use App\Models\Exam;
use DateTime;
use App\Models\TeachersClassSubjectAssign;
use App\Models\Grades;
use App\Models\Subjects;
use App\Models\Strands;
use App\Models\AttemptExams;
use App\Models\User;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\AuditLogs;
use App\Models\Nodes;
use App\Models\GradeSchoolMappings;
use App\Models\School;
use App\Models\GradeClassMapping;
use App\Models\ClassPromotionHistory;
use App\Models\GlobalConfiguration;
use App\Models\Question;
use App\Models\Answer;
use App\Models\ExamConfigurationsDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Cookie;
use Illuminate\Database\Eloquent\QueryException;
use App\Models\ExamGradeClassMappingModel;
use App\Models\ExamSchoolMapping;
use App\Models\PeerGroupMember;
use App\Models\CurriculumYear;
use App\Models\PeerGroup;
use App\Models\MyTeachingReport;
use App\Helpers\Helper;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\Languages;
use Carbon\Carbon;
use App\Models\CurriculumYearStudentMappings;
use App\Models\AICalibrationReport;
use App\Models\CalibrationQuestionLog;
use App\Models\LearningUnitOrdering;
use App\Models\LearningObjectiveOrdering;
use App\Events\UserActivityLog;

trait Common {

    /**
     * Save user activity log history
     */
    public function UserActivityLog($UserId = null, $ActivityMessage){
        $ActivityHistory = [
            'user_id' => $UserId,
            'ActivityMessage' => $ActivityMessage
        ];
        event(new UserActivityLog($ActivityHistory));
    }

    /**
     * USE : Get Page Name
     */
    public function GetPageName($ExamId){
        $menuItem = '';
        $ExamData = Exam::find($ExamId);
        switch($ExamData->exam_type){
            case 1:
                if($ExamData->{cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL} == 1){
                    $menuItem = 'self_learning';
                }else{
                    $menuItem = 'testing_zone';
                }
                break;
            case 2:
                $menuItem = 'exercise';
                break;
            case 3:
                $menuItem = 'test';
                break;
        }
        return $menuItem;
    }

    // Get Learning Units
    public function GetLearningUnits($strandId){
        $finalArray = [];
        $strandId = is_array($strandId) ? $strandId : [$strandId];
        if(LearningUnitOrdering::where('school_id',Auth::user()->school_id)->exists()){
            $LearningUnitOrdering =  LearningUnitOrdering::where('school_id',Auth::user()->school_id)
                                    ->whereIn('strand_id',$strandId)
                                    ->orderBy('position','ASC')
                                    ->get();
            if(isset($LearningUnitOrdering) && !empty($LearningUnitOrdering)){
                foreach($LearningUnitOrdering as $UnitOrderKey => $learningUnitData){
                    $LearningsUnits = LearningsUnits::where('id',$learningUnitData->learning_unit_id)->where('stage_id','<>',3)->first();
                    if(isset($LearningsUnits) && !empty($LearningsUnits)){
                        $finalArray[$UnitOrderKey] = $LearningsUnits->toArray();
                        $finalArray[$UnitOrderKey]['position'] = $learningUnitData->position;
                        $finalArray[$UnitOrderKey]['index'] = $learningUnitData->index; //$IndexArray[$position - 1];
                    }
                }
            }
            // $positionArray = $LearningUnitOrdering->pluck('position')->toArray();
            // $IndexArray =   $LearningUnitOrdering->pluck('index')->toArray();
            // foreach($positionArray as $positionKey => $position){
            //     $LearningsUnits = LearningsUnits::where('id',$position)->where('stage_id','<>',3)->first();
            //     if(isset($LearningsUnits) && !empty($LearningsUnits)){
            //         $FindLearningUnitIndex = $LearningUnitOrdering->where('learning_unit_id',$LearningsUnits->id)->first();
            //         $finalArray[$positionKey] = $LearningsUnits->toArray();
            //         $finalArray[$positionKey]['position'] = $position;
            //         $finalArray[$positionKey]['index'] = $FindLearningUnitIndex->index; //$IndexArray[$position - 1];
            //     }
            // }
        }else{
            $LearningUnits = LearningsUnits::where('stage_id','<>',3)->whereIn(cn::LEARNING_UNITS_STRANDID_COL, $strandId)->get();
            foreach($LearningUnits as $learningUnitKey => $UnitData){
                $finalArray[$learningUnitKey] = $UnitData->toArray();
                $finalArray[$learningUnitKey]['index'] = ($learningUnitKey + 1);
            }
        }
        return $finalArray;
    }

    // Get Learning Objectives
    public function GetLearningObjectives($learningUnitId){
        $learningUnitIds = is_array($learningUnitId) ? $learningUnitId : [$learningUnitId];
        $learningObjectiveData = '';
        if(LearningUnitOrdering::where('school_id',Auth::user()->school_id)->exists()){
            $learningObjectiveData = $this->OrderingObjectiveData($learningUnitIds,true);
        }else{
            $learningObjectiveData = $this->OrderingObjectiveData($learningUnitIds,false);
        }
        return $learningObjectiveData;
    }

    // Ordering Objective Data
    public function OrderingObjectiveData($learningUnitId,$learningUnitExists){
        $finalArray = [];
        $learningObjectiveData = [];
        $learningUnitId = (is_array($learningUnitId)) ? $learningUnitId : [$learningUnitId];
        if(LearningObjectiveOrdering::where('school_id',Auth::user()->school_id)->exists() && ($learningUnitExists==false || $learningUnitExists == 0)){

            $learningObjectiveOrdering = LearningObjectiveOrdering::where('school_id',Auth::user()->school_id)
                                ->whereIn('learning_unit_id',$learningUnitId)
                                ->get();
            $positionArray = $learningObjectiveOrdering->pluck('position')->toArray();
            $IndexArray = $learningObjectiveOrdering->pluck('index')->toArray();
            $tempLearningUnitIds = [];
            $counter = 0;
            foreach($positionArray as $positionKey => $position){
                $LearningsObjectives =  LearningsObjectives::where('id',$position)->where('stage_id','<>',3)->first();
                if(isset($LearningsObjectives) && !empty($LearningsObjectives)){
                    $FindLearningObjectiveIndex = $learningObjectiveOrdering->where('learning_objective_id',$LearningsObjectives->id)->first();
                    $finalArray[$positionKey] = $LearningsObjectives->toArray();
                    $finalArray[$positionKey]['position'] = $position;
                    if(!in_array($LearningsObjectives->learning_unit_id,$tempLearningUnitIds)){
                        $tempLearningUnitIds[] = $LearningsObjectives->learning_unit_id;
                        $counter = 0;
                    }
                    $finalArray[$positionKey]['index'] = $LearningsObjectives->learning_unit_id.'.'.(++$counter);
                }
            }
            return $finalArray;
        }elseif(LearningObjectiveOrdering::where('school_id',Auth::user()->school_id)->exists() && ($learningUnitExists == true || $learningUnitExists == 1)){
            foreach($learningUnitId as $unitKey => $unitId){
                $learningObjectiveOrdering = LearningObjectiveOrdering::where('school_id',Auth::user()->school_id)
                                            ->where('learning_unit_id',$unitId)
                                            ->orderBy('position','ASC') // Manoj Added
                                            ->get();
                $positionArray = $learningObjectiveOrdering->pluck('learning_objective_id')->toArray();
                foreach($positionArray as $positionKey => $position){
                    $LearningsObjectives =   LearningsObjectives::where('id',$position)->where('stage_id','<>',3)->first();
                    if(isset($LearningsObjectives) && !empty($LearningsObjectives)){
                        $FindLearningObjectiveIndex = $learningObjectiveOrdering->where('learning_objective_id',$LearningsObjectives['id'])->first();
                        $finalArray[$unitKey][$positionKey] = $LearningsObjectives->toArray();
                        $finalArray[$unitKey][$positionKey]['position'] = $position;
                        $orderingLearningUnit = LearningUnitOrdering::where('school_id',Auth::user()->school_id)->where('learning_unit_id',$LearningsObjectives['learning_unit_id'])->first()->toArray();
                        $finalArray[$unitKey][$positionKey]['index'] = ($orderingLearningUnit['position']).'.'.($positionKey + 1);
                    }
                }
            }
            $learningObjectiveData = array_merge(...$finalArray);
            return $learningObjectiveData;
        }elseif(LearningObjectiveOrdering::where('school_id',Auth::user()->school_id)->doesntExist() && ($learningUnitExists == true || $learningUnitExists == 1)){
            $orderingLearningUnit = LearningUnitOrdering::where('school_id',Auth::user()->school_id)->whereIn('learning_unit_id',$learningUnitId)->orderBy('position','ASC')->get();
            foreach($learningUnitId as $unitKey => $unitId){
                $learningObjectiveOrdering = LearningsObjectives::where('learning_unit_id',$unitId)->get()->toArray();
                foreach($learningObjectiveOrdering as $learningObjectiveOrderingKey => $learningObjective){
                    $finalArray[$unitKey][$learningObjectiveOrderingKey] = $learningObjective;
                    $finalArray[$unitKey][$learningObjectiveOrderingKey]['index'] =  ($orderingLearningUnit[$this->searchForId($unitId, 'learning_unit_id',$orderingLearningUnit->toArray())]['position']).'.'.($learningObjectiveOrderingKey + 1);
                }
            }
            $learningObjectiveData = array_merge(...$finalArray);
            return $learningObjectiveData;
        }else{
            $indexingArray = [];
            foreach($learningUnitId as $unitKey => $unitIds){
                $ObjectiveData = LearningsObjectives::where('learning_unit_id',$unitIds)->where('stage_id','<>',3)->get()->toArray();
                foreach($ObjectiveData as $objectiveKey => $objectiveData){
                    $indexingArray[$objectiveKey] = $objectiveData;
                    $indexingArray[$objectiveKey]['index'] = ($unitIds).'.'.($objectiveKey+1);
                    array_push($learningObjectiveData,$indexingArray[$objectiveKey]);
                }
            }
            return $learningObjectiveData;
        }
    }

    function searchForId($id, $column, $array) {
        foreach ($array as $key => $val) {
            if ($val[$column] == $id) {
                return $key;
            }
        }
        return null;
     }
    
    /**
     * USE : Get current adjusted calibration id
     */
    public function GetCurrentAdjustedCalibrationId(){
        $CalibrationId = null;
        $AICalibrationReport =  AICalibrationReport::where([
                                    cn::AI_CALIBRATION_REPORT_STATUS_COL => 'adjusted'
                                ])
                                ->orderBy(cn::AI_CALIBRATION_REPORT_ID_COL,'DESC')
                                ->first();
        if($AICalibrationReport){
            $CalibrationId = $AICalibrationReport->{cn::AI_CALIBRATION_REPORT_ID_COL};
        }
        return $CalibrationId;
    }

    /**
     * USE : Difficulties value by calibration id
     */
    public function GetDifficultiesValueByCalibrationId($CalibrationId=null,$QuestionId){
        if($CalibrationId){
            $difficulty = 0;
            $CalibrationQuestionLog = CalibrationQuestionLog::where([
                        cn::CALIBRATION_QUESTION_LOG_REPORT_ID_COL      => $CalibrationId,
                        cn::CALIBRATION_QUESTION_LOG_QUESTION_ID_COL    => $QuestionId
                    ])->first();
            if(isset($CalibrationQuestionLog) && isset($CalibrationQuestionLog->{cn::CALIBRATION_QUESTION_LOG_CALIBRATION_DIFFICULTY_COL})){
                $difficulty = $CalibrationQuestionLog->{cn::CALIBRATION_QUESTION_LOG_CALIBRATION_DIFFICULTY_COL} ?? 0;
            }else{
                $Question = Question::find($QuestionId);
                $difficulty = $Question->{cn::QUESTION_AI_DIFFICULTY_VALUE} ?? 0;
            }
        }else{
            $Question = Question::find($QuestionId);
            $difficulty = $Question->{cn::QUESTION_PRE_CONFIGURE_DIFFICULTY_VALUE} ?? 0;
        }
        return $difficulty;
    }

    /**
     * USE : Previous Difficulties value by calibration id
     */
    public function GetPreviousDifficultiesValueByCalibrationId($CalibrationId=null,$QuestionId){
        if($CalibrationId){
            $difficulty = 0;
            $CalibrationQuestionLog = CalibrationQuestionLog::where([
                        cn::CALIBRATION_QUESTION_LOG_REPORT_ID_COL      => $CalibrationId,
                        cn::CALIBRATION_QUESTION_LOG_QUESTION_ID_COL    => $QuestionId
                    ])->first();
            if(isset($CalibrationQuestionLog) && isset($CalibrationQuestionLog->{cn::CALIBRATION_QUESTION_LOG_PREVIOUS_AI_DIFFICULTY_COL})){
                $difficulty = $CalibrationQuestionLog->{cn::CALIBRATION_QUESTION_LOG_PREVIOUS_AI_DIFFICULTY_COL} ?? 0;
            }else{
                $Question = Question::find($QuestionId);
                $difficulty = $Question->{cn::QUESTION_PRE_CONFIGURE_DIFFICULTY_VALUE} ?? 0;
            }
        }else{
            $Question = Question::find($QuestionId);
            $difficulty = $Question->{cn::QUESTION_PRE_CONFIGURE_DIFFICULTY_VALUE} ?? 0;
        }
        return $difficulty;
    }

    /**
     * USE : Get Pre-Defined difficulties value by level
     */
    public function GetPreConfigDifficultiesValueByLevel($DifficultyLevel){
        $PreConfigurationDifficultyLevel = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL,$DifficultyLevel)->first();
        return $PreConfigurationDifficultyLevel->{cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL} ?? 0;
    }

    /**
     * USE : Get AI-difficulties value by level
     */
    public function GetAIDifficultiesValueByLevel($DifficultyLevel){
        $PreConfigurationDifficultyLevel = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL,$DifficultyLevel)->first();
        return $PreConfigurationDifficultyLevel->{cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL} ?? 0;
    }

    public function curriculum_year_mapping_student_ids($gradeId='',$classId='', $schoolId='', $year=''){
        if(isset($year) && !empty($year)){
            $query = CurriculumYearStudentMappings::where([
                //cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => Self::getGlobalConfiguration('current_curriculum_year')
                cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $year
            ]);
        }else{
            $query = CurriculumYearStudentMappings::where([
                cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => Self::GetCurriculumYear()
            ]);
        }
        
        if(!empty($schoolId)){
            $query->where(cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL,$schoolId);
        }else{
            if(!Self::isAdmin()){
                $query->where(cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL,Auth::user()->school_id);
            }
        }

        if(!empty($gradeId)){
            if(is_array($gradeId)){
                $query->whereIn(cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL,$gradeId);
            }else{
                if(!Self::isAdmin()){
                    $query->where(cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL,$gradeId);
                }
            }
        }

        if(!empty($classId)){
            if(is_array($classId)){
                $query->whereIn(cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL,$classId);
            }else{
                if(!Self::isAdmin()){
                    $query->where(cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL,$classId);
                }
            }
        }
        $studentIdsArray = $query->pluck(cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL)->unique()->toArray();
        return $studentIdsArray;
    }

    /**
     * USE Set the Question Difficulty Type and Question Difficulty Value
    */
    public function setQuestionDifficultyTypeAndDifficultyValue($getRequestValue,$CalibrationId=null){
        $SelectedGlobalConfigDifficultyType  = $this->getGlobalConfiguration('difficulty_selection_type');
        $request_data = json_decode($getRequestValue,true);
        $questions = array_column($request_data,'question_id');

        foreach($questions as $key => $question){
            $questionData = Question::find($question);
            if(!empty($questionData)){
                $request_data[$key]['difficulty_type'] = $SelectedGlobalConfigDifficultyType;
                if(isset($CalibrationId) && !empty($CalibrationId)){
                    $request_data[$key]['question_difficulty_value'] = $this->GetDifficultiesValueByCalibrationId($CalibrationId,$questionData->id);
                }else{
                    $request_data[$key]['question_difficulty_value'] = $questionData->{cn::QUESTION_PRE_CONFIGURE_DIFFICULTY_VALUE} ?? 0;
                }
            }                
        }
        return  json_encode($request_data);
    }

    function findCreatedByUserType(){
        switch(Auth::user()->role_id){
            case cn::SUPERADMIN_ROLE_ID: 
                $UserType = 'super_admin';
                break;
            case cn::TEACHER_ROLE_ID:
                $UserType = 'teacher';
                break;
            case cn::STUDENT_ROLE_ID:
                $UserType = 'student';
                break;
            case cn::SCHOOL_ROLE_ID:
                $UserType = 'school_admin';
                break;
            case cn::PRINCIPAL_ROLE_ID:
                $UserType = 'principal';
                break;
            case cn::PANEL_HEAD_ROLE_ID:
                $UserType = 'panel_head';
                break;
            case cn::CO_ORDINATOR_ROLE_ID:
                $UserType = 'co_ordinator';
                break;
            default:
                $UserType = '';
                break;
        }
        return $UserType;
    }

    function GetExamStatus($request, $ExamId){
        if(!empty($ExamId)){
            switch(Auth::user()->role_id){
                case cn::SUPERADMIN_ROLE_ID:  // Case 1 is Super Admin
                    $status = 'draft';
                    break;
                
                case cn::TEACHER_ROLE_ID: // Case 2 is Teacher Panel
                    $ExamData = Exam::find($ExamId);
                    if(!empty($ExamData)){
                        if($ExamData->use_of_mode == 1){
                            if($ExamData->created_by == Auth::user()->{cn::USERS_ID_COL}){
                                $status = ($request->has('save_and_publish')) ? 'publish' : 'draft';
                            }else{
                                $status = $ExamData->status;
                            }
                        }
                    }
                    break;

                case cn::SCHOOL_ROLE_ID: 
                case cn::PRINCIPAL_ROLE_ID:
                case cn::CO_ORDINATOR_ROLE_ID:
                case cn::PANEL_HEAD_ROLE_ID: // Case 5 & 7 is School Admin & Principal
                    $ExamData = Exam::find($ExamId);
                    if(!empty($ExamData)){
                        $status = ($request->has('save_and_publish')) ? 'publish' : 'draft';
                    }                    
                    break;
            }
        }
        return $status;
    }

    protected static function checkExecutionTime($StartTime){
        return 'This page was generated in '.(number_format(microtime(true) - $StartTime, 2)).'seconds.';
    }

    protected static function array_flatten($array) { 
        if (!is_array($array)) { 
          return FALSE; 
        } 
        $result = array(); 
        foreach ($array as $key => $value) { 
            if (is_array($value)) { 
                $result = array_merge($result, Self::array_flatten($value));
            }else{
                $result[$key] = $value;
            }
        }
        return $result;
    }

    protected static function StrandUnitObjectivesMappingClone($gradeid, $subjectid){
        Log::info('Job start - StrandsUnitsMapping Start');
        $Strands = Strands::all();
        if(!empty($Strands)){
            foreach($Strands as $StrandVal){
                $LearningsUnits = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL,$StrandVal->{cn::STRANDS_ID_COL})->where('stage_id','<>',3)->get();
                if(!empty($LearningsUnits)){
                    foreach($LearningsUnits as $LearningsUnit){
                        $LearningsObjectives = LearningsObjectives::IsAvailableQuestion()->where('stage_id','<>',3)->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$LearningsUnit->{cn::LEARNING_UNITS_ID_COL})->get();
                        foreach($LearningsObjectives as $LearningsObjective){
                            StrandUnitsObjectivesMappings::updateOrCreate([
                                // cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $gradeid,
                                // cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $subjectid,
                                cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => $LearningsUnit->{cn::LEARNING_UNITS_ID_COL},
                                cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => $LearningsObjective->{cn::LEARNING_OBJECTIVES_ID_COL}
                            ],[cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                            cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => $LearningsUnit->{cn::LEARNING_UNITS_ID_COL},
                            cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => $LearningsObjective->{cn::LEARNING_OBJECTIVES_ID_COL}]);
                        }
                    }
                }
            }
        }       
        Log::info('Job End - StrandsUnitsMapping End');
    }

    /**
     * USE : Check user login type.
     * Return : User Role type
     */
    protected static function checkLoginType($type = null){
        switch ($type) {
            case "superadmin":
                $RoleType = 1;
                break;
            case "teacher":
                $RoleType = 2;
                break;
            case "student":
                $RoleType = 3;
                break;
            case "parent":
                $RoleType = 4;
                break;
            case "school":
                $RoleType = 5;
                break;
            case "external_resource":
                $RoleType = 6;
                break;
            default:
                $RoleType = 0;
        }
        return $RoleType ?? 0;
    }

    /**
     * USE : Set  Default curriculum Year
     */
    public static function SetCurriculumYear($CurriculumYearId=''){
        if(isset($CurriculumYearId) && !empty($CurriculumYearId)){
           User::find(Auth::user()->id)->Update([
                cn::USERS_CURRICULUM_YEAR_ID_COL => $CurriculumYearId
            ]);
        }else{
            if(User::where([
                cn::USERS_ID_COL => Auth::user()->id,
                cn::USERS_CURRICULUM_YEAR_ID_COL => null
            ])->exists()){
                User::find(Auth::user()->id)->Update([
                    cn::USERS_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID
                ]);
            }
        }
    }

    /**
     * USE : Get Curriculum Year
     */
    public static function GetCurriculumYear(){
        if(empty(Auth::user()->{cn::USERS_CURRICULUM_YEAR_ID_COL})){            
            Self::SetCurriculumYear();
        }        
        return Auth::user()->{cn::USERS_CURRICULUM_YEAR_ID_COL};        
    }

    protected static function GetRedirectURL(){
        if(Auth::user()){
            $redirectUrl = '';
            // Set Curriculum Year
            Self::SetCurriculumYear();
            switch (Auth::user()->roles['role_slug']) {
                case "superadmin":
                    $redirectUrl = config()->get('app.url').'admin/dashboard';
                    //$redirectUrl = config()->get('app.url').'users';
                    break;
                case "principal":
                    $redirectUrl = config()->get('app.url').'principal/dashboard';
                    //$redirectUrl = config()->get('app.url').'report/class-test-reports/correct-incorrect-answer';
                    break;
                case "panel_head" :
                    $redirectUrl = config()->get('app.url').'panel-head/dashboard';
                    //$redirectUrl = config()->get('app.url').'report/class-test-reports/correct-incorrect-answer';
                    break;
                case "co-ordinator" :
                    $redirectUrl = config()->get('app.url').'co-ordinator/dashboard';
                    //$redirectUrl = config()->get('app.url').'report/class-test-reports/correct-incorrect-answer';
                    break;
                case "teacher":
                    $redirectUrl = config()->get('app.url').'teacher/dashboard';
                    break;
                case "student":
                    $redirectUrl = config()->get('app.url').'student/dashboard';
                    //$redirectUrl = config()->get('app.url').'student/test/exam';
                    break;
                case "parent":
                    $redirectUrl = config()->get('app.url').'parent/dashboard';
                    break;
                case "school" :
                    $redirectUrl = config()->get('app.url').'schools/dashboard';
                    //$redirectUrl = config()->get('app.url').'report/class-test-reports/correct-incorrect-answer';
                    break;
                case "external_resource":
                    //$redirectUrl = config()->get('app.url').'schools/dashboard';
                    $redirectUrl = config()->get('app.url').'external_resource/dashboard';
                    break;
                default:
                $redirectUrl = '';
            }
        }
        return $redirectUrl ?? '';
    }

    protected static function isLogged(){  
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::SUPERADMIN_ROLE_ID){
            return route('superadmin.dashboard');
        } elseif(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::TEACHER_ROLE_ID){
            return route('teacher.dashboard');
        }elseif(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::STUDENT_ROLE_ID){ // For Student role
            return route('getStudentExamList');
            //return route('student.dashboard');
        }elseif(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::PARENT_ROLE_ID){
            return route('parent.dashboard');
        }elseif(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::SCHOOL_ROLE_ID){
            return route('schools.dashboard');
        }elseif(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::PANEL_HEAD_ROLE_ID){
            return route('panel_head.dashboard');
        }elseif(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::CO_ORDINATOR_ROLE_ID){
            return route('co_ordinator.dashboard');
        }elseif(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::EXTERNAL_RESOURCE_ROLE_ID){
            return route('external_resource.dashboard');
        }elseif(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::PRINCIPAL_ROLE_ID){
            return route('principal.dashboard');
        }
    }

    protected static function getSchoolByGradeIds($schoolId = null){
        $result = GradeSchoolMappings::where([
                    cn::SUBJECT_MAPPING_SCHOOL_ID_COL => $schoolId,
                    cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => Self::GetCurriculumYear()
                ])->pluck(cn::GRADES_MAPPING_GRADE_ID_COL);
        return $result;
    }

    /**
     * USE : Check current logged user is school
     * Return : true | false
     */
    protected static function isSchoolLogin(){
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::SCHOOL_ROLE_ID){
            return Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        }
        return false;
    }

    /**
     * USE : Check current logged user is Sub Admin
     * Return : true | false
     */
    protected static function isSubAdminLogin(){
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::PANEL_HEAD_ROLE_ID){
            return Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        }
        return false;
    }

    /**
     * USE : Check current logged user is Panel Head
     * Return : Current user school id
     */
    protected static function isPanelHeadLogin(){
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::PANEL_HEAD_ROLE_ID){
            return Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        }
        return false;
    }

    /**
     * USE : Check current logged user is Panel Head
     * Return : Current user school id
     */
    protected static function isCoOrdinatorLogin(){
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::CO_ORDINATOR_ROLE_ID){
            return Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        }
        return false;
    }

    /**
     * USE : Check current logged user is principal
     * Return : true | false
     */
    protected static function isPrincipalLogin(){
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::PRINCIPAL_ROLE_ID){
            return Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        }
        return false;
    }

    /**
     * USE : Check current logged user is school
     * Return : true | false
     */
    protected static function isTeacherLogin(){
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::TEACHER_ROLE_ID){
            return Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        }
        return false;
    }

    /**
     * USE : Check current logged user is student
     * Return : true | false
     */
    protected static function isStudentLogin(){
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::STUDENT_ROLE_ID){
            return Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        }
        return false;
    }

    protected static function isAdmin(){
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::SUPERADMIN_ROLE_ID){
            return true;
        }
        return false;
    }

    protected static function isSchoolStudent($studentId){
        $Student = User::where(cn::USERS_ID_COL,$studentId)->where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->first();
        if(isset($Student) && !empty($Student)){
            return true;
        }
        return false;
    }

    //Get Role Based Classes 
    public static function getClassesByRoles($examId = NULL,$SchoolID = NULL,$GradeID = NULL){
        $response = [];
        
        //Super Admin School Wise All Classes Get
        if(Auth::user()->role_id == cn::SUPERADMIN_ROLE_ID){
            if($examId){
                $schoolId = [];
                $SchoolGroups;
                $examData = Exam::find($examId);
                if(!empty($examData->school_id)){
                    $schoolId = explode(',',$examData->school_id);
                }
                $School = (!empty($SchoolID)) ? School::where('id',$SchoolID)->get() : School::whereIn(cn::SCHOOL_ID_COLS,$schoolId)->get();
                $SchoolGradeClassArray = [];
                if(!empty($School)){
                    foreach($School as $schoolKey => $SchoolValue){
                        $classArray = [];
                        $groupArray = [];

                        if(empty($GradeID)){
                            $SchoolGrades  = GradeSchoolMappings::where([
                                                cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => Self::GetCurriculumYear(),
                                                cn::GRADES_MAPPING_SCHOOL_ID_COL => $SchoolValue->id
                                            ])->pluck(cn::GRADES_MAPPING_GRADE_ID_COL);
                        }else{
                            $SchoolGrades  = GradeSchoolMappings::where([
                                                cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => Self::GetCurriculumYear(),
                                                cn::GRADES_MAPPING_SCHOOL_ID_COL => $SchoolValue->id,
                                                'grade_id' => $GradeID
                                            ])->pluck(cn::GRADES_MAPPING_GRADE_ID_COL);
                        }
                        
                        if(!empty($examData->peer_group_ids)){
                            $SchoolGroups = PeerGroup::where([
                                                cn::PEER_GROUP_SCHOOL_ID_COL => $SchoolValue->id,
                                                cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL => Self::GetCurriculumYear()
                                            ])
                                            ->whereIn(cn::PEER_GROUP_ID_COL,explode(',',$examData->peer_group_ids))
                                            ->get();
                        }
                        
                        if(isset($SchoolGroups) && !empty($SchoolGroups)){
                            foreach($SchoolGroups as $groupData){
                               $groupArray[] = array('groupId' => $groupData->id,'groupName' => $groupData->group_name);
                            }
                        }
                        if(isset($SchoolGrades) && !empty($SchoolGrades)){
                            foreach($SchoolGrades as $gradeId){
                                if(empty($examId)){
                                    $SchoolGradeClass  = GradeClassMapping::with('grade')->where([
                                                            cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => Self::GetCurriculumYear(),
                                                            cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $SchoolValue->id,
                                                            cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $gradeId
                                                        ])
                                                        ->get();
                                }else{
                                   $SchoolGradeClass =  ExamGradeClassMappingModel::with('grade','grade_class_mapping')->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,[$examId])
                                                        ->where([
                                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => self::GetCurriculumYear(),
                                                            'school_id' => $SchoolValue->id,
                                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                                        ])
                                                        ->get();
                                }
                                if(isset($SchoolGradeClass) && !empty($SchoolGradeClass)){
                                    foreach($SchoolGradeClass as $class){
                                        if(empty($class->peer_group_id)){
                                            $classArray[] = array(
                                                'classId' => (!empty($class->grade_class_mapping) && isset($class->grade_class_mapping)) ? $class->grade_class_mapping->id : $class->id,
                                                'className' => (!empty($class->grade_class_mapping) && isset($class->grade_class_mapping)) ?  $class->grade->name.''.$class->grade_class_mapping->name : $class->grade->name.''.$class->name
                                            );
                                        }
                                    }
                                    $SchoolGradeClassArray[$SchoolValue->id] = array('schoolName' => $SchoolValue->{'DecryptSchoolName'.ucfirst(app()->getLocale())},'class' => $classArray,'group' => $groupArray);
                                }
                            }   
                        }
                    }
                }
            }
            return $SchoolGradeClassArray;
        }
        if(Auth::user()->role_id == cn::TEACHER_ROLE_ID){
            $teacherAssignClasses = TeachersClassSubjectAssign::where([
                                        cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => Self::GetCurriculumYear(),
                                        cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth::user()->{cn::USERS_ID_COL},
                                        cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                                    ]);
            $AssignGrades = $teacherAssignClasses->get()->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->toArray();
            if(!empty($AssignGrades)){
                foreach($AssignGrades as $grade){
                    $TeachersClassIds = $teacherAssignClasses->where([cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL => $grade])
                                        ->get()
                                        ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)
                                        ->toArray();
                    if(isset($TeachersClassIds) && !empty($TeachersClassIds)){
                        $TeachersClassIds = explode(',',implode(',',$TeachersClassIds));
                        $gradeData = Grades::find($grade);
                        $response['grades'][] = [
                                                    'id' => $gradeData->id,
                                                    'grade_name' => $gradeData->name
                                                ];
                        $classData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,Self::GetCurriculumYear())
                                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$TeachersClassIds)
                                    ->get();
                        foreach($classData as $class){
                            $response['class'][] = [
                                'classId'    => $class->id,
                                'className'  => $gradeData->name.$class->name
                            ];
                        }
                    }
                }   
            }
            return $response;
        }
        if(Auth::user()->role_id == cn::SCHOOL_ROLE_ID || Auth::user()->role_id == cn::PRINCIPAL_ROLE_ID || Auth::user()->role_id == cn::PANEL_HEAD_ROLE_ID || Auth::user()->role_id == cn::CO_ORDINATOR_ROLE_ID){
            $classMappingData = GradeClassMapping::where([
                                    cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => Self::GetCurriculumYear(),
                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                                ])
                                ->get();
            if(!empty($classMappingData)){
                foreach($classMappingData as $classMappingKey => $classMapping){
                    $GradeData = Grades::find($classMapping->grade_id);
                    $response['class'][] = [
                        'classId'    => $classMapping->id,
                        'className'  => $GradeData->name.$classMapping->name
                    ];
                }
            }
            return $response;
        }
        return $response;
    }

    //in Class Performance Report option based set Alphabet Option
    public static function setOptionBasedAlphabet($option){
        switch($option){
            case 1:
                return '1';
                break;
            case 2:
                return '2';
                break;
            case 3:
                return '3';
                break;
            case 4: 
                return '4';
                break;
            case 5: 
                return 'N';
                break;
        }
    }

    public static function UniqueQuestionCodeGenerate($length = 16){
        $pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    public static function DateConvertToYMD($date){
        $date = DateTime::createFromFormat('d/m/Y', $date);
        return $date->format('Y-m-d');
    }

    /**
     * USE : Get current login user id
     * Return : user id
     */
    public static function LoggedUserId(){
        return Auth::user()->{cn::USERS_ID_COL};
    }

    /**
     * USE : Get current login user school id
     * Return : school id
     */
    public static function LoggedUserSchoolId(){
        return Auth::user()->{cn::USERS_SCHOOL_ID_COL};
    }

    /**
     * USE : Get current login user role id
     * Return : User role id
     */
    public static function LoggedUserRoleId(){
        return Auth::user()->{cn::USERS_ROLE_ID_COL};
    }

    /**
     * Get User current Ip
     */
    public static function getUserIP(){
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
                $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];
        if(filter_var($client, FILTER_VALIDATE_IP)){
            $ip = $client;
        }elseif(filter_var($forward, FILTER_VALIDATE_IP)){
            $ip = $forward;
        }else{
            $ip = $remote;
        }
        return $ip;
    }

    /**
     * Use : Get server data
     */
    public static function serverData(){
        $ip = Self::getUserIP();
        $ip = '182.70.126.142';
        $serverInfo = [];
        if(!empty($ip)){
            $serverInfo['IP'] = $ip ?? null;
            $serverInfo['Browser'] = Self::get_browser_name() ?? null;
            $serverInfo['DateTime'] = date('Y-m-d h:i:s');
            // Get the device details for current users
            $GetDevicePlatformDetails = Self::GetDevicePlatformDetails();
            $serverInfo['device'] = (!empty($GetDevicePlatformDetails['device'])) ? $GetDevicePlatformDetails['device'] : '';
            $serverInfo['platform'] = (!empty($GetDevicePlatformDetails['platform'])) ? $GetDevicePlatformDetails['platform'] : '';
            $ip_info = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));
            if($ip_info && $ip_info->geoplugin_countryName != null){
                $serverInfo['Country']          = $ip_info->geoplugin_countryName ?? null;
                $serverInfo['Country Code']     = $ip_info->geoplugin_countryCode ?? null;
                $serverInfo['City']             = $ip_info->geoplugin_city ?? null;
                $serverInfo['Region']           = $ip_info->geoplugin_region ?? null;
                $serverInfo['Latitude']         = $ip_info->geoplugin_latitude ?? null;
                $serverInfo['Longitude']        = $ip_info->geoplugin_longitude ?? null;
                $serverInfo['Timezone']         = $ip_info->geoplugin_timezone ?? null;
                $serverInfo['ContinentCode']    = $ip_info->geoplugin_continentCode ?? null;
                $serverInfo['ContinentName']    = $ip_info->geoplugin_continentName ?? null;
                $serverInfo['Timezone']         = $ip_info->geoplugin_timezone ?? null;
                $serverInfo['CurrencyCode']     = $ip_info->geoplugin_currencyCode ?? null;
            }
        }
        return $serverInfo;
    }

    /**
     * USE : Get the user device and platform details
     */
    public static function GetDevicePlatformDetails(){
        // Check if the "mobile" word exists in User-Agent 
        $isMobile = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile")); 
        
        // Check if the "tablet" word exists in User-Agent 
        $isTablet = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "tablet")); 
        
        // Platform check  
        $isWindow = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "windows"));
        $isMac = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mac")); 
        $isAndroid = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "android")); 
        $isIPhone = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "iphone")); 
        $isIPad = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "ipad")); 
        $isIOS = $isIPhone || $isIPad;
        $response = array(
            'device' => '',
            'platform' => ''
        );
        if($isMobile){
            if($isTablet){
                $response['device'] = 'Tablet';
            }else{ 
                $response['device'] = 'Mobile';
            }
        }else{
            $response['device'] = 'Desktop';
        }
        
        if($isIOS){ 
            $response['platform'] = 'iOS';
        }elseif($isAndroid){
            $response['platform'] = 'ANDROID';
        }elseif($isWindow){
            $response['platform'] = 'WINDOWS';
        }elseif($isMac){
            $response['platform'] = 'MacOs';
        }

        return $response;
    }

    /**
     * USE : check user access browser name
     */
    public static function get_browser_name(){
        $t = strtolower($_SERVER['HTTP_USER_AGENT']);
        $t = " " . $t;
        if     (strpos($t, 'opera'     ) || strpos($t, 'opr/')     ) return 'Opera'            ;
        elseif (strpos($t, 'edge'      )                           ) return 'Edge'             ;
        elseif (strpos($t, 'chrome'    )                           ) return 'Chrome'           ;
        elseif (strpos($t, 'safari'    )                           ) return 'Safari'           ;
        elseif (strpos($t, 'firefox'   )                           ) return 'Firefox'          ;
        elseif (strpos($t, 'msie'      ) || strpos($t, 'trident/7')) return 'Internet Explorer';
        return 'Unkown';
    }

    /**
     * USER : Save user activities for login_logout
     */
    public static function UserActivitiesLogs($type){
        if($type){
            UserActivities::create([
                cn::LOGIN_ACTIVITIES_TYPE_COL => $type,
                cn::LOGIN_ACTIVITIES_USER_ID_COL => Self::LoggedUserId(),
                cn::LOGIN_ACTIVITIES_USER_AGENT_ID_COL => json_encode(Self::serverData())
            ]);
        }
    }

    /**
     * USE : Get mapping id for "strand_units_objectives_mappings"
     */
    public static function GetStrandUnitsObjectivesMappingsId($questionCode){
        Log::info('Question Code : '. $questionCode);
        $result = [];
        $code = [];
        $result['StrandUnitsObjectivesMappingsId'] = 0;
        if(!empty($questionCode)){
            $ArrayOfQuestionCode = explode('-',$questionCode);
            if(count($ArrayOfQuestionCode) == 7 || count($ArrayOfQuestionCode) == 8){
                // Check Last Element value of array in question code
                if(count($ArrayOfQuestionCode) == 8){
                    $questionTypeArray = ['S','E','T'];
                    if(strlen($ArrayOfQuestionCode[7]) != 3 || !in_array(substr($ArrayOfQuestionCode[7],0,1),$questionTypeArray)){
                        return $result;
                    }
                }
                if(!empty($ArrayOfQuestionCode)){
                    // Get grade id
                    if(isset($ArrayOfQuestionCode[0]) && !empty($ArrayOfQuestionCode[0])){
                        $code['stage_id'] = $ArrayOfQuestionCode[0];
                        $code['grade_id'] = $ArrayOfQuestionCode[0];
                        // $Grades = Grades::where(cn::GRADES_NAME_COL,$ArrayOfQuestionCode[0])->first();
                        // if(isset($Grades)){
                        //     $code['grade_id'] = $Grades->{cn::GRADES_ID_COL};
                        // }else{
                        //     $code['grade_id'] = $ArrayOfQuestionCode[0];
                        // }
                    }

                    // Get strands id
                    if(isset($ArrayOfQuestionCode[1]) && !empty($ArrayOfQuestionCode[1])){
                        $Strands = Strands::where(cn::STRANDS_CODE_COL,$ArrayOfQuestionCode[1])->first();
                        if(isset($Strands)){
                            $code['strand_id'] = $Strands->{cn::STRANDS_ID_COL};
                        }
                    }

                    // Get learning_unit id && Learning objectives id
                    if(isset($ArrayOfQuestionCode[2]) && !empty($ArrayOfQuestionCode[2])){                        
                        //Get learning_unit
                        $LearningsUnits =   LearningsUnits::where(cn::LEARNING_UNITS_CODE_COL,substr($ArrayOfQuestionCode[2],0,2))
                                            ->where('stage_id',$ArrayOfQuestionCode[0])
                                            ->first();
                        if(isset($LearningsUnits)){
                            $code['learning_unit_id'] = $LearningsUnits->{cn::LEARNING_UNITS_ID_COL};
                        }
                        // Learning objectives id
                        $LearningsObjectives = LearningsObjectives::IsAvailableQuestion()
                                                ->where('stage_id',$ArrayOfQuestionCode[0])
                                                ->where(cn::LEARNING_OBJECTIVES_CODE_COL,substr($ArrayOfQuestionCode[2],2))
                                                ->where('learning_unit_id',$code['learning_unit_id'])
                                                ->first();
                        if(isset($LearningsObjectives)){
                            $code['learning_objectives_id'] = $LearningsObjectives->{cn::LEARNING_OBJECTIVES_ID_COL};
                        }
                    }

                    if(isset($ArrayOfQuestionCode[3]) && !empty($ArrayOfQuestionCode[3])){
                        $e = substr($ArrayOfQuestionCode[3],0,2);
                        $f = substr($ArrayOfQuestionCode[3],2,2);
                        $g = substr($ArrayOfQuestionCode[3],-1);
                    }
                    
                    $difficulty_level = null;
                    if(isset($ArrayOfQuestionCode[5]) && !empty($ArrayOfQuestionCode[5])){
                        $difficulty_level = $ArrayOfQuestionCode[5];
                    }

                    $question_type = null;
                    if(isset($ArrayOfQuestionCode[7]) && !empty($ArrayOfQuestionCode[7])){
                        switch (substr($ArrayOfQuestionCode[7],0,1)) {
                            case 'S':
                                $question_type = 1;  // 1 = Self Learning Question code
                                break;
                            case 'E':
                                $question_type = 2;  // 1 = Excercise Question code
                                break;
                            case 'T':
                                $question_type = 3;  // 1 = Test Question code
                                break;
                        }
                    }else{
                        $question_type = 4; // 4 = Main Seed Question code
                    }
                }

                $Subjects = Subjects::where(cn::SUBJECTS_CODE_COL,'MA')->first();
                $code['subject_id'] = $Subjects->{cn::SUBJECTS_ID_COL};
                if(array_key_exists('grade_id',$code) && 
                    array_key_exists('stage_id',$code) &&
                    array_key_exists('strand_id',$code) &&
                    array_key_exists('learning_unit_id',$code) &&
                    array_key_exists('learning_objectives_id',$code)){
                        // Check mapping id is exists or not
                        if(StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$code['strand_id'])
                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$code['learning_unit_id'])
                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$code['learning_objectives_id'])
                        ->where(cn::OBJECTIVES_MAPPINGS_STAGE_ID_COL,$code['stage_id'])
                        ->doesntExist()){
                            // Create new mapping
                            StrandUnitsObjectivesMappings::Create([
                                cn::OBJECTIVES_MAPPINGS_STAGE_ID_COL                => $code['stage_id'],
                                cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL               => $code['strand_id'],
                                cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL        => $code['learning_unit_id'],
                                cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL  => $code['learning_objectives_id']
                            ]);
                        }
                        $StrandUnitsObjectivesMappings =    StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$code['strand_id'])
                                                            ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$code['learning_unit_id'])
                                                            ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$code['learning_objectives_id'])
                                                            ->where(cn::OBJECTIVES_MAPPINGS_STAGE_ID_COL,$code['stage_id'])
                                                            ->first();
                        $result = [
                            'StrandUnitsObjectivesMappingsId'   => $StrandUnitsObjectivesMappings->{cn::OBJECTIVES_MAPPINGS_ID_COL},
                            'stage_id'                          => $code['stage_id'],
                            'grade_id'                          => $code['grade_id'],
                            'subject_id'                        => $Subjects->id,
                            'strand_id'                         => $code['strand_id'],
                            'learning_unit_id'                  => $code['learning_unit_id'],
                            'learning_objectives_id'            => $code['learning_objectives_id'],
                            'question_type'                     => $question_type,
                            'dificulaty_level'                  => $difficulty_level,
                            'e'                                 => $e,
                            'f'                                 => $f,
                            'g'                                 => $g
                        ];
                }
            }
        }
        return $result;
    }

    /**
     * USE : Get Node id by Node
     */
    public static function getNodeById($id){
        if(!empty($id)){
            $NodeData = Nodes::find($id);
            if(isset($NodeData) && !empty($NodeData)){
                $Node = $NodeData->node_id ?? null;
            }
        }
        return $Node ?? null;
    }

    /**
     * USE : Get StrandUnitsObjectivesMappingsIdByNodes using nodes
     */
    public static function StrandUnitsObjectivesMappingsIdByNodes($node_id=0){
        $code = [];
        $result = [];
        $StrandUnitsObjectivesMappingsId = null;
        if(isset($node_id) && $node_id!=0){
            $Node = self::getNodeById($node_id);
            if(isset($Node) && !empty($Node)){
                $ExplodeNode = explode('-',$Node);
                if($ExplodeNode){
                    // Get grade id
                    if(isset($ExplodeNode[0]) && !empty($ExplodeNode[0])){
                        $Grades = Grades::where(cn::GRADES_NAME_COL,$ExplodeNode[0])->first();
                        if(isset($Grades)){
                            $code['grade_id'] = $Grades->{cn::GRADES_ID_COL};
                        }
                    }

                    // Get strands id
                    if(isset($ExplodeNode[1]) && !empty($ExplodeNode[1])){
                        $Strands = Strands::where(cn::STRANDS_CODE_COL,$ExplodeNode[1])->first();
                        if(isset($Strands)){
                            $code['strand_id'] = $Strands->{cn::STRANDS_ID_COL};
                        }
                    }

                    // Get learning_unit id && Learning objectives id
                    if(isset($ExplodeNode[2]) && !empty($ExplodeNode[2])){
                        //Get learning_unit
                        $LearningsUnits = LearningsUnits::where(cn::LEARNING_UNITS_CODE_COL,substr($ExplodeNode[2],0,2))->where('stage_id','<>',3)->first();
                        if(isset($LearningsUnits)){
                            $code['learning_unit_id'] = $LearningsUnits->{cn::LEARNING_UNITS_ID_COL};
                        }
                        // Learning objectives id
                        $LearningsObjectives = LearningsObjectives::IsAvailableQuestion()->where('stage_id','<>',3)->where(cn::LEARNING_OBJECTIVES_CODE_COL,substr($ExplodeNode[2],2))->where('learning_unit_id',$code['learning_unit_id'])->first();
                        if(isset($LearningsObjectives)){
                            $code['learning_objectives_id'] = $LearningsObjectives->{cn::LEARNING_OBJECTIVES_ID_COL};
                        }
                    }

                    $Subjects = Subjects::where(cn::SUBJECTS_CODE_COL,'MA')->first();
                    $code['subject_id'] = $Subjects->id;

                    // Get mapping id based on selected options
                    if(array_key_exists('grade_id',$code) && array_key_exists('strand_id',$code) && array_key_exists('learning_unit_id',$code) && array_key_exists('learning_objectives_id',$code)){
                        $StrandUnitsObjectivesMappings = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$code['strand_id'])
                                                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$code['learning_unit_id'])
                                                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$code['learning_objectives_id'])
                                                        ->first();
                        if(isset($StrandUnitsObjectivesMappings) && !empty($StrandUnitsObjectivesMappings)){                            
                            $result = [
                                'StrandUnitsObjectivesMappingsId' => $StrandUnitsObjectivesMappings->id,
                                'grade_id' => $code['grade_id'],
                                'subject_id' => $Subjects->id,
                                'strand_id' => $code['strand_id'],
                                'learning_unit_id' => $code['learning_unit_id'],
                                'learning_objectives_id' => $code['learning_objectives_id']
                            ];
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * USE : Find student ranking
     */
    public static function getStudentExamRanking($examId, $studentId){
        $school_id = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $rank = 1;
        $firstValue = 0;
        $studentRank = [];
        if(!empty($school_id)){
            $AttemptData = AttemptExams::with('user')->where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)
                            ->whereHas('user',function ($q) use($school_id,$studentId){
                                $q->whereIn(cn::USERS_ID_COL,$studentId)
                                ->where([
                                    cn::USERS_SCHOOL_ID_COL => $school_id,
                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                                ]);
                            })
                            ->selectRaw('student_id,SUM(total_correct_answers) as Total')
                            ->groupBy(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID)
                            ->orderBy('Total','DESC')
                            ->get();
        }else{
            $AttemptData = AttemptExams::with('user')->where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)
                            ->selectRaw('student_id,SUM(total_correct_answers) as Total')
                            ->groupBy(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID)
                            ->orderBy('Total','DESC')
                            ->get();
        }
        if(!empty($AttemptData)){
            foreach($AttemptData as $data){
                if($firstValue == 0){
                    $firstValue = $AttemptData[0]->Total;
                    $studentRank[$data->student_id] = $rank;
                }elseif($firstValue != $data->Total){
                    $firstValue = $data->Total;
                    $rank += 1;
                    $studentRank[$data->student_id] = $rank;
                }elseif($data->Total ==  $firstValue){
                   $studentRank[$data->student_id] = $rank;
                }
            }
           return $studentRank;
        }
    }

    public static function getGroupTestStudentExamRanking($examId, $studentId = 0){
        $NoOfRank = 0;
        $result= DB::select('SELECT id, student_id, FIND_IN_SET( `total_correct_answers`, (
            SELECT GROUP_CONCAT( `total_correct_answers`
            ORDER BY `total_correct_answers` DESC )
            FROM attempt_exams where exam_id IN('.implode(',',$examId).'))
            ) AS studentRanking
            FROM attempt_exams where exam_id IN('.implode(',',$examId).')');
        if($result){
            if(!empty($studentId)){
                $FilterArray = array_filter($result, function ($array) use($studentId){
                    if($array->student_id == $studentId){
                        return $array ?? [];
                    }
                });
                $NoOfRank = (array_column($FilterArray,'studentRanking')[0]);
            }
            return $NoOfRank;
        }else{
            return $NoOfRank;
        }
    }

    public static function getQuestionCode($code, $count=0){
        $questionCode = '';
        $questionCode .= $code['grade'].'-'.$code['subject'].'-'.$code['strand'].'-';
        if(strlen($code['LearningUnit_id'] == 1)){
            $questionCode .= '0'.$code['LearningUnit_id'];
        }else{
            $questionCode .= $code['LearningUnit_id'];
        }
        if(strlen($code['LearningObjective_id']) == 1){
            $questionCode .= '0'.$code['LearningObjective_id'].'-';
        }else{
            $questionCode .= $code['LearningObjective_id'].'-';
        }
        if(strlen($code['LearningUnit_id'] == 1)){
            $questionCode .= '0'.$code['LearningUnit_id'];
        }else{
            $questionCode .= $code['LearningUnit_id'];
        }
        $questionCode .= '00F'.'-'.'00'.$count.'-1-'.'00';

        return $questionCode;
    }

    /**
     * USE : Add Subject in to question code
     */
    public static function concateQuestionCode($questionCode){
        $questionCodeArray = explode('-',$questionCode);
        $questionCode = '';
        foreach($questionCodeArray as $key => $code){
            if(count($questionCodeArray) == 7 && $key == 1){
                $questionCode .= '-MA';
            }
            if($key == 0){
                $questionCode .= $code;
            }else{
                $questionCode .= '-'.$code;
            }
        }
        return $questionCode;
    }

    /**
     * USE : Store & Get Filter data of array
     */
    public static function saveAndGetFilterData($filterType, $request){
        switch ($filterType) {
            case 'QuestionListFilter':
                    if(isset($request->filter)){
                        Session::put('QuestionListFilter', $request->all());
                    }else{
                        $request->merge(Session::get('QuestionListFilter'));
                    }
              break;
            default:
        }
        return $request;
    }

  public static function  TotalDurationCalculation($examTimes){
    $seconds = 0;
      if(!empty($examTimes)){
          foreach($examTimes as $time){
            $parts = explode(':',$time );
            $seconds += ($parts[0] * 60 * 60) + ($parts[1] * 60) + $parts[2];
          }
      }
    return gmdate("H:i:s", $seconds);
  }
  
    /**
        * USE : Create 6 digits password for users
    */
    public static function setPassword($password){
        $newPassword = '';
        if(!empty($password) && strlen($password) < 6) {
            $length = (6- strlen($password));
            for($i=0; $i < $length; $i++){
                $newPassword = $newPassword . 0;
            }
            $password = $newPassword.$password;
        }else if(empty($password)){
            $password = '000000';
        }
        return $password;
    }

    /**
     * Get the Grade Name 
     */
    public static function getGradeName($id){
        $grade = Grades::where(cn::GRADES_ID_COL,$id)->first();
        return $grade->{cn::GRADES_NAME_COL};
    }

    /****
     *  Get the subject Name
     */
    public static function getSubjectName($id){
        $subject = Subjects::where(cn::SUBJECTS_ID_COL,$id)->first();
        return $subject->{cn::SUBJECTS_NAME_COL};
    }

    /****
     * get Strand name
     */
    public static function getStrandName($id){
        $strand = Strands::where(cn::STRANDS_ID_COL,$id)->first();
        return $strand->{cn::STRANDS_NAME_COL};
    }

    /****
     * get Learning Unit Name
     */
    public static function getLearningUnitName($id){
        $LearningUnit = LearningsUnits::where(cn::LEARNING_UNITS_ID_COL,$id)->where('stage_id','<>',3)->first();
        return $LearningUnit->{cn::LEARNING_UNITS_NAME_COL};
    }

    /****
     * get Learning Objective Name
     */
    public static function getLearningObjectiveName($id){
       $learning_objective = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_ID_COL,$id)->where('stage_id','<>',3)->first();
       return $learning_objective->{cn::LEARNING_OBJECTIVES_TITLE_COL};
    }
   
    /**
     * Sorting multi dimensional array 
     */
    function make_comparer() {
        // Normalize criteria up front so that the comparer finds everything tidy
        $criteria = func_get_args();
        foreach ($criteria as $index => $criterion) {
            $criteria[$index] = is_array($criterion)
                ? array_pad($criterion, 3, null)
                : array($criterion, SORT_ASC, null);
        }
    
        return function($first, $second) use (&$criteria) {
            foreach ($criteria as $criterion) {
                // How will we compare this round?
                list($column, $sortOrder, $projection) = $criterion;
                $sortOrder = $sortOrder === SORT_DESC ? -1 : 1;
    
                // If a projection was defined project the values now
                if ($projection) {
                    $lhs = call_user_func($projection, $first[$column]);
                    $rhs = call_user_func($projection, $second[$column]);
                }else {
                    $lhs = $first[$column];
                    $rhs = $second[$column];
                }
    
                // Do the actual comparison; do not return if equal
                if ($lhs < $rhs) {
                    return -1 * $sortOrder;
                }else if ($lhs > $rhs) {
                    return 1 * $sortOrder;
                }
            }
            return 0; // tiebreakers exhausted, so $first == $second
        };
    }

    // this is used for a mapping strand_units_objectives_mappings table dynamically.
    public function SubjectMapping($grade,$subject){
        $StrandList = Strands::all();
        if($StrandList->isNotEmpty()){
            foreach($StrandList as $strand){
                $LearningUnits = LearningsUnits::where('stage_id','<>',3)->get();
                if($LearningUnits->isNotEmpty()){
                    foreach($LearningUnits as $learningUnit){
                        $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->where('stage_id','<>',3)->get();
                        if($LearningObjectives->isNotEmpty()){
                            foreach($LearningObjectives as $learningObjectives){
                                $postData = array(
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $grade->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $subject->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $strand->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL =>$learningUnit->{cn::LEARNING_UNITS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => $learningObjectives->{cn::LEARNING_OBJECTIVES_ID_COL}
                                );
                                StrandUnitsObjectivesMappings::create($postData);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * USE : Encryption Data
     */
    public static function encrypt($string){
        return base64_encode($string);
    }

    /**
     * USE : Decrypt Data
     */
    public static function decrypt($string){
        return base64_decode($string);
    }

    /**
     * USE : This is user to create user activities log
     */
    public static function StoreAuditLogFunction($value, $table_model, $columnName = '', $id=0, $log_name, $table_name, $child_table_name){
        $role_type = Auth::user()->roles['role_slug'];
        $ip_address = Self::getUserIP();
        $page_name = url()->full();
        $compare_arr = $value;
        if(!empty($child_table_name) && is_array($child_table_name)){
            $child_table_name = implode(',',$child_table_name);
        }
        if($id != 0){
            $compare_arr = array();
            $model_name = '\App\Models\\'.$table_model;
            $existingValue = [];
            if(isset($columnName) && !empty($columnName)){
                $existingValue = $model_name::where($columnName, $id)->first();
            }
            $existingValue = $existingValue ? $existingValue->toArray() : array();
            $postValue = $value;
            foreach ($existingValue as $key1 => $value1) {
                foreach ($postValue as $key2 => $value2) {
                    if ($key1 === $key2) {
                        switch (true) {
                        case ($value1 != $value2):
                            $compare_arr[] = array(
                                'old_value_' . $key1 => $value1,
                                'new_value_' . $key2 => $value2,
                            );
                            default:
                            break;
                        }
                    }
                }
            }
            if(empty($compare_arr)){
                $compare_arr = '';
            }
        }
        if(!empty($compare_arr)){
            $compare_arr = json_encode($compare_arr);
        }
        
        $audit_log_data = AuditLogs::create([
            cn::AUDIT_LOGS_ROLE_TYPE_COL => $role_type,
            cn::AUDIT_LOGS_USER_ID_COL => Self::LoggedUserId(),
            cn::AUDIT_LOGS_NAME_COL => $log_name,
            cn::AUDIT_LOGS_PAYLOAD_COL => $compare_arr,
            cn::AUDIT_LOGS_TABLE_NAME_COL => $table_name,
            cn::AUDIT_LOGS_CHILD_TABLE_NAME_COL => $child_table_name,
            cn::AUDIT_LOGS_PAGE_NAME_COL => $page_name,
            cn::AUDIT_LOGS_IP_ADDRESS_COL => $ip_address,
        ]);
    }

    /***
     * USE : GET PEER GROUP TYPE
     */
    public function getPeerGroupType(){
        return array(
            ['id' => '0', 'name' => __('languages.similar').' '.__('languages.distribution')],
            ['id' => '1', 'name' => __('languages.ability_based')]
        );
    }

    /**
     * USE : Get Status List options
     */
    public function getStatusOptions(){
        return array(
            ['id' => '1',"name" => 'Active'],
            ['id' => '0',"name" => 'Inactive']
        );
    }

    /***
     * USE : Get Status List On id = (active,inactive)
     ***/
    public function getStatusList(){
        return array(
            ['id' => 'active',"name" => 'Active'],
            ['id' => 'inactive',"name" => 'Inactive']
        );
    }

    /**
     * USE : Get Template Types
     */
    public function getTemplateTypes(){
        return [
            ['id' => '1', "name" => 'Self-Learning'],
            ['id' => '2', "name" => 'Exercise'],
            ['id' => '3', "name" => 'Testing']
        ];
    }

    /**
     * USE : Get Question Types
     */
    public function getQuestionTypes(){
        return  array(
            ['id' => '0', "name" => 'All'],
            ['id' => '1', "name" => 'Self-Learning'],
            ['id' => '2', "name" => 'Exercise/Assignment'],
            ['id' => '3', "name" => 'Testing'],
            ['id' => "4", "name" => 'Seed']
        );
    }

    /**
     * USE : Get Difficulty Levels
     */
    public function getDifficultyLevels(){
        return array(
            ['id' =>  1,"name" => '1 - Easy'],
            ['id' =>  2,"name" => '2 - Medium'],
            ['id' =>  3,"name" => '3 - Difficult'],
            ['id' =>  4,"name" => '4 - Tough']
        );
    }
    
    /**
     * USE : Get new Difficulty Levels
     */
    public function getDifficultyLevel(){
        return array(
            ['id' =>  1,"name" => 'Level 1'],
            ['id' =>  2,"name" => 'Level 2'],
            ['id' =>  3,"name" => 'Level 3'],
            ['id' =>  4,"name" => 'Level 4'],
            ['id' =>  5,"name" => 'Level 5']
        );
    }

    /**
     * USE : Get Document Types
     */
    public function getListOfDocumentType(){
        return array(
            ['id' => 'video', "name" => 'Video'],
            ['id' => 'pdf', "name" => 'PDF'],
            ['id' => 'ppt', "name" => 'PPT'],
            ['id' => 'excel', "name" => 'Excel'],
            ['id' => 'txt', "name" => 'TXT'],
            ['id' => 'audio', "name" => 'Audio'],
            ['id' => 'image', "name" => 'Image'],
            ['id' => 'doc', "name" => 'Doc'],
        );
    }

    /**
     * USE : Get Exam Types options
     */
    public function ExamTypeList(){
        return array(
            // ['id'=> 1, 'name' => 'Self-Learning'],
            ['id'=> 2, 'name' => 'Exercise'],
            ['id'=> 3, 'name' => 'Test']
        );
    }

    /**
     * USE : Get Exam Status List
     */
    public function ExamStatusList(){
        return array(
            // ['id'=>'pending', 'name' => 'Pending'],
            ['id'=>'draft', 'name' => __('languages.draft')],
            ['id'=>'publish', 'name' => __('languages.publish')],
            // ['id'=>'active', 'name' => 'Active'],
            ['id'=>'inactive', 'name' => __('languages.inactive')],
            // ['id'=>'inactive', 'name' => __('languages.deactivate')],
            // ['id'=>'complete', 'name' => 'Complete']
        );
    }

    // this is used for a mapping strand_units_objectives_mappings table dynamically.
    public function getSubjectMapping($strands_id=array(),$learning_units_id=array(),$learning_objectives_id=array()){
        $html = '';
        $html .= '<ul class="categories-list">';
        $strandsIds = StrandUnitsObjectivesMappings::pluck(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL);
        if(!empty($strandsIds)){
            $strandsIds = array_unique($strandsIds->toArray());
            $StrandList = Strands::whereIn(cn::STRANDS_ID_COL, $strandsIds)->get();
            if($StrandList->isNotEmpty()){
                foreach($StrandList as $strand){
                    $selected = "";
                    if(isset($strands_id) && !empty($strands_id)){
                        if(in_array($strand->id,$strands_id)){
                            $selected='checked="checked"';
                        }
                    }
                    $html .= '<li class="category"><input type="checkbox" name="strands[]" value="'.$strand->{cn::STRANDS_ID_COL}.'" '.$selected.' ><div class="row"><div class="col-md-8"><label>'.$strand->{cn::STRANDS_NAME_COL}.'</label></div><div class="col-md-4"><input type="range" id="" min="0" value="0" disabled="" max="100" class="up-50" style=""><span class="label-percentage">0%</span></div></div>';
                    $learningUnitsIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strand->{cn::STRANDS_ID_COL})
                                        ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL);
                    if(!empty($learningUnitsIds)){
                        $learningUnitsIds = array_unique($learningUnitsIds->toArray());
                        $LearningUnits = LearningsUnits::whereIn(cn::LEARNING_UNITS_ID_COL, $learningUnitsIds)->where('stage_id','<>',3)->get();
                        if($LearningUnits->isNotEmpty()){
                            $html .= '<a class="collapse-category close"></a>';
                            $html .= '<ul class="subcategories">';
                            foreach($LearningUnits as $learningUnit){
                                $selectedLearningUnit="";
                                if(isset($learning_units_id) && $learning_units_id!=0){
                                    if(in_array($learningUnit->id,$learning_units_id)){
                                        $selectedLearningUnit='checked="checked"';
                                    }
                                }
                                $html .= '<li class="category"><input type="checkbox" name="learning_units[]" value="'.$learningUnit->{cn::LEARNING_UNITS_ID_COL}.'" '.$selectedLearningUnit.'><div class="row"><div class="col-md-8"><label>'.$learningUnit->{cn::LEARNING_UNITS_NAME_COL}.'</label></div><div class="col-md-4"><input type="range" id="" min="0" value="0" disabled="" max="100" class="up-50" style=""><span class="label-percentage">0%</span></div></div>';
                                $learningObjectivesIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strand->{cn::STRANDS_ID_COL})
                                                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$learningUnit->{cn::LEARNING_UNITS_ID_COL})
                                                        ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL);
                                if(!empty($learningObjectivesIds)){
                                    $learningObjectivesIds = array_unique($learningObjectivesIds->toArray());
                                    $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->where('stage_id','<>',3)->whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                                    if($LearningObjectives->isNotEmpty()){
                                        $html .= '<a class="collapse-category close"></a>';
                                        $html .= '<ul class="subcategories">';
                                        foreach($LearningObjectives as $learningObjective){
                                            $selectedLearningObjectives="";
                                            if(isset($learning_objectives_id) && $learning_objectives_id != 0){
                                                if(in_array($learningObjective->id,$learning_objectives_id)){
                                                    $selectedLearningObjectives = 'checked="checked"';
                                                }
                                            }
                                            $html .= '<li class="category"><input type="checkbox"  name="learning_objectives_id[]" value="'.$learningObjective->{cn::LEARNING_OBJECTIVES_ID_COL}.'" '.$selectedLearningObjectives.'><div class="row"><div class="col-md-8"><label>'.$learningObjective->{cn::LEARNING_OBJECTIVES_STUDY_FOCI_COL}.' '.$learningObjective->{cn::LEARNING_OBJECTIVES_TITLE_COL}.'</label></div><div class="col-md-4"><input type="range" id="" min="0" value="0" disabled="" max="100" class="up-50" style=""><span class="label-percentage">0%</span></div></div></li>';
                                        }
                                        $html .= '</ul></li>';
                                    }
                                }
                            }
                            $html .= '</ul>';
                        }
                    }
                    $html .= '</li>';
                }
            }
        }
        $html .= '</ul>';

        return $html;
    }

    public function getSingleClassName($id){
        $className = '';
        $ClassData = GradeClassMapping::where([
                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => Self::GetCurriculumYear(),
                        cn::GRADE_CLASS_MAPPING_ID_COL => $id
                    ])->first();
        if(!empty($ClassData)){
            $className = strtoupper($ClassData->{cn::GRADE_CLASS_MAPPING_NAME_COL});
        }
        return $className;
    }   

    /**
     * USE : Get Document type using type
     */
    public function getDocumentType($type){
        if($type == 'video'){
            $typeList = array('mp4','3gp','avi','vob','flv','webm','wmv','ogg','mpeg','mov','url');
        }else if($type == 'url'){
            $typeList = array('url');
        }else if($type == 'pdf'){
            $typeList = array('pdf');
        }else if($type == 'ppt'){
            $typeList = array('ppt','pptx');
        }else if($type == 'excel'){
            $typeList = array('xlsx','xls','csv');
        }else if($type == 'txt'){
            $typeList = array('txt');
        }else if($type == 'audio'){
            $typeList = array('mp3');
        }else if($type == 'image'){
            $typeList = array('jpg','jpeg','png','gif','svg');
        }else{
            $typeList = array('doc','docx');
        }
        return $typeList;
    }

    /**
     * USE : URL thumbnail generator
     */
    public function urlThumbnailGenerator($url){
        if(strpos($url, 'youtube') > 0) {
            $fetch = explode("v=", $url);
            $videoId = $fetch[1];
            return 'http://img.youtube.com/vi/'.$videoId.'/mqdefault.jpg';
        }elseif(strpos($url, 'vimeo') > 0){
            preg_match('#(?:https?://)?(?:www.)?(?:player.)?vimeo.com/(?:[a-z]*/)*([0-9]{6,11})[?]?.*#', $url, $m);
            if(isset($m[1])){
                $arr_vimeo = unserialize(file_get_contents("https://vimeo.com/api/v2/video/".$m[1].".php"));
                return $arr_vimeo[0]['thumbnail_medium'];
            }
            return '';
        }elseif(strpos($url, 'dailymotion') > 0){
            preg_match_all('/^.+dailymotion.com\/(?:video|swf\/video|embed\/video|hub|swf)\/([^&?]+)/',$url,$m);
            if (isset($m[1][0])) {
                $thumbnail_large_url = 'https://api.dailymotion.com/video/'.$m[1][0].'?fields=thumbnail_360_url';
                $json_thumbnail = file_get_contents($thumbnail_large_url);
                $arrayDailyMotion = json_decode($json_thumbnail, TRUE);
                return $arrayDailyMotion['thumbnail_360_url'];
            }
            return '';
        }else{
            return '';
        }
    }

    /**
     * USE : Insert mapping data after creation new learning objectives
     */
    public function insertStrandsUnitsObjectivesMappingRecord(Request $request, $LearningObjectivesId = null){
        if(!empty($LearningObjectivesId)){
            $Grades = Grades::all();
            if(isset($Grades) && !empty($Grades)){
                foreach($Grades as $grade){
                    $Subjects = Subjects::all();
                    if(isset($Subjects) && !empty($Subjects)){
                        foreach($Subjects as $subject){
                            $LearningsUnits = LearningsUnits::find($request->learning_unit_id);
                            StrandUnitsObjectivesMappings::updateOrCreate([
                                cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL                => $grade->{cn::GRADES_ID_COL},
                                cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL              => $subject->{cn::SUBJECTS_ID_COL},
                                cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL               => $LearningsUnits->{cn::LEARNING_UNITS_STRANDID_COL},
                                cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL        => $request->learning_unit_id,
                                cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL  => $LearningObjectivesId
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * AI Function Implement "analyze_student"
     */
    public function analyze_student($examData){
        $result = [];
        $normalized_student_ability = 0;
        $student_ability = $examData['student_ability'];
        $student_results_list = $examData['questions_results'];
        $questions_difficulties_list = $examData['difficulty_list'];

        $student_results = $student_results_list;
        $questions_difficulties = $questions_difficulties_list;

        $correct_questions = [];
        $incorrect_questions = [];
        
        foreach($student_results as $key =>  $result){
            if(!empty($result)){
                $correct_questions[$key][] =  $questions_difficulties[$key];
            }else{
                $incorrect_questions[$key][] =  $questions_difficulties[$key];
            }
        }

        if(!empty($correct_questions)){
            foreach($correct_questions as $correctQuestion){
                $correct_questions_difficulties[] = $correctQuestion[0];
            }
        }

        if(!empty($incorrect_questions)){
            foreach($incorrect_questions as $incorrectQuestion){
                $incorrect_questions_difficulties[] = $incorrectQuestion[0];
            }
        }

        if(!empty($correct_questions_difficulties) && !empty($incorrect_questions_difficulties)){
            $incorrect_num = count($incorrect_questions_difficulties);
            $correct_num = count($correct_questions_difficulties);
            $totalQuestionNumber = ($incorrect_num + $correct_num);
            $incorrect_percent = ($incorrect_num * 100) / $totalQuestionNumber;
            $correct_percent = ($correct_num * 100) / $totalQuestionNumber;
            $student_ability = (exp($student_ability) / (1 + exp($student_ability))*100);
            $normalized_student_ability = number_format((float)$student_ability, 2, '.', '');
            $result = [
                'student_ability' => $student_ability,
                'normalized_student_ability' => $normalized_student_ability,
                'incorrect_num' => $incorrect_num,
                'correct_num' => $correct_num,
                'incorrect_percent' => $incorrect_percent,
                'correct_percent' => $correct_percent
            ];
        }
        return $result;
    }

    public function CheckNumber($x) {
        if ($x > 0){
            $message = "Positive number";
        }elseif ($x == 0){
            $message = "Zero";
        }else{
            $message = "Negative number";
        }
        return $message;
    }

    /**
     * USE : Get Global Configuration value using key name
     */
    public static function getGlobalConfiguration($key){
        $configuration = GlobalConfiguration::where(cn::GLOBAL_CONFIGURATION_KEY_COL,$key)->first();
        if(isset($configuration) && !empty($configuration)){
            return $configuration->{cn::GLOBAL_CONFIGURATION_VALUE_COL} ?? null;
        }
        return null;
    }
    /**
     * USE : Get Global Configuration value using key name
     */
    public function createTestTitle(){
        $TestTitle = '';
        $schoolYear = '';
        $currentDate = new DateTime();
        $StartYearDate = date('Y').'-09-01';
        $CompleteYearDate = date('Y', strtotime('+1 years')).'-08-31';
        $StartYearDate = new DateTime($StartYearDate);
        $CompleteYearDate  = new DateTime($CompleteYearDate);
        if( $currentDate->getTimestamp() > $StartYearDate->getTimestamp() && $currentDate->getTimestamp() < $CompleteYearDate->getTimestamp()){
            $schoolYear = date('y', strtotime('-1 year')).'-'.date('y');
        }elseif($currentDate->getTimestamp() < $StartYearDate->getTimestamp()){
            $schoolYear = date('y', strtotime('-1 year')).'-'.date('y');
        }else{
            $schoolYear = date('y').'-'.date('y', strtotime('+1 year'));
        }

        // Get Grade Name
        //$gradeName = $this->getGradeName(Auth::user()->grade_id);
        $gradeName = $this->getGradeName(Auth::user()->CurriculumYearGradeId);
        // Get class Name
        //$className = $this->getSingleClassName(Auth::user()->class_id);
        $className = $this->getSingleClassName(Auth::user()->CurriculumYearClassId);
        //get Student Number 
        //$studentNumber = Auth::user()->student_number;
        $studentNumber = Auth::user()->CurriculumYearData['student_number'];
        // get Time Stamp
        $timestamps = $currentDate->getTimestamp();
        //Dynamic Title
        $TestTitle = $schoolYear.'+'.$gradeName.$className.'+'.$studentNumber.'+'.$timestamps;
        return $TestTitle;
    }

    //Convert time to Second Function
    function timeToMinute(string $time): int{
        $arr = explode(':', $time);
        if (count($arr) === 3) {
            return ($arr[0] * 3600 + $arr[1] * 60 + $arr[2]) / 60;
        }
        return (($arr[0] * 60 + $arr[1])/60);
    }

    function secondToTime($seconds){
        if($seconds){
            return sprintf('%02d:%02d:%02d', ($seconds/ 3600),($seconds/ 60 % 60), $seconds% 60);
        }
    }

    //Convert time to Second Function
    function timeToSecond(string $time): int{
        $arr = explode(':', $time);
        if (count($arr) === 3) {
            $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $time);
            sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
            $time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
            return $time_seconds;
        }
        if (count($arr) === 2) {
            $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $time);
            sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
            $time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
            return $time_seconds;
        }
        return 0;
    }

    /**
     * USE : Get question id based number of answer count
     */
    function GetQuestionNumOfAnswerAndDifficultyValue($questionId,$CalibrationId=null){
        $response = [];
        $SelectedGlobalConfigDifficultyType  = $this->getGlobalConfiguration('difficulty_selection_type');
        //$QuestionAnswer = Question::with('answers')->with('PreConfigurationDifficultyLevel')->find($questionId);
        $QuestionAnswer = Question::with('answers')->find($questionId);
        if(isset($QuestionAnswer) && !empty($QuestionAnswer)){
            // if($SelectedGlobalConfigDifficultyType == 1){ // 1 = Pre-Configured Question Difficulties
            //     $response['difficulty_value'] = $QuestionAnswer->PreConfigurationDifficultyLevel->title;
            // }
            //if($SelectedGlobalConfigDifficultyType == 2){ // 2 = AI-Calculated Question Difficulties
                if(isset($CalibrationId) && !empty($CalibrationId)){
                    $response['difficulty_value'] = $this->GetDifficultiesValueByCalibrationId($CalibrationId,$questionId);
                }else{
                    $response['difficulty_value'] = $QuestionAnswer->PreConfigurationDifficultyLevel->title;
                }
            //}
            //$response['difficulty_value'] = $QuestionAnswer->PreConfigurationDifficultyLevel->title;

            if(!empty($QuestionAnswer->answers->answer1_en) && !empty($QuestionAnswer->answers->answer2_en) && !empty($QuestionAnswer->answers->answer3_en) && !empty($QuestionAnswer->answers->answer4_en)){
                $response['noOfAnswers'] = 4;
            }else if(!empty($QuestionAnswer->answers->answer1_en) && !empty($QuestionAnswer->answers->answer2_en) && !empty($QuestionAnswer->answers->answer3_en)){
                $response['noOfAnswers'] = 3;
            }else if(!empty($QuestionAnswer->answers->answer1_en) && !empty($QuestionAnswer->answers->answer2_en)){
                $response['noOfAnswers'] = 2;
            }else if(!empty($QuestionAnswer->answers->answer1_en)){
                $response['noOfAnswers'] = 1;
            }else{
                $response['noOfAnswers'] = 0;
            }
        }
        return $response;
    }

    /**
     * USE : GetPercentageQuestionDifficultyLevel
     */
    function GetPercentageQuestionDifficultyLevel($data){
        $response = [];
        $largestQuestionDifficulty = [];
        if(!empty($data)){
            for($i=1; $i <= 5; $i++){
                ${'correctLevelQuesion'.$i} = $data['correct_Level'.$i];
                ${'wrongLevelQuesion'.$i} = ($data['Level'.$i] - $data['correct_Level'.$i]);
                $largestQuestionDifficulty[] = (${'correctLevelQuesion'.$i} +${'wrongLevelQuesion'.$i});
                $response['correct_Level'.$i] =  ${'correctLevelQuesion'.$i};
                $response['wrong_Level'.$i] = ${'wrongLevelQuesion'.$i};
            }
            
            if(!empty($largestQuestionDifficulty)){
                $LargestDifficultyValue = max($largestQuestionDifficulty);
                if(!empty($LargestDifficultyValue)){
                    For($i=1;$i<=5;$i++){
                        $response['Level'.$i.'_correct_percentage'] = round(((${'correctLevelQuesion'.$i} * 100) / $LargestDifficultyValue),2);
                        $response['Level'.$i.'_wrong_percentage'] = round(((${'wrongLevelQuesion'.$i} * 100) / $LargestDifficultyValue),2);
                    }
                }
            }
        }
        return array_merge($data,$response);
    }

    /**
     * USE : Covert natural value to normalized value
     */
    function getNormalizedAbility($value){
        $value = (exp($value) / (1 + exp($value)) * 100);
        return round($value,2);
    }

    /***
     * USE : Get Percentile
     */
    function getStudentPercentile($rank,$TotalStudents,$overAllNormalizeArray){
        $position = (array_search($rank,$overAllNormalizeArray) + 1);
        $value = ($position / $TotalStudents) * 100;
        return round($value,1);
    }

    function getNormalizedDifficulty($value){
        $value = (exp($value) / (1 + exp($value)) * 100);
        return round($value,2);
    }

    function getTeacherAssignedClasses($schoolid,$teacherid){
        $teacherClassesIds = null;
        $teacherClassesIds = TeachersClassSubjectAssign::where([
                                cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => Self::GetCurriculumYear,
                                cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => $schoolid,
                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => $teacherid
                            ])
                            ->groupBy(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                            ->get()
                            ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
        if(isset($teacherClassesIds) && !empty($teacherClassesIds)){
            return $teacherClassesIds->toArray();
        }
        return $teacherClassesIds;
    }

    // Laravel Pagination set in Cookie
    function paginationCookie($cookieName,$request){
        $items = $request->items ?? 10;
        $oneYear = time()+31556926;
        $currentPage = request()->get('page');
        $PageData = Cookie::get($cookieName);
        if(!empty($PageData)){
            $PageData = json_decode($PageData,true);
            $currentPage = $PageData['currentPage'];
            $currentItems = $PageData['items'];
            if(request()->has('page')){
                $currentPage = request()->get('page');
            }
            if(request()->has('items')){
                $currentItems = request()->get('items');
            }
            $cookieData = array('currentPage' => $currentPage, 'items' => $currentItems);
        }else{
            $cookieData = array('currentPage' => $currentPage, 'items' => $items);
        }
        $cookieData = json_encode($cookieData);
        Cookie::queue($cookieName, $cookieData, $oneYear);
        $PageData = Cookie::get($cookieName);
        if(!empty($PageData)){
            $PageData = json_decode($PageData,true);
            $currentPage = $PageData['currentPage'];
            $currentItems = $PageData['items'];
            if(request()->has('page')){
                $currentPage = request()->get('page');
            }
            $request->merge([
                'page' => $currentPage,
            ]);
            if(request()->has('items')){
                $currentItems=request()->get('items');
            }
            $request->merge([
                'items' => $currentItems,
            ]);
            $items = $currentItems;
        }
    }


    public function getCookie($cookieName){
        $IntelligentTutorData = Cookie::get($cookieName);
        $value = json_decode($IntelligentTutorData);
        return $value;
    }

    //Get Role Base Grades 
    public function GetRoleBasedGrades($roleId){
        switch($roleId){
            case cn::SUPERADMIN_ROLE_ID:
                return $this->GetPluckIds('Grades');
                break;
            case cn::TEACHER_ROLE_ID:
                return $this->GetPluckIds('TeachersClassSubjectAssign');
                break;
            case cn::STUDENT_ROLE_ID:
                //return Grades::where(cn::GRADES_ID_COL,Helper::GetCurriculumDataById($this->LoggedUserId(),Self::GetCurriculumYear(),'grade_id'))->get()->pluck(cn::GRADES_ID_COL)->toArray();
                return Grades::where(cn::GRADES_ID_COL,Auth::user()->CurriculumYearGradeId)->get()->pluck(cn::GRADES_ID_COL)->toArray();
                break;
            case cn::SCHOOL_ROLE_ID:
            case cn::PRINCIPAL_ROLE_ID:
            case cn::PANEL_HEAD_ROLE_ID:
            case cn::CO_ORDINATOR_ROLE_ID:
                return $this->GetPluckIds('GradeClassMapping');
                break;
        }
    }

    /**
     * USE : Get All pluck ids based on selected tables
     */
    public function GetPluckIds($ModelName){
        switch($ModelName){
            case 'Grades':
                return Grades::get()->pluck(cn::GRADES_ID_COL)->toArray();
                break;
            case 'Strands':
                return Strands::get()->pluck(cn::STRANDS_ID_COL)->toArray();
                break;
            case 'LearningsUnits':
                return LearningsUnits::where('stage_id','<>',3)->get()->pluck(cn::LEARNING_UNITS_ID_COL)->toArray();
                break;
            case 'LearningsObjectives':
                    return LearningsObjectives::where('stage_id','<>',3)->get()->pluck(cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                break;
            case 'Languages':
                    return Languages::get()->pluck(cn::LANGUAGES_ID_COL)->toArray();
                break;
            case 'TeachersClassSubjectAssign' :
                    return TeachersClassSubjectAssign::where(cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                            ->where(cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL,Self::GetCurriculumYear())
                            ->where(cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL,Auth::user()->{cn::USERS_ID_COL})
                            ->get()
                            ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                            ->toArray();
                break;
            case 'GradeClassMapping':
                return GradeClassMapping::where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,Auth::user()->school_id)
                        ->where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,Self::GetCurriculumYear())
                        ->get()
                        ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL)
                        ->toArray();
                break;
            default:
                break;
        }
    }
    
    // Laravel LearningTutor Cookie set in Cookie
    function LearningTutorCookie($cookieName,$request){
        $currentGrade = (request()->get('learning_tutor_grade_id')) ? request()->get('learning_tutor_grade_id') : $this->GetRoleBasedGrades(Auth::user()->role_id);//$this->GetPluckIds('Grades');
        $currentStrand = (request()->get('learning_tutor_strand_id')) ? request()->get('learning_tutor_strand_id') : $this->GetPluckIds('Strands');
        $currentLearningUnit = (request()->get('learning_tutor_learning_unit')) ? request()->get('learning_tutor_learning_unit') : $this->GetPluckIds('LearningsUnits');
        $currentLearningObjective = (request()->get('learning_tutor_learning_objectives')) ? request()->get('learning_tutor_learning_objectives') : $this->GetPluckIds('LearningsObjectives');
        $currentLanguage = (request()->get('learning_tutor_language_id')) ? request()->get('learning_tutor_language_id') : $this->GetPluckIds('Languages');
        $currentStatus = (request()->get('learning_tutor_status')) ? request()->get('learning_tutor_status') : 'active';
        $cookieTime = time()+31556926;
        $IntelligentTutorData = Cookie::get($cookieName);
        if(!empty($IntelligentTutorData)){
            $IntelligentTutorData = json_decode($IntelligentTutorData,true);
            $currentGrade = $IntelligentTutorData['learning_tutor_grade_id'];
            $currentStrand = $IntelligentTutorData['learning_tutor_strand_id'];
            $currentLearningUnit = $IntelligentTutorData['learning_tutor_learning_unit'];
            $currentLearningObjective = $IntelligentTutorData['learning_tutor_learning_objectives'];
            $currentLanguage = $IntelligentTutorData['learning_tutor_language_id'];
            $currentStatus = $IntelligentTutorData['learning_tutor_status'];
            
            if(request()->has('learning_tutor_grade_id')){
                $currentGrade = request()->get('learning_tutor_grade_id');
            }
            if(request()->has('learning_tutor_strand_id')){
                $currentStrand = request()->get('learning_tutor_strand_id');
            }
            if(request()->has('learning_tutor_learning_unit')){
                $currentLearningUnit = request()->get('learning_tutor_learning_unit');
            }
            if(request()->has('learning_tutor_learning_objectives')){
                $currentLearningObjective = request()->get('learning_tutor_learning_objectives');
            }
            if(request()->has('learning_tutor_language_id')){
                $currentLanguage = request()->get('learning_tutor_language_id');
            }
            if(request()->has('learning_tutor_status')){
                $currentStatus = request()->get('learning_tutor_status');
            }
           
            $cookieData = [
                            'learning_tutor_grade_id' => $currentGrade,
                            'learning_tutor_strand_id' => $currentStrand,
                            'learning_tutor_learning_unit' => $currentLearningUnit,
                            'learning_tutor_learning_objectives' => $currentLearningObjective,
                            'learning_tutor_language_id' => $currentLanguage,
                            'learning_tutor_status' => $currentStatus
                        ];

            $request->merge($cookieData);
            $array_json = json_encode($cookieData);
            Cookie::queue($cookieName, $array_json, $cookieTime);
            $items = $request;
        }else{
            $cookieData = [
                            'learning_tutor_grade_id' => $currentGrade,'learning_tutor_strand_id' => $currentStrand,
                            'learning_tutor_learning_unit' => $currentLearningUnit,
                            'learning_tutor_learning_objectives' => $currentLearningObjective,
                            'learning_tutor_language_id' => $currentLanguage,
                            'learning_tutor_status' => $currentStatus
                        ];
            $array_json = json_encode($cookieData);
            Cookie::queue($cookieName, $array_json, $cookieTime);
        }
    }

    /**
     * USE : Set learningObjectivesTitle 
     */
    function setLearningObjectivesTitle($learningObjectivesTitle){        
        $learningObjectivesTitleArray = explode(' ', $learningObjectivesTitle);
        $learningObjectivesTitlewithBr = '';
        $brIndex = 0;
        for($iw = 0; $iw < sizeof($learningObjectivesTitleArray) ; $iw++){
            if($learningObjectivesTitlewithBr != ""){
                $learningObjectivesTitlewithBr.=' '.$learningObjectivesTitleArray[$iw];
            }else{
                $learningObjectivesTitlewithBr.=$learningObjectivesTitleArray[$iw];
            }
            if($brIndex == 2){
                $learningObjectivesTitlewithBr.=' <br />';
                $brIndex=-1;
            }
            $brIndex++;
        }
        $learningObjectivesTitlewithBr = preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $learningObjectivesTitlewithBr);
        return $learningObjectivesTitlewithBr;
    }

    /**
     * USE : Get time option array
     */
    function getTimeSlot(){
        return [
            1 => '00:00',
            2 => '01:00',
            3 => '02:00',
            4 => '03:00',
            5 => '04:00',
            6 => '05:00',
            7 => '06:00',
            8 => '07:00',
            9 => '08:00',
            10 => '09:00',
            11 => '10:00',
            12 => '11:00',
            13 => '12:00',
            14 => '13:00',
            15 => '14:00',
            16 => '15:00',
            17 => '16:00',
            18 => '17:00',
            19 => '18:00',
            20 => '19:00',
            21 => '20:00',
            22 => '21:00',
            23 => '22:00',
            24 => '23:00'
        ];
    }

    /**
     * USE : Get question ids from AI Api
     */
    function generateQuestionsAI($request,$StrandUnitsObjectivesMappingsId){
        $response = [];
        $questionIds = '';
        $questionId_data = array();
        //$gradeId = Auth::user()->grade_id;
        $gradeId = Auth::user()->CurriculumYearGradeId;
        $objective_mapping_id = $StrandUnitsObjectivesMappingsId;
        $difficulty_lvl = $request->dificulty_level;
        $selected_levels = array();
        foreach ($difficulty_lvl as $difficulty_value) {
            $selected_levels[] = ($difficulty_value - 1);
        }
        $no_of_questions_per_learning_skills = $this->getGlobalConfiguration('no_of_questions_per_learning_skills');
        if(empty($no_of_questions_per_learning_skills)){
            $no_of_questions_per_learning_skills = 2;
        }
        $no_of_questions = 10;
        if(isset($request->no_of_questions) && !empty($request->no_of_questions)){
            $no_of_questions = $request->no_of_questions;
        }
        if(!empty($objective_mapping_id)){
            $user_id = Auth::user()->{cn::USERS_ID_COL};
            $oldQesList = "";
            $oldExamId = ExamConfigurationsDetails::where(cn::EXAM_CONFIGURATIONS_DETAILS_CREATED_BY_USER_ID_COL,$user_id)
                        ->pluck(cn::EXAM_CONFIGURATIONS_DETAILS_EXAM_ID_COL)
                        ->toArray();
            $oldExamList = Exam::whereIn(cn::EXAM_TABLE_ID_COLS,$oldExamId)
                            ->pluck(cn::EXAM_TABLE_QUESTION_IDS_COL
                            )->toArray();
            if(isset($oldExamList) && !empty($oldExamList)){
                $oldQesList = implode(',',$oldExamList);
                $oldQesList = explode(',',$oldQesList);
            }
            $questionId_data_list = Question::where(cn::QUESTION_QUESTION_TYPE_COL,1) // 1 = Self-learning questions
                                    ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$objective_mapping_id)
                                    ->groupBy(cn::QUESTION_QUESTION_CODE_COL)
                                    ->pluck(cn::QUESTION_QUESTION_CODE_COL)
                                    ->toArray();
            
            $question_code_skills = array();
            foreach ($questionId_data_list as $question_code_s) {
                $question_code_exp = explode('-', $question_code_s);
                $question_code_skills[] = $question_code_exp[0].'-'.$question_code_exp[1].'-'.$question_code_exp[2].'-'.substr($question_code_exp[3],0,2);
            }
            if(isset($question_code_skills) && !empty($question_code_skills)){
                $question_code_skills = array_unique($question_code_skills);
                $question_code_skills = array_values($question_code_skills);
            }else{
                return array();
            }
            rsort($difficulty_lvl);
            $qLoop = 0;
            $question_id_list = '';
            $coded_questions_list = array();
            while($qLoop <= $no_of_questions){
                foreach($question_code_skills as $question_code){
                    foreach($difficulty_lvl as $difficulty){
                        //$questionId_data_list = Question::with('PreConfigurationDifficultyLevel')->where(cn::QUESTION_QUESTION_TYPE_COL,1) // 1 = Self-learning questions
                        $questionId_data_list = Question::where(cn::QUESTION_QUESTION_TYPE_COL,1) // 1 = Self-learning questions
                            ->where(cn::QUESTION_QUESTION_CODE_COL,'like',$question_code.'%')
                            ->where(function ($query) use ($oldQesList){
                                if(!empty($oldQesList)){
                                    $query->whereNotIn(cn::QUESTION_TABLE_ID_COL,$oldQesList);
                                }
                            })
                            ->where(cn::QUESTION_DIFFICULTY_LEVEL_COL,$difficulty)
                            ->where(function ($query) use ($question_id_list){
                                if($question_id_list != ""){
                                    $question_id_list_array = explode(',', $question_id_list);
                                    $query->whereNotIn(cn::QUESTION_TABLE_ID_COL,$question_id_list_array);
                                }
                            })
                            ->limit($no_of_questions_per_learning_skills);
                            $questionId_list = $questionId_data_list->pluck(cn::QUESTION_TABLE_ID_COL)
                            ->toArray();
                        $question_data = $questionId_data_list->get()->toArray();
                        if(isset($questionId_list) && !empty($questionId_list)){
                            if($question_id_list != ""){
                                $question_id_list.=','.implode(',', $questionId_list);
                            }else{
                                $question_id_list.=implode(',', $questionId_list);
                            }
                            foreach ($question_data as $question_key => $question_value) {
                                //$coded_questions_list[] = array($question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],floatval($question_data[0]['pre_configuration_difficulty_level']['title']),0);
                                $coded_questions_list[] = array($question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],floatval($question_data[0]['PreConfigurationDifficultyLevel']->title),0);
                            }
                        }
                        $qSize = sizeof(explode(',',$question_id_list));
                        if($qSize >= $no_of_questions){
                            if($qSize > $no_of_questions){
                                $question_id_list_tmp = explode(',',$question_id_list);
                                array_pop($question_id_list_tmp);
                                $question_id_list = implode(',',$question_id_list_tmp);
                            }
                            break;
                        }
                    }
                }
                if($qSize >= $no_of_questions){
                    break;
                }
                $qLoop++;
            }
            return $coded_questions_list = array_slice($coded_questions_list, 0, $no_of_questions);
        }
    }

    /**
     * USE : Get question ids from AI Api
     */
    function generateQuestionsAIQenerateQuestions($request,$StrandUnitsObjectivesMappingsId){
        $response = [];
        $questionIds = '';
        $questionId_data = array();
        //$gradeId = Auth::user()->grade_id;
        $gradeId = Auth::user()->CurriculumYearGradeId;
        $objective_mapping_id = $StrandUnitsObjectivesMappingsId;
        $difficulty_lvl = $request->dificulty_level;
        $selected_levels = array();
        foreach ($difficulty_lvl as $difficulty_value) {
            $selected_levels[] = $difficulty_value-1;
        }
        $no_of_questions_per_learning_skills = $this->getGlobalConfiguration('no_of_questions_per_learning_skills');
        if(empty($no_of_questions_per_learning_skills)){
            $no_of_questions_per_learning_skills = 2;
        }
        $no_of_questions = 10;
        if(isset($request->no_of_questions) && !empty($request->no_of_questions)){
            $no_of_questions = $request->no_of_questions;
        }
        if(!empty($objective_mapping_id)){
            $user_id = Auth::user()->{cn::USERS_ID_COL};
            $oldQesList = "";
            $oldExamId = ExamConfigurationsDetails::where(cn::EXAM_CONFIGURATIONS_DETAILS_CREATED_BY_USER_ID_COL,$user_id)
                            ->pluck(cn::EXAM_CONFIGURATIONS_DETAILS_EXAM_ID_COL)
                            ->toArray();
            $oldExamList = Exam::whereIn(cn::EXAM_TABLE_ID_COLS,$oldExamId)->pluck(cn::EXAM_TABLE_QUESTION_IDS_COL)->toArray();
            if(isset($oldExamList) && !empty($oldExamList)){
                $oldQesList = implode(',',$oldExamList);
                $oldQesList = explode(',',$oldQesList);
            }
            if($oldQesList!=""){
                $oldQesList = array_merge($oldQesList,$request->old_question_ids);
            }else{
                $oldQesList = $request->old_question_ids;
            }            
            $questionId_data_list = Question::whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$objective_mapping_id)
                                    //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                    ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,[2,3])
                                    ->whereNotIn(cn::QUESTION_QUESTION_TYPE_COL,$request->old_question_ids)
                                    ->groupBy(cn::QUESTION_QUESTION_CODE_COL)
                                    ->pluck(cn::QUESTION_QUESTION_CODE_COL)
                                    ->toArray();
            $question_code_skills = array();
            foreach ($questionId_data_list as $question_code_s) {
                $question_code_exp = explode('-', $question_code_s);
                $question_code_skills[] = $question_code_exp[0].'-'.$question_code_exp[1].'-'.$question_code_exp[2].'-'.substr($question_code_exp[3],0,2);
            }
            if(isset($question_code_skills) && !empty($question_code_skills)){
                $question_code_skills = array_unique($question_code_skills);
                $question_code_skills = array_values($question_code_skills);
            }else{
                return array();
            }
            rsort($difficulty_lvl);
            $qLoop = 0;
            $question_id_list = '';
            $coded_questions_list = array();
            foreach($question_code_skills as $question_code){
                foreach($difficulty_lvl as $difficulty){
                    //$questionId_data_list = Question::with('PreConfigurationDifficultyLevel')->where(cn::QUESTION_QUESTION_CODE_COL,'like',$question_code.'%')
                    $questionId_data_list = Question::where(cn::QUESTION_QUESTION_CODE_COL,'like',$question_code.'%')
                    //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                    ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,[2,3])
                    ->where(function ($query) use ($oldQesList){
                        if(!empty($oldQesList)){
                            $query->whereNotIn(cn::QUESTION_TABLE_ID_COL,$oldQesList);
                        }
                    })
                    ->where(cn::QUESTION_DIFFICULTY_LEVEL_COL,$difficulty)
                    ->where(function ($query) use ($question_id_list){
                        if($question_id_list != ""){
                            $question_id_list_array = explode(',', $question_id_list);
                            $query->whereNotIn(cn::QUESTION_TABLE_ID_COL,$question_id_list_array);
                        }
                    });
                    //->limit($no_of_questions_per_learning_skills);
                    $questionId_list = $questionId_data_list->pluck(cn::QUESTION_TABLE_ID_COL)->toArray();
                    $question_data = $questionId_data_list->get()->toArray();
                    if(isset($questionId_list) && !empty($questionId_list)){
                        if($question_id_list != ""){
                            $question_id_list.=','.implode(',', $questionId_list);
                        }else{
                            $question_id_list.=implode(',', $questionId_list);
                        }
                        foreach ($question_data as $question_key => $question_value) {
                            //$coded_questions_list[] = array($question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],floatval($question_data[0]['pre_configuration_difficulty_level']['title']),0);
                            $coded_questions_list[] = array($question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],floatval($question_data[0]['PreConfigurationDifficultyLevel']->title),0);
                        }
                    }
                }
            }
            return $coded_questions_list;
        }
    }

    /**
     * USE : Get Student Peer Group Ids using Auth Id
     */
    protected static function getStudentPeerGroupIds(){
        return PeerGroupMember::where([
                cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL => Self::GetCurriculumYear(),
                cn::PEER_GROUP_MEMBERS_STATUS_COL => 1,
                cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL => Auth::user()->id
            ])
            ->pluck(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL)
            ->toArray();
    }

    /**
     * USE : Get Default option list for credit points
     */
    function GetDefaultCreditPointsRules(){
        $DefaultCreditPointRules = array();
        $DefaultCreditPointRules = GlobalConfiguration::whereIn('key',['submission_on_time','credit_points_of_accuracy','credit_points_of_normalized_ability'])
                    ->where('value','yes')
                    ->get()->toArray();
        return $DefaultCreditPointRules;
    }

    /**
     * USE : Check student master or not in learning objectives
     * Return : "True" or "false"
     */
    public function CheckLearningObjectivesMastered($StudentAbility){
        $CheckObjectivesMastered = false;
        $LearningObjectivesMasteredLevel = Helper::getGlobalConfiguration('study_status_master');
        if(!empty($StudentAbility) && $StudentAbility >= \App\Helpers\Helper::getStudyStatusValue($LearningObjectivesMasteredLevel)['from']){        
            $CheckObjectivesMastered = true;
        }
        return $CheckObjectivesMastered;
    }

    /**
     * USE : Get Student Attempted Exam Ids
     */
    function GetStudentAttemptedExamIds($StudentId){
        $ExamIds = array();
        $ExamIds = AttemptExams::where([
                    cn::ATTEMPT_EXAMS_CURRICULUM_YEAR_ID_COL => Self::GetCurriculumYear(),
                    cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID => $StudentId
                ])->pluck(cn::ATTEMPT_EXAMS_EXAM_ID);
        return $ExamIds;
    }

    /*Export Result Summary & export performance report combine logic */
    public static function getStudentResultSummary($request){
        $questionIds = '';
        $AttemptExamData = [];
        $studentIds = '';
        $QuestionList = '';
        $correctAnswerArray = [];
        $QuestionAnswerData = [];
        $examData = '';
        $records = [];
        $QuestionHeaders = [];
        $totalStudent = 0;
    
        //Get Exam ID
        $examId = $request->examId;
        if(is_array($request->studentIds)){
            $GetStudentIds = ($request->studentIds) ? $request->studentIds : [];
        }else{
            $GetStudentIds = ($request->studentIds) ? explode(',',$request->studentIds) : [];
        }
        $classIds = $request->classIds;
        $groupIds = $request->groupIds;

        //Set Main Header Row
        $header = [
            'Class',
            'Student No. Within Class'
        ];

        //Set Correct Answer Row
        $correctAnswerArray = [
            '',
            'Correct Answers'
        ];

        //Set Sub Main Header
        $QuestionHeaders = [
            '',
            'Questions'
        ];
        $ExamData = Exam::find($examId);
        if(!empty($ExamData)){
            if($ExamData->exam_type == 1 || empty($request->classIds)){
                $userData = User::find($request->studentIds);
                if(array_key_exists(0, $userData->toArray())){
                    $classIds = array($userData[0]->CurriculumYearClassId);
                }else{
                    $classIds = array($userData->CurriculumYearClassId);
                }
            }
            //Get Questions in Exam Assigns
            if(!empty($ExamData->{cn::EXAM_TABLE_QUESTION_IDS_COL})){
                $questionIds = explode(',',$ExamData->{cn::EXAM_TABLE_QUESTION_IDS_COL});
                $QuestionList = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)->get();
            }
            
            //Get Student data in Exam Assigns
            if(!empty($ExamData->{cn::EXAM_TABLE_STUDENT_IDS_COL})){
                $studentIds = explode(',',$ExamData->{cn::EXAM_TABLE_STUDENT_IDS_COL});
            }
            //Set Header And Correct Answer Array
            foreach($QuestionList as $questionKey =>  $question){
                $header[] = 'Q'.($questionKey + 1);
                $QuestionHeaders[] = ($questionKey + 1);
                $correctAnswerArray[] = SELF::setOptionBasedAlphabet($question->answers->correct_answer_en);
            }
            if($ExamData->exam_type != 1){
                $header[] = 'Overall Percentile'; 
            }
                      
            //Set Heading
            $records['heading'] = $header;
            // Store in first row headings
            $records[] = $correctAnswerArray;
            $Query = AttemptExams::with('user')->where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId);
            $getAllStudentIds = [];
            $getStudentsFromClassIds = [];
            $getStudentFromGroupIds = [];

            if(SELF::isAdmin()){
                if(!empty($classIds)){
                    // $getStudentsFromClassIds = User::whereIn(cn::USERS_CLASS_ID_COL,$classIds)->pluck(cn::USERS_ID_COL)->toArray();
                    $CommonObject = new Self;
                    $getStudentsFromClassIds = User::whereIn(cn::USERS_ID_COL,$CommonObject->curriculum_year_mapping_student_ids('',$classIds,''))->pluck(cn::USERS_ID_COL)->toArray();
                }
                if(!empty($groupIds)){
                    $getStudentFromGroupIds =  PeerGroupMember::where(cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL,Self::GetCurriculumYear())
                                                ->whereIn(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL,$groupIds)
                                                ->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL)
                                                ->unique()
                                                ->toArray();
                }
                $getAllStudentIds = array_unique(array_merge($getStudentsFromClassIds,$getStudentFromGroupIds));
                $AttemptExamData = $Query->whereHas('user',function($q) use($getAllStudentIds){
                    $q->whereIn(cn::USERS_ID_COL,$getAllStudentIds);
                })->get();
            }

            if(SELF::isTeacherLogin()){
                if(!empty($GetStudentIds)){
                    $AttemptExamData = $Query->whereHas('user',function($q) use($GetStudentIds){
                        $q->where(cn::USERS_SCHOOL_ID_COL, Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                            ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                            ->whereIn(cn::USERS_ID_COL,$GetStudentIds);
                        })->get();
                }else{
                    if(empty($groupIds)){
                        $TeachersGradeClass = TeachersClassSubjectAssign::where([
                                                cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => Self::GetCurriculumYear(),
                                                cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth::user()->{cn::USERS_ID_COL}
                                            ]);
                        if(!empty($TeachersGradeClass)){
                            $assignTeacherGrades = $TeachersGradeClass->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
                            if(!empty($TeachersGradeClass->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)->toArray())){
                                $assignTeacherClass = explode(',',implode(',',$TeachersGradeClass->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)->toArray()));
                                $AttemptExamData = $Query->whereHas('user',function($q) use($assignTeacherGrades,$assignTeacherClass, $classIds){
                                    $q->where(cn::USERS_SCHOOL_ID_COL, Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                                        ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                        ->get()
                                        ->whereIn('CurriculumYearClassId',$classIds)
                                        ->whereIn('CurriculumYearGradeId',$assignTeacherGrades);
                                    })->get();
                            }
                        }      
                    }else{
                        $peerGroupIds = PeerGroup::where(cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL,Self::GetCurriculumYear())
                                        ->whereIn(cn::PEER_GROUP_ID_COL,$groupIds)
                                        ->where(cn::PEER_GROUP_CREATED_BY_USER_ID_COL,Auth::user()->{cn::USERS_ID_COL})
                                        ->pluck(cn::PEER_GROUP_ID_COL)
                                        ->toArray();
                        if(!empty($peerGroupIds)){
                            $peerGroupMemberIds = PeerGroupMember::where(cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL,Self::GetCurriculumYear())
                                                ->whereIn(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL,$peerGroupIds)
                                                ->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL)
                                                ->unique()
                                                ->toArray();
                            $AttemptExamData = $Query->whereHas('user',function($q) use($peerGroupMemberIds){
                                $q->where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                                ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                ->whereIn(cn::USERS_ID_COL,$peerGroupMemberIds);
                            })->get();
                        }
                    }
                }               
            }

            if(SELF::isPrincipalLogin() || SELF::isSchoolLogin() || SELF::isPanelHeadLogin() || SELF::isCoOrdinatorLogin()){
                if(!empty($GetStudentIds)){
                    $AttemptExamData = $Query->whereHas('user',function($q) use($GetStudentIds){
                        $q->where(cn::USERS_SCHOOL_ID_COL, Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                            ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                            ->whereIn(cn::USERS_ID_COL,$GetStudentIds);
                        })->get();
                }else{
                    if(empty($groupIds)){
                        $AttemptExamData = $Query->whereHas('user',function($q) use($classIds){
                            $q->where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                            ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                            ->whereIn(cn::USERS_CLASS_ID_COL, $classIds);
                        })->get();
                    }else{
                        $peerGroupIds = PeerGroup::where(cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL,Self::GetCurriculumYear())
                                        ->whereIn(cn::PEER_GROUP_ID_COL,$groupIds)
                                        ->pluck(cn::PEER_GROUP_ID_COL)
                                        ->toArray();
                        if(!empty($peerGroupIds)){
                            $peerGroupMemberIds =   PeerGroupMember::where(cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL,Self::GetCurriculumYear())
                                                    ->whereIn(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL,$peerGroupIds)
                                                    ->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL)
                                                    ->unique()
                                                    ->toArray();
                            $AttemptExamData =  $Query->whereHas('user',function($q) use($peerGroupMemberIds){
                                                    $q->where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                                                    ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                                    ->whereIn(cn::USERS_ID_COL,$peerGroupMemberIds);
                                                })->get();
                        }
                    }
                }
            }
            if($AttemptExamData->isNotEmpty()){
                $totalStudent = count($AttemptExamData);
                foreach($AttemptExamData as $attemptedExamKey => $attemptedExam){
                    $rowArray = [];
                    $rowArray[] = $attemptedExam->user->CurriculumYearData[cn::CURRICULUM_YEAR_STUDENT_CLASS] ?? '';
                    $rowArray[] = ($attemptedExam->user->CurriculumYearData[cn::CURRICULUM_YEAR_CLASS_STUDENT_NUMBER]!='') ? $attemptedExam->user->CurriculumYearData[cn::CURRICULUM_YEAR_CLASS_STUDENT_NUMBER] : Self::decrypt($attemptedExam->user->name_en);
                    foreach($QuestionList as $questionKey => $questions){
                    // get Selected Answer
                        if(isset($attemptedExam->question_answers)){
                            $filterAttemptQuestionAnswer = array_filter(json_decode($attemptedExam->question_answers), function ($var) use($questions){
                                if($var->question_id == $questions->id){
                                    return $var ?? [];
                                }
                            });
                            //Count Answer Selected By Student
                            if(isset($filterAttemptQuestionAnswer) && !empty($filterAttemptQuestionAnswer)){
                                foreach($filterAttemptQuestionAnswer as $fanswer){
                                    $objToArrayConvert = get_object_vars($fanswer);
                                    $rowArray[] = SELF::setOptionBasedAlphabet($objToArrayConvert['answer']);
                                    if($fanswer->answer==1){
                                        if(isset($QuestionAnswerData[$questionKey]['1'])){
                                            $QuestionAnswerData[$questionKey]['1'] = $QuestionAnswerData[$questionKey]['1'] + 1;

                                        }else{
                                            $QuestionAnswerData[$questionKey]['1'] = 1;
                                        }
                                    }
                                    if($fanswer->answer==2){
                                        if(isset($QuestionAnswerData[$questionKey]['2'])){
                                            $QuestionAnswerData[$questionKey]['2'] = $QuestionAnswerData[$questionKey]['2'] + 1;
                                        }else{
                                            $QuestionAnswerData[$questionKey]['2'] = 1;
                                        }
                                    }
                                    if($fanswer->answer==3){
                                        if(isset($QuestionAnswerData[$questionKey]['3'])){
                                            $QuestionAnswerData[$questionKey]['3'] = $QuestionAnswerData[$questionKey]['3'] + 1;
                                        }else{
                                            $QuestionAnswerData[$questionKey]['3'] = 1;
                                        }
                                    }
                                    if($fanswer->answer==4){
                                        if(isset($QuestionAnswerData[$questionKey]['4'])){
                                            $QuestionAnswerData[$questionKey]['4'] = $QuestionAnswerData[$questionKey]['4'] + 1;
                                        }else{
                                            $QuestionAnswerData[$questionKey]['4'] = 1;
                                        }
                                    }
                                    if($fanswer->answer==5){
                                        if(isset($QuestionAnswerData[$questionKey]['N'])){
                                            $QuestionAnswerData[$questionKey]['N'] = $QuestionAnswerData[$questionKey]['N'] + 1;
                                        }else{
                                            $QuestionAnswerData[$questionKey]['N'] = 1;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if($ExamData->exam_type != 1){
                        $rowArray[] = self::GetStudentPercentileRank($examId,$attemptedExam->user->id).'%';
                    }
                    $records[] = $rowArray;
                }
                
                //Set Selected Answers Row On Based Question
                $HeadingIndexArray = ['1','2','3','4','N','1%','2%','3%','4%','N%','Correct %'];
                for($row = 1;$row <= count($HeadingIndexArray);$row++){
                    $rowArray = [];
                    if($row == 1){
                        $rowArray[] = 'Total Students : '.$totalStudent;
                        $rowArray[] = ($HeadingIndexArray[$row-1]);                        
                    }else{
                        $rowArray[] = '';
                        $rowArray[] = ($HeadingIndexArray[$row-1]);
                    }
                    foreach($QuestionList as $questionKey => $question){
                        switch($row){
                            case 1 :    //Case 1 : is used For Display Row  of 1 = No. of Student Selected Answer 1
                                $rowArray[] = ($QuestionAnswerData[$questionKey]['1']) ?? 0;
                                break;
                            case 2:     //Case 2 : is used For Display Row  of 2 = No. of Student Selected Answer 2
                                $rowArray[] = ($QuestionAnswerData[$questionKey]['2']) ?? 0;
                                break;
                            case 3:     //Case 3 : is used For Display Row  of 3 = No. of Student Selected Answer 3
                                $rowArray[] = ($QuestionAnswerData[$questionKey]['3']) ?? 0;
                                break;
                            case 4:     //Case 4 : is used For Display Row  of 4 = No. of Student Selected Answer 4
                                $rowArray[] = ($QuestionAnswerData[$questionKey]['4']) ?? 0;
                                break;
                            case 5:     //Case 4 : is used For Display Row  of N = No. of Student Selected Answer N
                                $rowArray[] = ($QuestionAnswerData[$questionKey]['N']) ?? 0;
                                break;
                            case 6:     //Case 5 : is used For Display Row  of 1(%) = Average of Student Selected Answer 1(%)
                                $value = ($QuestionAnswerData[$questionKey]['1']) ?? 0;
                                $rowArray[] = round((($value * 100) / $totalStudent),2);
                                break;
                            case 7:     //Case 5 : is used For Display Row  of 2(%) = Average of Student Selected Answer 2(%)
                                $value = ($QuestionAnswerData[$questionKey]['2']) ?? 0;
                                $rowArray[] =  round((($value * 100) / $totalStudent),2);
                                break;
                            case 8:     //Case 5 : is used For Display Row  of 3(%) = Average of Student Selected Answer 3(%)
                                $value = ($QuestionAnswerData[$questionKey]['3']) ?? 0;
                                $rowArray[] =  round((($value * 100) / $totalStudent),2);
                                break;
                            case 9:     //Case 5 : is used For Display Row  of 4(%) = Average of Student Selected Answer 4(%)
                                $value = ($QuestionAnswerData[$questionKey]['4']) ?? 0;
                                $rowArray[] =  round((($value * 100) / $totalStudent),2);
                                break;
                            case 10:     //Case 5 : is used For Display Row  of N(%) = Average of Student Selected Answer N(%)
                                $value = ($QuestionAnswerData[$questionKey]['N']) ?? 0;
                                $rowArray[] =  round((($value * 100) / $totalStudent),2);
                                break;
                            case 11://Average of Student Selected  Correct Answer(%)
                                $value = ($QuestionAnswerData[$questionKey]['1']) ?? 0;
                                $rowArray[] =  round((($value * 100) / $totalStudent),2);
                                break;
                        }
                    }                    
                    //Total Attempted Student + Sub Main Heading + row // Maintain Rows(with Sub Heading)
                    $records[(count($ExamData->attempt_exams) +1)+$row] =  $rowArray;
                }
            }else{
                $records = null;
            }
            return $records;
        }
    }

    /**
     * USE : Get AI-API Labels Array based on selected languages
     */
    public function GetAiApiLabels($ApiName, $isGroup=false){
        $labels = array();
        switch($ApiName){
            case config()->get('aiapi.api.Plot_Analyze_Question.uri'):
                $labels = [
                    'label_1' => __('languages.aiapi_label.Plot_Analyze_Question.label_1'),                    
                    'label_2' => __('languages.aiapi_label.Plot_Analyze_Question.label_2'),
                    'label_3' => __('languages.aiapi_label.Plot_Analyze_Question.label_3'),
                    'plot_title' => __('languages.aiapi_label.Plot_Analyze_Question.plot_title'),
                    'x_axis' => __('languages.aiapi_label.Plot_Analyze_Question.x_axis'),
                    'left_y_axis' => __('languages.aiapi_label.Plot_Analyze_Question.left_y_axis'),
                    'right_y_axis' => __('languages.aiapi_label.Plot_Analyze_Question.right_y_axis')
                ];
                break;
            case config()->get('aiapi.api.Plot_Analyze_Student.uri'):
                $labels = [
                    'label_1' => __('languages.aiapi_label.Plot_Analyze_Student.label_1'),
                    'label_2' => __('languages.aiapi_label.Plot_Analyze_Student.label_2'),
                    'label_3' => __('languages.aiapi_label.Plot_Analyze_Student.label_3'),
                    'plot_title' => __('languages.aiapi_label.Plot_Analyze_Student.plot_title'),
                    'x_axis' => __('languages.aiapi_label.Plot_Analyze_Student.x_axis'),
                    'left_y_axis' => __('languages.aiapi_label.Plot_Analyze_Student.left_y_axis'),
                    'right_y_axis' => __('languages.aiapi_label.Plot_Analyze_Student.right_y_axis'),
                ];
                break;
            case config()->get('aiapi.api.Plot_Analyze_My_Class_Ability.uri'):
                $labels = [
                    'label_1' => __('languages.aiapi_label.Plot_Analyze_My_Class_Ability.label_1'),
                    'label_2' => '',
                    'label_3' => '',
                    'plot_title' => __('languages.aiapi_label.Plot_Analyze_My_Class_Ability.plot_title'),
                    'left_y_axis' => __('languages.aiapi_label.Plot_Analyze_My_Class_Ability.left_y_axis'),
                    'right_y_axis' => __('languages.aiapi_label.Plot_Analyze_My_Class_Ability.right_y_axis'),
                    'x_axis' => __('languages.aiapi_label.Plot_Analyze_My_Class_Ability.x_axis'),
                    'median_label' => __('languages.aiapi_label.Plot_Analyze_My_School_Ability.median_label'),
                    'mean_label' => __('languages.aiapi_label.Plot_Analyze_My_School_Ability.mean_label'),
                    'std_label' => __('languages.aiapi_label.Plot_Analyze_My_School_Ability.std_label'),
                ];
                if($isGroup===true){
                    $labels['label_1'] = __('languages.aiapi_label.abilities_of_my_group');
                    $labels['plot_title'] = __('languages.aiapi_label.my_group_abilities');
                }
                break;
            case config()->get('aiapi.api.Plot_Analyze_My_School_Ability.uri'):
                $labels = [
                    'label_1' => __('languages.aiapi_label.Plot_Analyze_My_School_Ability.label_1'),
                    'label_2' => __('languages.aiapi_label.Plot_Analyze_My_School_Ability.label_2'),
                    'label_3' => '',
                    'plot_title' => __('languages.aiapi_label.Plot_Analyze_My_School_Ability.plot_title'),
                    'left_y_axis' => __('languages.aiapi_label.Plot_Analyze_My_School_Ability.left_y_axis'),
                    'right_y_axis' => __('languages.aiapi_label.Plot_Analyze_My_School_Ability.right_y_axis'),
                    'x_axis' => __('languages.aiapi_label.Plot_Analyze_My_School_Ability.x_axis'),
                    'median_label' => __('languages.aiapi_label.Plot_Analyze_My_School_Ability.median_label'),
                    'mean_label' => __('languages.aiapi_label.Plot_Analyze_My_School_Ability.mean_label'),
                    'std_label' => __('languages.aiapi_label.Plot_Analyze_My_School_Ability.std_label'),
                ];
                if($isGroup===true){
                    $labels['label_1'] = __('languages.aiapi_label.abilities_of_my_group');
                    $labels['plot_title'] = __('languages.aiapi_label.my_group_vs_my_school');
                }
                break;
            case config()->get('aiapi.api.Plot_Analyze_All_Schools_Ability.uri'):
                $labels = [
                    'label_1' => __('languages.aiapi_label.Plot_Analyze_All_Schools_Ability.label_1'),
                    'label_2' => __('languages.aiapi_label.Plot_Analyze_All_Schools_Ability.label_2'),
                    'label_3' => __('languages.aiapi_label.Plot_Analyze_All_Schools_Ability.label_3'),
                    'plot_title' => __('languages.aiapi_label.Plot_Analyze_All_Schools_Ability.plot_title'),
                    'left_y_axis' => __('languages.aiapi_label.Plot_Analyze_All_Schools_Ability.left_y_axis'),
                    'right_y_axis' => __('languages.aiapi_label.Plot_Analyze_All_Schools_Ability.right_y_axis'),
                    'x_axis' => __('languages.aiapi_label.Plot_Analyze_All_Schools_Ability.x_axis'),
                    'median_label' => __('languages.aiapi_label.Plot_Analyze_My_School_Ability.median_label'),
                    'mean_label' => __('languages.aiapi_label.Plot_Analyze_My_School_Ability.mean_label'),
                    'std_label' => __('languages.aiapi_label.Plot_Analyze_My_School_Ability.std_label'),
                ];
                if($isGroup===true){
                    $labels['label_1'] = __('languages.aiapi_label.abilities_of_my_group');
                    $labels['plot_title'] = __('languages.aiapi_label.my_group_vs_my_school_vs_all_school');
                }
                break;
            case config()->get('aiapi.api.Plot_Analyze_Test_Difficulty.uri'):
                $labels = [
                    'label_1' => __('languages.aiapi_label.Plot_Analyze_Test_Difficulty.label_1'),
                    'plot_title' => __('languages.aiapi_label.Plot_Analyze_Test_Difficulty.plot_title'),
                    'left_y_axis' => __('languages.aiapi_label.Plot_Analyze_Test_Difficulty.left_y_axis'),
                    'right_y_axis' => __('languages.aiapi_label.Plot_Analyze_Test_Difficulty.right_y_axis'),
                    'x_axis' => __('languages.aiapi_label.Plot_Analyze_Test_Difficulty.x_axis'),
                    'median_label' => __('languages.aiapi_label.Plot_Analyze_Test_Difficulty.median_label'),
                    'mean_label' => __('languages.aiapi_label.Plot_Analyze_Test_Difficulty.mean_label'),
                    'std_label' => __('languages.aiapi_label.Plot_Analyze_Test_Difficulty.std_label'),
                ];
                break;
            default:
                break;
        }
        return $labels;
    }

    /**
     * USE : Count no of answer by question id
     */
    public function CountNoOfAnswerByQuestionId($QuestionId){
        $AnswerData = Answer::where('question_id',$QuestionId)->first()->toArray();
        if(isset($AnswerData) && !empty($AnswerData)){
            if(!empty($AnswerData['answer1_en']) && !empty($AnswerData['answer2_en']) && !empty($AnswerData['answer3_en']) && !empty($AnswerData['answer4_en'])){
                $NoOfAnswers = 4;
            }else if(!empty($AnswerData['answer1_en']) && !empty($AnswerData['answer2_en']) && !empty($AnswerData['answer3_en'])){
                $NoOfAnswers = 3;
            }else if(!empty($AnswerData['answer1_en']) && !empty($AnswerData['answer2_en'])){
                $NoOfAnswers = 2;
            }else if(!empty($AnswerData['answer1_en'])){
                $NoOfAnswers = 1;
            }else{
                $NoOfAnswers = 0;
            }
        }
        return $NoOfAnswers;
    }

    /**
     * USE : Get Reference number of Test/Exercise
     */
    public function GetMaxReferenceNumberExam($TestType,$self_learning_test_type=null){
        $MaxReferenceNumber = 10000000001;
        $ExamPrefix = '';
        switch($TestType){
            case 1:
                if($self_learning_test_type==1){
                    $MaxReferenceNumber = Exam::where(cn::EXAM_TYPE_COLS,1)->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,1)->max(cn::EXAM_REFERENCE_NO_COL);
                    $ExamPrefix = 'S';
                }else{
                    $MaxReferenceNumber = Exam::where(cn::EXAM_TYPE_COLS,1)->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2)->max(cn::EXAM_REFERENCE_NO_COL); 
                    $ExamPrefix = 'Z';
                }
                break;
            case 2:
                $MaxReferenceNumber = Exam::where(cn::EXAM_TYPE_COLS,2)->max(cn::EXAM_REFERENCE_NO_COL);
                $ExamPrefix = 'E';
                break;
            case 3:
                $MaxReferenceNumber = Exam::where(cn::EXAM_TYPE_COLS,3)->max(cn::EXAM_REFERENCE_NO_COL);
                $ExamPrefix = 'T';
                break;
        }
        if(!empty($MaxReferenceNumber)){
            $value = explode($ExamPrefix,$MaxReferenceNumber);
            $MaxReferenceNumber = $ExamPrefix.((int) $value[1] + 1 );
        }else{
            $MaxReferenceNumber = $ExamPrefix.$MaxReferenceNumber;
        }
        return $MaxReferenceNumber;
    }

    /***
     * USE: Get Curriculum Current Year
     */
    public static function GetCurriculumCurrentYear(){
        //$PresentYearCurriculumId = CurriculumYear::where(cn::CURRICULUM_YEAR_YEAR_COL,((int)Carbon::now()->format('Y').'-'.((int)(Carbon::now()->format('y'))+1)))->first();
        $PresentYearCurriculumId = Helper::getGlobalConfiguration('current_curriculum_year') ?? cn::DEFAULT_CURRICULUM_YEAR_ID;
        if(!empty($PresentYearCurriculumId)){
            return CurriculumYear::whereBetween(cn::CURRICULUM_YEAR_ID_COL, [1, $PresentYearCurriculumId])->orderBy(cn::CURRICULUM_YEAR_ID_COL,'DESC')->get();
        }
        return [];
    }

    /***
     * USE : Get From Enter Year to 1 Year Future Year
     */
    public static function GetCurriculumCurrentFutureYear($Date){
        $PresentYearCurriculumId = CurriculumYear::find($Date+1);
        if(!empty($PresentYearCurriculumId)){
            return CurriculumYear::whereBetween(cn::CURRICULUM_YEAR_ID_COL, [1, $PresentYearCurriculumId->id])->orderBy(cn::CURRICULUM_YEAR_ID_COL,'DESC')->get();
        }
        return [];
    }

    /**
     * USE : Get Student detail by Curriculum Year
     */
    public static function GetStudentDataByCurriculumYear($YearId,$UserId){
        $CurriculumYearStudentMappings =    CurriculumYearStudentMappings::where([
                                                cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $YearId,
                                                cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL => $UserId
                                            ])->first();
        return $CurriculumYearStudentMappings ?? [];
    }

    /**
     * USE : Get The Current Date
     */
    public function CurrentDate(){
        return Carbon::now()->toDateString();
    }

    /**
     * USE : Update Curriculum Year in global configurations
     */
    public function UpdateGlobalConfigurationCurriculumYear(){
        $nextCurriculumYear = (((int)Carbon::now()->format('Y')+1).'-'.((int)(Carbon::now()->format('y'))+2));
        $CurriculumYear =   CurriculumYear::where([
                                cn::CURRICULUM_YEAR_YEAR_COL => $nextCurriculumYear,
                                cn::CURRICULUM_YEAR_STATUS_COL => 'active'
                            ])->first();
        GlobalConfiguration::where([cn::GLOBAL_CONFIGURATION_KEY_COL => 'current_curriculum_year'])
        ->Update([
            cn::GLOBAL_CONFIGURATION_VALUE_COL  => $CurriculumYear->{cn::CURRICULUM_YEAR_ID_COL}
        ]);
    }

    /**
     * USE : Get Monday Date List
     */
    public function getMondayDates($year, $month){
        $MondayDateList = array();

        # First weekday in specified month: 1 = monday, 7 = sunday
        $firstDay = date('N', mktime(0, 0, 0, $month, 1, $year));

        /* Add 0 days if monday ... 6 days if tuesday, 1 day if sunday to get the first monday in month */
        $addDays = (8 - $firstDay);
        
        $MondayDateList[] = date('Y-m-d', mktime(0, 0, 0, $month, 1 + $addDays, $year));

        $nextMonth = mktime(0, 0, 0, $month + 1, 1, $year);

        # Just add 7 days per iteration to get the date of the subsequent week
        for ($week = 1, $time = mktime(0, 0, 0, $month, 1 + $addDays + $week * 7, $year);
            $time < $nextMonth;
            ++$week, $time = mktime(0, 0, 0, $month, 1 + $addDays + $week * 7, $year))
        {
            $MondayDateList[] = date('Y-m-d', $time);
        }

        return $MondayDateList;
    }

    /**
     * USE : Get the next curriculum year
     */
    public function GetNextCurriculumYearId(){
        // Find Next Curriculum Year
        $nextCurriculumYear = (((int)Carbon::now()->format('Y')+1).'-'.((int)(Carbon::now()->format('y'))+2));

        // Fins Current Curriculum Year
        $CurrentCurriculumYear = ((int)Carbon::now()->format('Y').'-'.((int)(Carbon::now()->format('y'))+1));

        // Get the Current Curriculum Year Id
        $CurriculumYearData = CurriculumYear::where(cn::CURRICULUM_YEAR_YEAR_COL,$CurrentCurriculumYear)->first();
        if(CurriculumYear::where(cn::CURRICULUM_YEAR_YEAR_COL,$nextCurriculumYear)->doesntExist()){
            $CurriculumYear =   CurriculumYear::Create([
                                    cn::CURRICULUM_YEAR_YEAR_COL => $nextCurriculumYear,
                                    cn::CURRICULUM_YEAR_STATUS_COL => 'active'
                                ]);
        }else{
            $CurriculumYear = CurriculumYear::where([
                                cn::CURRICULUM_YEAR_YEAR_COL => $nextCurriculumYear,
                                cn::CURRICULUM_YEAR_STATUS_COL => 'active'
                            ])->first();
        }
        // Store Next Curriculum Year Id
        $nextCurriculumYearId = $CurriculumYear->{cn::CURRICULUM_YEAR_ID_COL};
        return $nextCurriculumYearId;
    }

    /***
     * USE : GradeList in get From 1 to 6 for common function define
     */
    public function getGradeLists(){
        $GradeList = Grades::where(cn::GRADES_STATUS_COL,1)->whereIn(cn::GRADES_ID_COL,[1,2,3,4,5,6])->get();
        return $GradeList ?? [];
    }

    public function ClassPromotionHistoryCreateOrUpdateRecord($CurriculumYearId,$userData,$gradeId ='',$classId=''){
        if(ClassPromotionHistory::where([
            cn::CLASS_PROMOTION_HISTORY_CURRICULUM_YEAR_ID_COL => $CurriculumYearId,
            cn::CLASS_PROMOTION_HISTORY_SCHOOL_ID_COL          => auth()->user()->{cn::CLASS_PROMOTION_HISTORY_SCHOOL_ID_COL},
            cn::CLASS_PROMOTION_HISTORY_STUDENT_ID_COL         => $userData->id,
            cn::CLASS_PROMOTION_HISTORY_CURRENT_GRADE_ID_COL    =>  $userData->grade_id,
            cn::CLASS_PROMOTION_HISTORY_CURRENT_CLASS_ID_COL    =>  $userData->class_id,
        ])
        ->exists()){
            ClassPromotionHistory::where([
                cn::CLASS_PROMOTION_HISTORY_CURRICULUM_YEAR_ID_COL  => $CurriculumYearId,
                cn::CLASS_PROMOTION_HISTORY_SCHOOL_ID_COL           => auth()->user()->{cn::CLASS_PROMOTION_HISTORY_SCHOOL_ID_COL},
                cn::CLASS_PROMOTION_HISTORY_STUDENT_ID_COL          => $userData->id,
                cn::CLASS_PROMOTION_HISTORY_CURRENT_GRADE_ID_COL    =>  $userData->grade_id,
            cn::CLASS_PROMOTION_HISTORY_CURRENT_CLASS_ID_COL        =>  $userData->class_id
            ])
            ->update([
                cn::CLASS_PROMOTION_HISTORY_CURRENT_GRADE_ID_COL    =>  $userData->grade_id,
                cn::CLASS_PROMOTION_HISTORY_CURRENT_CLASS_ID_COL    =>  $userData->class_id,
                cn::CLASS_PROMOTION_HISTORY_PROMOTED_GRADE_ID_COL   =>  $gradeId,
                cn::CLASS_PROMOTION_HISTORY_PROMOTED_CLASS_ID_COL   =>  $classId,
                cn::CLASS_PROMOTION_HISTORY_PROMOTED_BY_USER_ID_COL =>  Auth::user()->id,
            ]);
        }else{
            ClassPromotionHistory::create([
                cn::CLASS_PROMOTION_HISTORY_CURRICULUM_YEAR_ID_COL => $CurriculumYearId,
                cn::CLASS_PROMOTION_HISTORY_SCHOOL_ID_COL          => auth()->user()->{cn::CLASS_PROMOTION_HISTORY_SCHOOL_ID_COL},
                cn::CLASS_PROMOTION_HISTORY_STUDENT_ID_COL         => $userData->id,
                cn::CLASS_PROMOTION_HISTORY_CURRENT_GRADE_ID_COL    =>  NULL,
                cn::CLASS_PROMOTION_HISTORY_CURRENT_CLASS_ID_COL    =>  NULL,
                cn::CLASS_PROMOTION_HISTORY_PROMOTED_GRADE_ID_COL   =>  $gradeId,
                cn::CLASS_PROMOTION_HISTORY_PROMOTED_CLASS_ID_COL   =>  $classId,
                cn::CLASS_PROMOTION_HISTORY_PROMOTED_BY_USER_ID_COL =>  Auth::user()->id,
            ]);   
        }
    }

    /**
     * USE : Array Flatten Convert in Single Array
     */
    function arrayFlatten(array $array) {
        $flatten = array();
        array_walk_recursive($array, function($value) use(&$flatten) {
            $flatten[] = $value;
        });
        return $flatten;
    }

    /**
     * USE : Check Duplication Curriculum Student Mapping Table
     */
    public function CheckDuplicationRecordCurriculumStudentMapping($ignoreUserData,$usersClassStudentNumber,$curriculum_id){
        $response = array();
        $Query = CurriculumYearStudentMappings::where([
                                                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $curriculum_id,
                                                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL => Auth::user()->school_id,
                                                        cn::CURRICULUM_YEAR_STUDENT_NUMBER_WITHIN_CLASS_COL => $usersClassStudentNumber
                                                    ])
                                                    ->where( cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL,'<>',$ignoreUserData->id);
        if($Query->doesntExist()){
            $response = $Query->get();
            if($response->isNotEmpty()){
                return $response->toArray();
            }
        }
        return $response;
    }

    /**
     * USE : In User Table check Particular column value exists in database (main Used for check (email,permanent reference number,student with in class) when import student)
     */
    public function CheckUserInDataExists($columnName,$value,$school_id = ''){
        $userExists = '';
        if(!empty($school_id)){
            $userExists = User::where($columnName,$value)->where(cn::USERS_SCHOOL_ID_COL,$school_id)->exists();
        }else{
            $userExists = User::where($columnName,$value)->exists();
        }
       return $userExists;
    }

    /* Question is Seed or not*/
    public function isSeedQuestion($questionStructure){
        $result = false;
        $questionNamingStructure = explode('-',$questionStructure);
        if(count($questionNamingStructure)==7){
            $result = true;
            return  $result;
        }
        return $result;
    }

    // Get All role base user of Particular School
    public function getRoleBasedUserForParticularSchool($userRole,$Schools){
        $roleId = (is_array($userRole)) ? $userRole : [$userRole];
        $schoolId = (is_array($Schools)) ? $Schools : [$Schools];

        $UserData = User::whereIn(cn::USERS_ROLE_ID_COL,$roleId)
                            ->whereIn(cn::USERS_SCHOOL_ID_COL,$schoolId)
                            ->withTrashed()
                            ->pluck(cn::USERS_ID_COL)
                            ->toArray();
        return $UserData;
    }

    /**
     * USE : Array String To Float Convert
     */
    public function StringArrayToConvertArray($array){
        if(isset($array) && !empty($array)){
            return array_map('floatval',$array);
        }
        return [];
    }

    /**
     * USE: Get Overall Percentile
     */
    public static function GetStudentPercentileRank($examId,$studentId){
        $PercentileRankValue = 0;
        $overAllExamData = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)->get();
        if(isset($overAllExamData) && !empty($overAllExamData)){
            $overAllExamData = $overAllExamData->toArray();
        }
        $OverAllStudentAnswerCorrectInCorrectRank = array_column($overAllExamData,'student_ability'); 
        $overAllNormalizeArray = [];
        foreach($OverAllStudentAnswerCorrectInCorrectRank as $NormalizeAbility){
            array_push($overAllNormalizeArray,Helper::getNormalizedAbility($NormalizeAbility));
        }                
        array_multisort($overAllNormalizeArray, SORT_ASC, $overAllExamData);
        if(isset($overAllNormalizeArray) && !empty($overAllNormalizeArray)){
            $self = new Self();
            $PercentileRankValue = $self->getStudentPercentile(($overAllNormalizeArray[array_search($studentId, array_column($overAllExamData,'student_id'))]),count($overAllExamData),array_unique($overAllNormalizeArray));
        }
        return $PercentileRankValue;
    }

     /**
     * USE Set Activity Log into Test Type
     */
    public function ActivityTestType($examDetail){
        $examTypeText = '';
        if($examDetail->exam_type == 1){
            if(substr($examDetail->reference_no,0,1) == 'S'){
                $examTypeText = __('activity_history.self_learning');
            }else{
                $examTypeText = __('activity_history.ai_based_assessment');
            }
        }elseif($examDetail->exam_type == 2){
            $examTypeText = __('activity_history.exercise');
        }else{
            $examTypeText = __('activity_history.test');
        }
        return $examTypeText;
    }

}