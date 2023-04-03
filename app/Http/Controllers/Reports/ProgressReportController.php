<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Traits\ResponseFormat;
use App\Traits\Common;
use App\Helpers\Helper;
use App\Http\Services\AIApiService;
use App\Models\Exam;
use App\Models\User;
use App\Models\GradeClassMapping;
use App\Models\Grades;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\Question;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\AttemptExams;
use App\Models\TeachersClassSubjectAssign;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\GradeSchoolMappings;
use App\Constants\DbConstant As cn;
use Illuminate\Support\Facades\DB;
use App\Models\LearningUnitOrdering;
use App\Models\LearningObjectiveOrdering;
use App\Models\LearningUnitsProgressReport;
use App\Models\LearningObjectivesProgressReport;
use App\Events\UserActivityLog;

class ProgressReportController extends Controller
{
    use Common, ResponseFormat;

    public function __construct(){
        $this->AIApiService = new AIApiService();
    }

    /**
     * USE : Learning progress learning units
     */
    public function LearningProgressLearningUnits(Request $request){
        try{
            ini_set('max_execution_time', -1);
            if(isset($request->isFilter)){
                $isFilter = true;
            }else{
                $isFilter = false;
            }

            // Get Color Codes Array
            $ColorCodes = Helper::GetColorCodes();
            $progressReportArray = array();
            $grade_id = array();
            $GradeClassListData = array();
            $class_type_id = array();
            $GradesList = array();
            //$teachersClassList = array();
            //$currentLang = ucwords(app()->getLocale());
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
            $reportLearningType = "";
            if(isset($request->reportLearningType) && !empty($request->reportLearningType)){
                $reportLearningType = $request->reportLearningType;
            }

            if(isset($request->grade_id) && !empty($request->grade_id)){
                $grade_id = $request->grade_id;
            }

            // Get pre-configured data for the questions
            $PreConfigurationDifficultyLevel = array();
            $PreConfigurationDifficultyLevelData = PreConfigurationDiffiltyLevel::get()->toArray();
            if(isset($PreConfigurationDifficultyLevelData)){
                $PreConfigurationDifficultyLevel = array_column($PreConfigurationDifficultyLevelData,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
            }
            $principalGradesList =  GradeSchoolMappings::where([
                                        cn::GRADES_MAPPING_SCHOOL_ID_COL => $schoolId,
                                        cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                    ])->get();
            $gradeArray = array();
            $classArray = array();
            foreach($principalGradesList as $grades){
                // Store teacher grades into array
                $gradeData = Grades::find($grades['grade_id']);
                if(isset($request->grade_id) && !empty($request->grade_id)){
                    $gradeArray = $request->grade_id;
                }else{
                    $gradeArray[] = $gradeData->id;
                }
                $GradesList[] = array(
                                    'id' => $gradeData->id,
                                    'name' => $gradeData->name
                                );
            }

            $principalGradesFirst = $principalGradesList[0];
            if(isset($request->grade_id) && !empty($request->grade_id)){
                $teacherClassSubjectAssignNew = GradeSchoolMappings::where([
                                                    cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                    cn::GRADES_MAPPING_SCHOOL_ID_COL => $schoolId,
                                                    cn::GRADES_MAPPING_GRADE_ID_COL => $request->grade_id
                                                ])->get();
                if(isset($teacherClassSubjectAssignNew[0]) && !empty($teacherClassSubjectAssignNew[0])){
                    $principalGradesFirst = $teacherClassSubjectAssignNew[0];
                }
            }

            if(!empty($principalGradesFirst['grade_id'])){
                $gradeData = Grades::find($principalGradesFirst['grade_id']);
                if(!empty($gradeData)){
                    $GradeClassData = GradeClassMapping::where([
                                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $principalGradesFirst['grade_id'],
                                        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId
                                    ])->get();
                    foreach($GradeClassData as $gradeClass){
                        if(isset($request->class_type_id) && !empty($request->class_type_id)){
                            $classArray = $request->class_type_id;
                        }else{
                            $classArray[] = $gradeClass->id;
                        }
                        $ClassList[] = array(
                            'class_id' => $gradeClass->id,
                            'class_name' => $gradeData->name.$gradeClass->name
                        );
                    }
                }
            }

            // Get Strands data
            if(isset($gradeArray)){
                $gradeid = $gradeArray[0];
            }
            if(isset($classArray)){
                $classid = $classArray[0];
            }
            $StrandList = Strands::all();

            if(!empty($StrandList)){
                $strandId = $StrandList->pluck('id')->toArray();
            }
            
            $LearningUnitsList = $this->GetLearningUnits($strandId);

            $StrandsLearningUnitsList = Strands::with('LearningUnit')->get()->toArray();
            
            $strandDataLbl = Strands::pluck('name_'.app()->getLocale(),cn::STRANDS_ID_COL)->toArray();
            if(!empty($StrandList)){
                $gradeid = $gradeArray[0];
                $gradeData = Grades::find($gradeid);
                $GradeClassData =   GradeClassMapping::where([
                                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $gradeid,
                                        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId
                                    ])
                                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$classArray)
                                    ->get();
                $gradeClass = $GradeClassData[0];
                
                $studentList =  User::where([
                                    cn::USERS_SCHOOL_ID_COL => $schoolId,
                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                                ])
                                ->get()
                                ->where('CurriculumYearGradeId',$gradeid)
                                ->where('CurriculumYearClassId',$gradeClass->id);
                $StudentIds = $studentList->pluck('id');
                
                $ProgressData = array();
                foreach($studentList as $student){
                    $StudentId = $student->id;
                    $progressReportArray[$StudentId]['student_data'][] = $student->toArray();
                    $LearningUnitsProgressReport = LearningUnitsProgressReport::where('student_id',$StudentId)->first();
                    if(isset($reportLearningType) && $reportLearningType == 1){
                        $ProgressData = (isset($LearningUnitsProgressReport->learning_progress_testing_zone)) ? json_decode($LearningUnitsProgressReport->learning_progress_testing_zone,TRUE) : [];
                    }
                    if(isset($reportLearningType) && $reportLearningType == 3){
                        $ProgressData = (isset($LearningUnitsProgressReport->learning_progress_test)) ? json_decode($LearningUnitsProgressReport->learning_progress_test,TRUE) : [];
                    }
                    if(empty($reportLearningType)){
                        $ProgressData = (isset($LearningUnitsProgressReport->learning_progress_all)) ? json_decode($LearningUnitsProgressReport->learning_progress_all,TRUE) : [];
                    }
                    foreach($StrandList as $strand){
                        $strandId = $strand->id;
                        $LearningUnitsLists = collect($this->GetLearningUnits($strandId));
                        $learningUnitsIds = $LearningUnitsLists->pluck('id');
                        if(isset($LearningUnitsList) && !empty($LearningUnitsList)){
                            foreach($learningUnitsIds as $learningUnitsId){
                                if(isset($ProgressData) && !empty($ProgressData)){
                                    $progressReportArray[$StudentId]['report_data'][] = $ProgressData[$strandId][$learningUnitsId];
                                }else{
                                    $progressReportArray[$StudentId]['report_data'][] = array();
                                }
                            }
                        }
                    }
                }
            }

            //echo '<pre>';print_r($LearningUnitsList);die;
            return view('backend.reports.learning_progress.learning_unit_report',compact('StrandList','LearningUnitsList','StrandsLearningUnitsList','GradesList',
            'grade_id','ClassList','reportLearningType','progressReportArray','gradeid','classid','ColorCodes'));
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Learning progress learning objectives
     */
    public function LearningProgressLearningObjectives(Request $request){
        ini_set('max_execution_time', -1);
        if(isset($request->isFilter)){
            $isFilter = true;
        }else{
            $isFilter = false;
        }

        // Get Color Codes Array
        $ColorCodes = Helper::GetColorCodes();

        $progressReportArray = array();
        $LearningsUnitsLbl = array();
        $grade_id = array();
        $GradeClassListData = array();
        $class_type_id = array();
        $GradesList = array();
        $teachersClassList = array();
        $currentLang = ucwords(app()->getLocale());
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
        $reportLearningType = "";
        $teacherListIds =   User::where([
                                cn::USERS_ROLE_ID_COL => cn::TEACHER_ROLE_ID,
                                cn::USERS_SCHOOL_ID_COL => $schoolId
                            ])
                            ->get()
                            ->pluck(cn::USERS_ID_COL);
        if(isset($request->reportLearningType) && !empty($request->reportLearningType)){
            $reportLearningType = $request->reportLearningType;
        }

        if(isset($request->grade_id) && !empty($request->grade_id)){
            $grade_id = $request->grade_id;
        }

        $teacherClassSubjectAssign = GradeSchoolMappings::where([
                                        cn::GRADES_MAPPING_SCHOOL_ID_COL => $schoolId,
                                        cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                    ])
                                    ->get();
        $gradeArray = array();
        $classArray = array();
        foreach($teacherClassSubjectAssign as $teacherGrades){
            // Store teacher grades into array
            $gradeData = Grades::find($teacherGrades['grade_id']);
            if(isset($request->grade_id) && !empty($request->grade_id)){
                $gradeArray = $request->grade_id;
            }else{
                $gradeArray[] = $gradeData->id;
            }
            $GradesList[] = array(
                                'id' => $gradeData->id,
                                'name' => $gradeData->name
                            );
        }
        $teacherGradesFirst = $teacherClassSubjectAssign[0];
        if(isset($request->grade_id) && !empty($request->grade_id)){
            $teacherClassSubjectAssignNew = GradeSchoolMappings::where([
                                                    cn::GRADES_MAPPING_SCHOOL_ID_COL => $schoolId,
                                                    cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                                ])
                                            ->where(cn::GRADES_MAPPING_GRADE_ID_COL,$request->grade_id)->get();
            if(isset($teacherClassSubjectAssignNew[0]) && !empty($teacherClassSubjectAssignNew[0])){
                $teacherGradesFirst = $teacherClassSubjectAssignNew[0];
            }
        }
        if(!empty($teacherGradesFirst['grade_id'])){
            $gradeData = Grades::find($teacherGradesFirst['grade_id']);
            if(!empty($gradeData)){
                $GradeClassData =   GradeClassMapping::where([
                                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $teacherGradesFirst['grade_id'],
                                        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId
                                    ])
                                    ->get();

                foreach($GradeClassData as $gradeClasss){
                    if(isset($request->class_type_id) && !empty($request->class_type_id)){
                        $classArray = $request->class_type_id;
                    }else{
                        $classArray[] = $gradeClasss->id;
                    }
                    $teachersClassList[] = array(
                        'class_id' => $gradeClasss->id,
                        'class_name' => $gradeData->name.$gradeClasss->name
                    );
                }
            }
        }

        if(isset($request->learningReportStrand)){
            $strandData = Strands::all();
            $strand = Strands::whereIn(cn::STRANDS_ID_COL,$request->learningReportStrand)->first();
            $strandDataLbl = Strands::whereIn(cn::STRANDS_ID_COL,$request->learningReportStrand)->pluck('name_'.app()->getLocale(),cn::STRANDS_ID_COL)->toArray();
        }else{
            $strandData = Strands::all();
            $strand = Strands::first();
            $strandDataLbl = Strands::pluck('name_'.app()->getLocale(),cn::STRANDS_ID_COL)->toArray();
        }
        $LearningsUnitsLbl = LearningsUnits::where('stage_id','<>',3)->pluck('name_'.app()->getLocale(),cn::LEARNING_UNITS_ID_COL)->toArray();
        $strandId = $strand->id;
        $LearningUnits = $this->GetLearningUnits($strandId);
        $learningUnitsIds = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->id)->where('stage_id','<>',3)->pluck(cn::LEARNING_UNITS_ID_COL)->toArray();
        $LearningObjectivesData = collect($this->GetLearningObjectives($learningUnitsIds));
        $learningObjectivesList = $LearningObjectivesData->where(cn::LEARNING_OBJECTIVES_IS_AVAILABLE_QUESTIONS_COL,'yes');
        if(isset($request->learning_unit_id) && !empty($request->learning_unit_id)){
            $learningUnitsIds = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->id)
                                ->where('stage_id','<>',3)
                                ->where(cn::LEARNING_UNITS_ID_COL, $request->learning_unit_id)
                                ->pluck(cn::LEARNING_UNITS_ID_COL)->toArray();
            $learningObjectivesList = $this->GetLearningObjectives($learningUnitsIds);             
        }
        if(isset($gradeArray)){
            $gradeid = $gradeArray[0];
        }
        if(isset($classArray)){
            $classid = $classArray[0];
        }

        if($isFilter){
            if(!empty($learningUnitsIds)){
                $learningUnitsId = $learningUnitsIds[0];
                $learningObjectivesList = collect($this->GetLearningObjectives($learningUnitsId));
                $learningObjectivesIds = $learningObjectivesList->pluck(cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                $LearningsObjectivesLbl = $learningObjectivesList->pluck('title_'.app()->getLocale(),cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                if(!empty($learningObjectivesIds)){
                    if(isset($gradeArray)){
                        $gradeid = $gradeArray[0];
                        $gradeData = Grades::find($gradeid);
                        $GradeClassData =   GradeClassMapping::where([
                                                cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $gradeid,
                                                cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId
                                            ])
                                            ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$classArray)
                                            ->get();
                        if(isset($GradeClassData) && !empty($GradeClassData)){
                            $gradeClasss = $GradeClassData[0];
                            $studentList =  User::where([
                                                cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                                cn::USERS_SCHOOL_ID_COL => $schoolId
                                            ])
                                            ->get()
                                            ->where('CurriculumYearGradeId',$gradeid)
                                            ->where('CurriculumYearClassId',$gradeClasss->id);
                            if(isset($studentList) && !empty($studentList)){
                                foreach($studentList as $student){
                                    $StudentId = $student->id;
                                    $ProgressData = array();
                                    // Learning objectives progress data
                                    $LearningObjectivesProgressReport = LearningObjectivesProgressReport::where('student_id',$StudentId)->get();
                                    if(isset($reportLearningType) && $reportLearningType == 1){
                                        $ProgressData = (isset($LearningObjectivesProgressReport->learning_progress_testing_zone)) ? json_decode($LearningObjectivesProgressReport->learning_progress_testing_zone,TRUE) : [];
                                    }
                                    if(isset($reportLearningType) && $reportLearningType == 3){
                                        $ProgressData = (isset($LearningObjectivesProgressReport->learning_progress_test)) ? json_decode($LearningObjectivesProgressReport->learning_progress_test,TRUE) : [];
                                    }
                                    if(empty($reportLearningType)){
                                        $ProgressData = (isset($LearningObjectivesProgressReport->learning_progress_all)) ? json_decode($LearningObjectivesProgressReport->learning_progress_all,TRUE) : [];
                                    }

                                    $progressReportArray[$strandId][$learningUnitsId][$StudentId]['student_data'][] = $student->toArray();
                                    foreach($learningObjectivesIds as $learningObjectivesId){
                                        if(isset($ProgressData) && !empty($ProgressData)){
                                            $progressReportArray[$strandId][$learningUnitsId][$StudentId]['report_data'][] = $ProgressData[$strandId][$learningUnitsId][$learningObjectivesId];
                                        }else{
                                            $progressReportArray[$strandId][$learningUnitsId][$StudentId]['report_data'][] = array();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return view('backend.reports.learning_progress.learning_objective_report',compact('strandData','GradesList','grade_id','teachersClassList','reportLearningType','progressReportArray','strandDataLbl','LearningsUnitsLbl','learningObjectivesList','LearningUnits','gradeid','classid','schoolId','roleId','ColorCodes'));
    }

    /**
     * USE : Progress report learning objectives display into student panel
     */
    public function StudentProgressReportLearningObjective(Request $request, $studentId=0){
        try{
            if($studentId==0){
                $studentData = User::find(Auth::user()->id);
            }else{
                $studentData = User::find($studentId);
            }

            ini_set('max_execution_time', -1);
            $showMenu = false;
            if(isset($request->showMenu) && $request->showMenu==true){
                $showMenu = true;
            }

            if(isset($request->isFilter)){
                $isFilter = true;
            }else{
                $isFilter = false;
            }

            // Get Color Codes Array
            $ColorCodes = Helper::GetColorCodes();
            $strandData = Strands::all();
            $strandDataLbl = $strandData->pluck('name_'.app()->getLocale(),cn::STRANDS_ID_COL)->toArray();
            $learningReportStrand = $strandData->pluck(cn::STRANDS_ID_COL)->toArray();

            $reportDataArray = array();
            $progressReportArray = array();
            $reportDataAbilityArray = array();
            $LearningsUnitsLbl = array();
            $LearningsObjectivesLbl = array();
            $reportLearningType = "";
            
            if(isset($request->learningReportStrand) && !empty($request->learningReportStrand)){
                $learningReportStrand = $request->learningReportStrand;
                $strandData = Strands::all();
                $strand = $strandData->whereIn(cn::STRANDS_ID_COL,$request->learningReportStrand)->first();
            }else{
                $strandData = Strands::all();
                $strand = $strandData->first();
                $strandDataLbl = $strandData->pluck('name_'.app()->getLocale(),cn::STRANDS_ID_COL)->toArray();
            }
            if(isset($request->reportLearningType) && !empty($request->reportLearningType)){
                $reportLearningType = $request->reportLearningType;
            }

            $strandId = $strand->id;
            $LearningUnits = collect($this->GetLearningUnits($strandId));
            $learningUnitsIds = $LearningUnits->pluck('id')->toArray();
            $LearningsUnitsLbl = $LearningUnits->where('stage_id','<>',3)->pluck('name_'.app()->getLocale(),cn::LEARNING_UNITS_ID_COL)->toArray();
            $learningObjectivesList = $this->GetLearningObjectives($LearningUnits->pluck('id')->toArray());
            if(isset($request->learning_unit_id) && !empty($request->learning_unit_id)){
                $learningUnitsIds = $LearningUnits->where(cn::LEARNING_UNITS_STRANDID_COL, $strandId)
                                    ->where('id',$request->learning_unit_id)
                                    ->where('stage_id','<>',3)
                                    ->pluck(cn::LEARNING_UNITS_ID_COL)
                                    ->toArray();
                $learningObjectivesList = $this->GetLearningObjectives($LearningUnits->where('id',$request->learning_unit_id)->pluck('id')->toArray());
            }
            if($isFilter){
                if(!empty($learningUnitsIds)){
                    $learningUnitsId = $learningUnitsIds[0];
                    $learningObjectivesData = collect($this->GetLearningObjectives($request->learning_unit_id));
                    $learningObjectivesIds = $learningObjectivesData->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$request->learning_unit_id)->pluck('id')->toArray();
                    $LearningsObjectivesLbl =   LearningsObjectives::IsAvailableQuestion()->where('stage_id','<>',3)
                                                ->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)
                                                ->pluck('title_'.app()->getLocale(),cn::LEARNING_OBJECTIVES_ID_COL)
                                                ->toArray();
                    if(!empty($learningObjectivesIds)){
                        $StudentId = $studentData->id;
                        $ProgressData = array();
                        // Learning objectives progress data
                        $LearningObjectivesProgressReport = LearningObjectivesProgressReport::where('student_id',$StudentId)->get();
                        if(isset($reportLearningType) && $reportLearningType == 1){
                            $ProgressData = (isset($LearningObjectivesProgressReport->learning_progress_testing_zone)) ? json_decode($LearningObjectivesProgressReport->learning_progress_testing_zone,TRUE) : [];
                        }
                        if(isset($reportLearningType) && $reportLearningType == 3){
                            $ProgressData = (isset($LearningObjectivesProgressReport->learning_progress_test)) ? json_decode($LearningObjectivesProgressReport->learning_progress_test,TRUE) : [];
                        }
                        if(empty($reportLearningType)){
                            $ProgressData = (isset($LearningObjectivesProgressReport->learning_progress_all)) ? json_decode($LearningObjectivesProgressReport->learning_progress_all,TRUE) : [];
                        }
                        $progressReportArray[$strandId][$learningUnitsId]['student_data'] = $studentData->toArray();
                        foreach($learningObjectivesIds as $learningObjectivesId){
                            if(isset($ProgressData) && !empty($ProgressData)){
                                $progressReportArray[$strandId][$learningUnitsId]['report_data'][$learningObjectivesId] = $ProgressData[$strandId][$learningUnitsId][$learningObjectivesId];
                            }else{
                                $progressReportArray[$strandId][$learningUnitsId]['report_data'][$learningObjectivesId] = array();
                            }
                        }
                    }
                }
            }
            return view('backend.reports.learning_progress.student.learning_objective_report',compact('studentData','strandData','strandDataLbl',
            'progressReportArray','LearningsUnitsLbl','LearningsObjectivesLbl','reportDataAbilityArray','learningObjectivesList','LearningUnits',
            'showMenu','ColorCodes','studentId'));
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }
    
    /**
     * USE : Progress report learning unit display into student panel
     */
    public function StudentProgressReportLearningUnits(Request $request,$studentId=0){
        try{
            if($studentId==0){
                $studentData = User::find(Auth::user()->id);
            }else{
                $studentData = User::find($studentId);
            }
            
            ini_set('max_execution_time', -1);
            if(isset($request->isFilter)){
                $isFilter = true;
            }else{
                $isFilter = false;
            }

            // Get Color Codes Array
            $ColorCodes = Helper::GetColorCodes();

            $progressReportArray = array();
            $LearningsUnitsLbl = array();
            $grade_id = array();
            $GradeClassListData = array();
            $class_type_id = array();
            $GradesList = array();
            $schoolId = $studentData->{cn::USERS_SCHOOL_ID_COL};
            $roleId = $studentData->{cn::USERS_ROLE_ID_COL};
            $reportLearningType = "";

            if(isset($request->reportLearningType) && !empty($request->reportLearningType)){
                $reportLearningType = $request->reportLearningType;
            }

            if(isset($request->grade_id) && !empty($request->grade_id)){
                $grade_id = $request->grade_id;
            }

            // Get pre-configured data for the questions
            $PreConfigurationDifficultyLevel = array();
            $PreConfigurationDiffiltyLevelData = PreConfigurationDiffiltyLevel::get()->toArray();
            if(isset($PreConfigurationDiffiltyLevelData)){
                $PreConfigurationDifficultyLevel = array_column($PreConfigurationDiffiltyLevelData,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
            }
            
            $gradeArray = array();
            $classArray = array();
            $gradeData = Grades::find($studentData->CurriculumYearGradeId);
            
            $GradesList[] = array('id' => $gradeData->id,'name' => $gradeData->name);

            // Get Strands data
            $StrandList = Strands::all();
            if(!empty($StrandList)){
                $strandId = $StrandList->pluck('id')->toArray();
            }
            $LearningUnitsList = $this->GetLearningUnits($strandId);
            
            $ProgressData = array();
            $StudentId = $studentData->id;
            $progressReportArray[$StudentId]['student_data'][] = $studentData->toArray();
            $LearningUnitsProgressReport = LearningUnitsProgressReport::where('student_id',$StudentId)->first();
            if(isset($reportLearningType) && $reportLearningType == 1){
                $ProgressData = (isset($LearningUnitsProgressReport->learning_progress_testing_zone)) ? json_decode($LearningUnitsProgressReport->learning_progress_testing_zone,TRUE) : [];
            }
            if(isset($reportLearningType) && $reportLearningType == 3){
                $ProgressData = (isset($LearningUnitsProgressReport->learning_progress_test)) ? json_decode($LearningUnitsProgressReport->learning_progress_test,TRUE) : [];
            }
            if(empty($reportLearningType)){
                $ProgressData = (isset($LearningUnitsProgressReport->learning_progress_all)) ? json_decode($LearningUnitsProgressReport->learning_progress_all,TRUE) : [];
            }
            foreach($StrandList as $strand){
                $strandId = $strand->id;
                $LearningUnitsLists = collect($this->GetLearningUnits($strandId));
                $learningUnitsIds = $LearningUnitsLists->pluck('id');
                if(isset($LearningUnitsList) && !empty($LearningUnitsList)){
                    foreach($learningUnitsIds as $learningUnitsId){
                        if(isset($ProgressData) && !empty($ProgressData)){
                            $progressReportArray[$StudentId]['report_data'][] = $ProgressData[$strandId][$learningUnitsId];
                        }else{
                            $progressReportArray[$StudentId]['report_data'][] = array();
                        }
                    }
                }
            }
            return view('backend.reports.learning_progress.student.learning_unit_progress',compact('StrandList','LearningUnitsList','GradesList','grade_id','reportLearningType','progressReportArray','ColorCodes','studentId'));
        }catch(\Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }
}