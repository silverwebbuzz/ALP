<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Events\UserActivityLog;
use App\Http\Services\TeacherGradesClassService;
use App\Models\PeerGroup;
use Carbon\Carbon;
use App\Traits\Common;
use App\Constants\DbConstant As cn;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\TeachersClassSubjectAssign;
use App\Models\GradeSchoolMappings;
use App\Models\MyTeachingReport;
use App\Models\GradeClassMapping;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\Grades;
use App\Models\Strands;
use App\Models\LearningUnitsProgressReport;
use App\Http\Services\StudentService;
use App\Models\Exam;

class DashboardController extends Controller
{
    use Common;
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('preventBackHistory');
        $this->TeacherGradesClassService = new TeacherGradesClassService;
        $this->StudentService = new StudentService;
    }

    /**
     * USE : Panel Head Dashboard
     */
    public function PanelHead(){
        return view('backend.dashboard.panel_head');
    }

    /**
     * USE : CoOrdinator dashboard
     */
    public function CoOrdinator (){
        return view('backend.dashboard.co_ordinator');
    }

    public function Dashboard(){
        if(Auth::user()->role_id != 3 && Auth::user()->role_id != 1){
            $UsersCount = $this->getAllUsersCount($this->GetCurriculumYear());
            $ExamsCount = $this->getAllExamsCount($this->GetCurriculumYear());
            $gradesCount = $this->getAllGradesCount($this->GetCurriculumYear());
            $classesCount = $this->getAllClassesCount($this->GetCurriculumYear());
            $groupCount = $this->getAllGroupCount($this->GetCurriculumYear());
            $peerGroupCount = $this->getAllPeerGroupCount($this->GetCurriculumYear());

            $userId = Auth::id();
            $items = $request->items ?? 10;
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
            $gradeId = array();
            $GradeClassListData = array();
            $Query = '';
            $filterPeerGroupIds = [];
            $classTypeId = array();
            $difficultyLevels = PreConfigurationDiffiltyLevel::get();
            $peerGroupData = PeerGroup::where(['school_id'=> Auth::user()->school_id,'status'=>1])->get();
            
            if($this->isTeacherLogin()){
                $gradesList =   TeachersClassSubjectAssign::where([
                                    cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth::user()->{cn::USERS_ID_COL},
                                    cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                ])
                                ->with('getClass')
                                ->get()
                                ->unique(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
                $gradesListId = TeachersClassSubjectAssign::where([
                                    cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth::user()->{cn::USERS_ID_COL},
                                    cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                ])
                                ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL,cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                                ->toArray();
                $gradesListIdArr =  TeachersClassSubjectAssign::where([
                                        cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth::user()->{cn::USERS_ID_COL},
                                        cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                    ])
                                    ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                                    ->toArray();
                $TeacherClass = TeachersClassSubjectAssign::where([
                                    cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth::user()->{cn::USERS_ID_COL},
                                    cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                ])
                                ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)
                                ->toArray();
                $TeacherAssignedClassIds = [];
                $TeacherAssignedClass = [];
                if(!empty($TeacherClass)){
                    foreach($TeacherClass as $teacherClass){
                        $TeacherAssignedClass[] = explode(',',$teacherClass);
                    }
                }
                $TeacherAssignedClass =$this->array_flatten($TeacherAssignedClass);
                
                // Find Teacher Peer Group Ids
                $PeerGroupIds = [];
                $PeerGroupIds = $this->TeacherGradesClassService->GetTeachersPeerGroupIds(Auth::user()->{cn::USERS_ID_COL}, Auth::user()->{cn::USERS_SCHOOL_ID_COL});
            }
            if($this->isPrincipalLogin() || $this->isCoOrdinatorLogin() || $this->isPanelHeadLogin() ){
                $gradesList =   GradeSchoolMappings::where([
                                    cn::GRADES_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                    cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                ])
                                ->with('grades')
                                ->get();
                $TeacherAssignedClass = GradeClassMapping::where([
                                    cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL             => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                    cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL    => $this->GetCurriculumYear()
                                ])
                                ->get()
                                ->pluck(cn::GRADE_CLASS_MAPPING_ID_COL);
                if(!empty($TeacherAssignedClass)){
                    $TeacherAssignedClass = $TeacherAssignedClass->toArray();
                }

                if(!empty($gradesList)){
                    $gradesListIdArr = $gradesList->pluck(cn::GRADES_MAPPING_GRADE_ID_COL)->toArray();
                }

                // Find Teacher Peer Group Ids
                $PeerGroupIds = [];
                $PeerGroupIds = $this->TeacherGradesClassService->GetSchoolBasedPeerGroupIds(Auth::user()->{cn::USERS_SCHOOL_ID_COL});
            }

            $AssignmentExerciseList =   MyTeachingReport::where(function($query) use($gradesListIdArr, $TeacherAssignedClass,$PeerGroupIds){
                                            $query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL,$gradesListIdArr)
                                            ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$TeacherAssignedClass)
                                            ->orWhereIn(cn::TEACHING_REPORT_PEER_GROUP_ID,$PeerGroupIds);
                                        })
                                        ->where([
                                            cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::TEACHING_REPORT_REPORT_TYPE_COL => 'assignment_test',
                                            cn::TEACHING_REPORT_STUDY_TYPE_COL => 1,
                                            cn::TEACHING_REPORT_SCHOOL_ID_COL => $schoolId
                                        ])
                                        ->with(['exams','peerGroup'])
                                        ->whereHas('exams',function($q){
                                            $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear())
                                            ->where(cn::EXAM_TABLE_STATUS_COLS,'<>','inactive');
                                        })
                                        ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')
                                        ->take(5)
                                        ->get();
            $AssignmentTestList =   MyTeachingReport::where(function($query) use($gradesListIdArr, $TeacherAssignedClass, $PeerGroupIds){
                                        $query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradesListIdArr)
                                            ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$TeacherAssignedClass)
                                            ->orWhereIn(cn::TEACHING_REPORT_PEER_GROUP_ID,$PeerGroupIds);
                                    })
                                    ->where([
                                        cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::TEACHING_REPORT_REPORT_TYPE_COL => 'assignment_test',
                                        cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  2,
                                        cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                                    ])
                                    ->with('exams','peerGroup')
                                    ->whereHas('exams',function($q){
                                        $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear())
                                        ->where(cn::EXAM_TABLE_STATUS_COLS,'<>','inactive');
                                    })
                                    ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->take(5)->get();
            return view('backend.common_dashboard',compact('peerGroupCount','AssignmentExerciseList','AssignmentTestList','UsersCount','ExamsCount','gradesCount','classesCount','groupCount'));
        }

        if(Auth::user()->role_id == 3){
            ini_set('max_execution_time', -1);
            $studentData = User::find(Auth::user()->{cn::USERS_ID_COL});
            $progressReportArray = array();
            $LearningsUnitsLbl = array();
            $grade_id = array();
            $GradeClassListData = array();
            $class_type_id = array();
            $GradesList = array();
            $schoolId = $studentData->{cn::USERS_SCHOOL_ID_COL};
            $roleId = $studentData->{cn::USERS_ROLE_ID_COL};
            $reportLearningType = "";
            $ColorCodes = Helper::GetColorCodes();

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

            $StrandsLearningUnitsList = Strands::with('LearningUnit')->get()->toArray();
            $strandDataLbl = Strands::pluck('name_'.app()->getLocale(),cn::STRANDS_ID_COL)->toArray();

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
                            $progressReportArray[$StudentId]['learning_unit_report_data'][] = $ProgressData[$strandId][$learningUnitsId];
                        }else{
                            $progressReportArray[$StudentId]['learning_unit_report_data'][] = array();
                        }
                    }
                }
            }
            /*For Learning Unit Progress Report*/


            $userId = Auth::id();
            
            $ExamList = array();
            $active_tab = "";
            $difficultyLevels = PreConfigurationDiffiltyLevel::get();
            if(isset($request->active_tab) && !empty($request->active_tab)){
                $active_tab = $request->active_tab;
            }
            $data = array();
            // Get Student assigned exam ids
            $GetStudentAssignedExamsIds = [];
            $GetStudentAssignedExamsIds = $this->StudentService->GetStudentAssignedExamsIds(Auth::user()->{cn::USERS_ID_COL});
            // Get Exercise Exams List
            $data['exerciseExam'] = Exam::with(['attempt_exams' => fn($query) => $query->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, Auth::user()->{cn::USERS_ID_COL})])
                                    ->with(['ExamGradeClassConfigurations' => function($q){
                                        $q->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                                        ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                        ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,Auth::user()->CurriculumYearGradeId)
                                        ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,Auth::user()->CurriculumYearClassId)
                                        ->orWhereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL,$this->getStudentPeerGroupIds());
                                    }])
                                    ->with('examSchoolGradeClass', function($q) use($userId){
                                        $q->whereRaw("find_in_set($userId,student_ids)");
                                    })
                                    ->whereRaw("find_in_set($userId,student_ids)")
                                    ->where(cn::EXAM_TYPE_COLS,2)
                                    ->where(cn::EXAM_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                    ->where(cn::EXAM_TABLE_IS_GROUP_TEST_COL,0)
                                    ->whereIn(cn::EXAM_TABLE_ID_COLS,$GetStudentAssignedExamsIds)
                                    ->where(cn::EXAM_TABLE_STATUS_COLS,'publish')
                                    ->orderBy(cn::EXAM_TABLE_CREATED_AT,'DESC')
                                    ->take(5)
                                    ->get();
            $data['testExam'] = Exam::with(['attempt_exams' => fn($query) => $query->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, Auth::user()->{cn::USERS_ID_COL})])
                                ->with(['ExamGradeClassConfigurations' => function($q){
                                    $q->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                                    ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,Auth::user()->CurriculumYearGradeId)
                                    ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,Auth::user()->CurriculumYearClassId)
                                    ->orWhereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL,$this->getStudentPeerGroupIds());
                                }])
                                ->with('examSchoolGradeClass', function($q) use($userId){
                                    $q->whereRaw("find_in_set($userId,student_ids)");
                                })
                                ->whereRaw("find_in_set($userId,student_ids)")
                                ->where(cn::EXAM_TYPE_COLS,3)
                                ->where(cn::EXAM_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                ->where(cn::EXAM_TABLE_IS_GROUP_TEST_COL,0)
                                ->whereIn(cn::EXAM_TABLE_ID_COLS,$GetStudentAssignedExamsIds)
                                ->where(cn::EXAM_TABLE_STATUS_COLS,'publish')
                                ->orderBy(cn::EXAM_TABLE_CREATED_AT,'DESC')
                                ->take(5)
                                ->get();
            return view('backend.student_dashboard',compact('StrandList','LearningUnitsList','StrandsLearningUnitsList','GradesList',
            'grade_id','reportLearningType','progressReportArray','ColorCodes','studentData','data'));
        }
        
    }

}
