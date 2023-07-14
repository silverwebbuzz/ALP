<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Reports\AlpAiGraphController;
use App\Constants\DbConstant As cn;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use Exception;
use App\Helpers\Helper;
use App\Models\School;
use App\Models\Exam;
use App\Models\GradeClassMapping;
use App\Models\GradeSchoolMappings;
use App\Models\User;
use App\Models\MyTeachingReport;
use App\Models\AttemptExams;
use App\Models\TeachersClassSubjectAssign;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\Grades;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\PeerGroup;
use App\Models\PeerGroupMember;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Log;
use Auth;
use App\Http\Services\TeacherGradesClassService;
use App\Events\UserActivityLog;

class MyTeachingController extends Controller
{
    use Common;

    protected $AlpAiGraphController, $Exam, $GradeSchoolMappings, $GradeClassMapping, $User, $AttemptExams, $MyTeachingReport, $PeerGroup,$TeacherGradesClassService;
    public function __construct(){
        $this->AlpAiGraphController     = new AlpAiGraphController();
        $this->Exam                     = new Exam;
        $this->GradeSchoolMappings      = new GradeSchoolMappings;
        $this->GradeClassMapping        = new GradeClassMapping;
        $this->User                     = new User;
        $this->AttemptExams             = new AttemptExams;
        $this->MyTeachingReport         = new MyTeachingReport;
        $this->PeerGroup                = new PeerGroup;
        $this->TeacherGradesClassService = new TeacherGradesClassService;
    }

    public function getAssignmentTestList(Request $request){
        $userId = Auth::id();
        $items = $request->items ?? 10;
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
        $grade_id = array();
        $class_type_id = array();
        $GradeClassListData = array();
        $Query = '';
        $peerGroupIds = [];
        $filterPeerGroupIds = [];
        $difficultyLevels = PreConfigurationDiffiltyLevel::get();
        $peerGroupData = PeerGroup::where(['school_id'=> Auth::user()->school_id,'status'=>1])->get();

        if($this->isTeacherLogin()){
            $gradesList =   TeachersClassSubjectAssign::where([
                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth::user()->{cn::USERS_ID_COL},
                                cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL =>$this->GetCurriculumYear()
                            ])
                            ->with('getClass')
                            ->get()
                            ->unique(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
            $gradesListId = TeachersClassSubjectAssign::where([
                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth::user()->{cn::USERS_ID_COL},
                                cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL =>$this->GetCurriculumYear()
                            ])
                            ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL,cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                            ->toArray();
            $gradesListIdArr =  TeachersClassSubjectAssign::where([
                                    cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth::user()->{cn::USERS_ID_COL},
                                    cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL =>$this->GetCurriculumYear()
                                ])
                                ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->toArray();
            $TeacherClass = TeachersClassSubjectAssign::where([
                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth::user()->{cn::USERS_ID_COL},
                                cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL =>$this->GetCurriculumYear()
                            ])
                            ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)->toArray();
            $TeacherAssignedClassIds = [];
            $TeacherAssignedClass = [];
            if(!empty($TeacherClass)){
                foreach($TeacherClass as $teacherClass){
                    $TeacherAssignedClass[] = explode(',',$teacherClass);
                }
            }
            $TeacherAssignedClass = $this->array_flatten($TeacherAssignedClass);
            $classListIdArray =  GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradesListIdArr)
                                ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                                ->where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                ->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)
                                ->toArray();

            // Find Teacher Peer Group Ids
            $PeerGroupIds = [];
            $PeerGroupIds = $this->TeacherGradesClassService->GetTeachersPeerGroupIds(Auth::user()->{cn::USERS_ID_COL}, Auth::user()->{cn::USERS_SCHOOL_ID_COL});
        }

        if($this->isPrincipalLogin() || $this->isCoOrdinatorLogin() || $this->isPanelHeadLogin() ){
            $gradesList =   GradeSchoolMappings::where([
                                cn::GRADES_MAPPING_SCHOOL_ID_COL            => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL   => $this->GetCurriculumYear()
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
                                    ->where(cn::EXAM_TABLE_FROM_DATE_COLS, '>=', Carbon::now()->subdays(30))
                                    ->where(cn::EXAM_TABLE_STATUS_COLS,'<>','inactive');
                                })
                                ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);
        
        // For Filtration
        if(isset($request->filter) && !empty($request->filter)){
            $grade_id = $request->grade_id;//For Filtration Selection
            $class_type_id = $request->class_type_id;
            $filterPeerGroupIds = $request->group_filter;
            $filterOnDate = $request->date_filter ?? '';
            $gradeId = ($request->grade_id) ? $request->grade_id : $gradesListIdArr;
            $classTypeId = ($request->class_type_id) ? $request->class_type_id : $TeacherAssignedClass;
            $title = ($request->title) ? $request->title : '';
            $Query =   MyTeachingReport::Select('*')
                                    ->with('exams','peerGroup')
                                    ->whereHas('exams',function($q){
                                        $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear())
                                        ->where(cn::EXAM_TABLE_STATUS_COLS,'<>','inactive');
                                    })
                                    // ->where(function($query) use($gradeId, $classTypeId, $PeerGroupIds){
                                    //     $query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradeId)
                                    //         ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$classTypeId);
                                    // })
                                    ->where([
                                        cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::TEACHING_REPORT_REPORT_TYPE_COL => 'assignment_test',
                                        cn::TEACHING_REPORT_STUDY_TYPE_COL  => 2,
                                        cn::TEACHING_REPORT_SCHOOL_ID_COL   => $schoolId,
                                    ]);
                                    // ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);
            
            if((!empty($gradeId) && !empty($classTypeId)) || !empty($request->group_filter)){
                $Query->where(function($query) use($gradeId, $classTypeId,$request,$PeerGroupIds){
                    if(!empty($request->grade_id) && !empty($request->class_type_id) && !empty($request->group_filter)){
                        $query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradeId)
                        ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$classTypeId)
                        ->orWhereIn(cn::TEACHING_REPORT_PEER_GROUP_ID,$request->group_filter);
                    }elseif(empty($request->grade_id) && empty($request->class_type_id) && !empty($request->group_filter)){
                        $query->WhereIn(cn::TEACHING_REPORT_PEER_GROUP_ID,$request->group_filter);
                    }elseif(empty($request->grade_id) && empty($request->class_type_id) && empty($request->group_filter)){
                        $query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradeId)
                        ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$classTypeId)
                        ->orWhereIn(cn::TEACHING_REPORT_PEER_GROUP_ID,$PeerGroupIds);
                    }else{
                        $query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradeId)
                        ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$classTypeId);
                    }
                });
            }

            //From Date
            // if(isset($request->from_date) && !empty($request->from_date)){
            //     $from_date = $this->DateConvertToYMD($request->from_date);
            //     $Query->whereHas('exams',function($q) use($from_date){
            //         $q->whereRaw(cn::EXAM_TABLE_FROM_DATE_COLS." >= '$from_date'");
            //     });
            // }

            //To Date
            // if(isset($request->to_date) && !empty($request->to_date)){
            //     $to_date = $this->DateConvertToYMD($request->to_date);
            //     $Query->whereHas('exams',function($q) use($to_date){
            //         $q->whereRaw(cn::EXAM_TABLE_TO_DATE_COLS." <= '$to_date'");
            //     });
            // }
            if(!empty($filterOnDate) && isset($filterOnDate)){
                $Query = $this->filterOnDateInReport($Query,$filterOnDate);
            }                      
            if(!empty($title) && isset($title)){
                $Query->whereHas("exams",function($q) use($title){
                    $q->where(cn::EXAM_TABLE_TITLE_COLS,'Like','%'.$title.'%');
                }); 
            }
            
            $AssignmentTestList = $Query->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);
           
            //After filtration selected value selected display.
            $GradeClassListDataArr = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeId)
                                    ->where([
                                        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                    ])
                                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$TeacherAssignedClass)->get()->toArray();
            if(!empty($GradeClassListDataArr)){
                foreach($GradeClassListDataArr as $class){
                    $GradeList = Grades::find($class[cn::GRADE_CLASS_MAPPING_GRADE_ID_COL]);
                    $GradeClassListData[strtoupper($class[cn::GRADE_CLASS_MAPPING_ID_COL])]=$GradeList->name.strtoupper($class[cn::GRADE_CLASS_MAPPING_NAME_COL]);
                }
            }
        }
        return view('backend/MyTeachingReports/assignment_test',compact('filterPeerGroupIds','filterPeerGroupIds','peerGroupIds','peerGroupData','AssignmentTestList','difficultyLevels','items','schoolId','gradesList','grade_id','class_type_id','GradeClassListData'));
    }
    
    /***
     * USE : For Exam ExerciseList Display  
     */
    public function getAssignmentExerciseList(Request $request){
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
                                        ->where(cn::EXAM_TABLE_FROM_DATE_COLS, '>=', Carbon::now()->subdays(30))
                                        ->where(cn::EXAM_TABLE_STATUS_COLS,'<>','inactive');
                                    })
                                    ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')
                                    ->paginate($items);
        // For Filtration
        if(isset($request->filter) && !empty($request->filter)){
            // $gradeId = (!empty($request->grade_id)) ? $request->grade_id : $gradesListIdArr;
            // $classTypeId = ($request->class_type_id) ? $request->class_type_id : $TeacherAssignedClass;
            $gradeId = (!empty($request->grade_id)) ? $request->grade_id : [];
            $classTypeId = ($request->class_type_id) ? $request->class_type_id : [];
            $filterOnDate = $request->date_filter ?? '';
            $filterPeerGroupIds = $request->group_filter;
            $title = $request->title ?? '';
            $Query = MyTeachingReport::with('exams','peerGroup')
                    ->whereHas('exams',function($q){
                        $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear())
                        ->where(cn::EXAM_TABLE_STATUS_COLS,'<>','inactive');
                    })
                    ->Select('*')
                    ->where([
                            cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                            cn::TEACHING_REPORT_REPORT_TYPE_COL => 'assignment_test',
                            cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  1,
                            cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                        ]);
            if((!empty($gradeId) && !empty($classTypeId)) || !empty($request->group_filter)){
                $Query->where(function($query) use($gradeId, $classTypeId,$request){
                    if(!empty($request->grade_id) && !empty($request->class_type_id) && !empty($request->group_filter)){
                        $query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradeId)
                        ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$classTypeId)
                        ->orWhereIn(cn::TEACHING_REPORT_PEER_GROUP_ID,$request->group_filter);
                    }elseif(empty($request->grade_id) && empty($request->class_type_id) && !empty($request->group_filter)){
                        $query->WhereIn(cn::TEACHING_REPORT_PEER_GROUP_ID,$request->group_filter);
                    }else{
                        $query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradeId)
                        ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$classTypeId);
                    }
                });
            }

            //From Date
            if(isset($request->from_date) && !empty($request->from_date)){
                $from_date = $this->DateConvertToYMD($request->from_date);
                $Query->whereHas('exams',function($q) use($from_date){
                    $q->whereRaw(cn::EXAM_TABLE_FROM_DATE_COLS." >= '$from_date'");
                });
            }

            //To Date
            if(isset($request->to_date) && !empty($request->to_date)){
                $to_date = $this->DateConvertToYMD($request->to_date);
                $Query->whereHas('exams',function($q) use($to_date){
                    $q->whereRaw(cn::EXAM_TABLE_TO_DATE_COLS." <= '$to_date'");
                });
            }
            // $Query->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL, $gradeId)
            // ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL,$classTypeId)
            // ->where([
            //     cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
            //     cn::TEACHING_REPORT_REPORT_TYPE_COL => 'assignment_test',
            //     cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  1,
            //     cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
            // ]);

            if(!empty($title) && isset($title)){
                $Query->whereHas('exams',function($q) use($title){
                    $q->where(cn::EXAM_TABLE_TITLE_COLS,'Like','%'.$title.'%');
                });
            }
            if(!empty($filterOnDate) && isset($filterOnDate)){
                $Query = $this->filterOnDateInReport($Query,$filterOnDate);
            }
            $AssignmentExerciseList = $Query->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);

            //After filtration selected value selected display.
            $GradeClassListDataArr = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeId)
                                        ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                                        ->where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                        ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$TeacherAssignedClass)->get()->toArray();
            if(!empty($GradeClassListDataArr)){
                foreach($GradeClassListDataArr as $class){
                    $GradeList = Grades::find($class[cn::GRADE_CLASS_MAPPING_GRADE_ID_COL]);
                    $GradeClassListData[strtoupper($class[cn::GRADE_CLASS_MAPPING_ID_COL])] = $GradeList->name.strtoupper($class[cn::GRADE_CLASS_MAPPING_NAME_COL]);
                }
            }
        }
        return view('backend/MyTeachingReports/assignment_exercise',compact('peerGroupData','AssignmentExerciseList','difficultyLevels','items','schoolId','gradesList','gradeId','classTypeId','GradeClassListData','filterPeerGroupIds'));
    }

    /***
     * USE : For Exam SelfLearning Exercise List Display  
     */
    public function getSelfLearningExerciseList(Request $request){
        $userId = Auth::id();
        $items = $request->items ?? 10;
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $grade_id = array();
        $GradeClassListData = array();
        $Query = '';
        $class_type_id = array();
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
            $TeacherAssignedClass = $this->array_flatten($TeacherAssignedClass);
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
        }

        $SelfLearningTestList = MyTeachingReport::whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradesListIdArr)
                                ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$TeacherAssignedClass)
                                ->where([
                                    cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                    cn::TEACHING_REPORT_REPORT_TYPE_COL => 'self_learning',
                                    cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  1,
                                    cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                                ])
                                ->with('exams','user','attempt_exams')
                                ->whereHas('exams',function($q){
                                    $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear())
                                    ->where(cn::EXAM_TABLE_FROM_DATE_COLS, '>=', Carbon::now()->subdays(30));
                                })
                                ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);
        if(isset($request->filter) && !empty($request->filter)){
            $grade_id = $request->grade_id; //For Filtration Selection
            $class_type_id = $request->class_type_id;
            $gradeId = ($request->grade_id) ? $request->grade_id : $gradesListIdArr;
            $classTypeId = ($request->class_type_id) ? $request->class_type_id : $TeacherAssignedClass;
            $filterOnDate = $request->date_filter ?? '';
            $title = $request->title ?? '';
            // Create filter query object
            // $SelfLearningTestList = MyTeachingReport::with('exams')->Select('*')->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL , $gradeId)
            //                         ->whereHas('exams',function($q){
            //                             $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear());
            //                         })
            //                         ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$classTypeId)
            //                         ->where([
            //                             cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
            //                             cn::TEACHING_REPORT_REPORT_TYPE_COL => 'self_learning',
            //                             cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  1,
            //                             cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
            //                         ])
            //                         ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);


            $Query = MyTeachingReport::with('exams')->Select('*')->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL , $gradeId)
                                    ->whereHas('exams',function($q){
                                        $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear());
                                    })
                                    ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$classTypeId)
                                    ->where([
                                        cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::TEACHING_REPORT_REPORT_TYPE_COL => 'self_learning',
                                        cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  1,
                                        cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                                    ]);
                                    // ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);
            if(!empty($title) && isset($title)){
                $Query->whereHas('exams',function($q) use($title){
                    $q->where(cn::EXAM_TABLE_TITLE_COLS,'Like','%'.$title.'%');
                });
            }
            if(!empty($filterOnDate) && isset($filterOnDate)){
                $Query = $this->filterOnDateInReport($Query,$filterOnDate);
            }
            $SelfLearningTestList = $Query->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);



            //After filtration selected value selected display.
            $GradeClassListDataArr = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeId)
            ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
            ->where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
            ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$TeacherAssignedClass)->get()->toArray();
            if(!empty($GradeClassListDataArr)){
                foreach($GradeClassListDataArr as $class){
                    $GradeList = Grades::find($class[cn::GRADE_CLASS_MAPPING_GRADE_ID_COL]);
                    $GradeClassListData[strtoupper($class[cn::GRADE_CLASS_MAPPING_ID_COL])]=$GradeList->name.strtoupper($class[cn::GRADE_CLASS_MAPPING_NAME_COL]);
                }
            }
        }
        return view('backend/MyTeachingReports/self_learning_exercise',compact('peerGroupData','SelfLearningTestList','difficultyLevels','items','schoolId','gradesList','grade_id','class_type_id','GradeClassListData'));
    }

     /***
     * USE : For Exam SelfLearning Test List Display  
     */
    public function getSelfLearningTestList(Request $request){
        $userId = Auth::id();
        $items = $request->items ?? 10;
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
        $grade_id = array();
        $GradeClassListData = array();
        $Query = '';
        $class_type_id = array();
        $strandsList = array();
        $LearningUnits = array();
        $LearningObjectives = array();
        $difficultyLevels = PreConfigurationDiffiltyLevel::get();

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
                            ])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)
                            ->toArray();
            $TeacherAssignedClassIds = [];
            $TeacherAssignedClass = [];
            if(!empty($TeacherClass)){
                foreach($TeacherClass as $teacherClass){
                    $TeacherAssignedClass[] = explode(',',$teacherClass);
                }
            }
            $TeacherAssignedClass = $this->array_flatten($TeacherAssignedClass);
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
        }

        $SelfLearningTestList = MyTeachingReport::whereIn(cn::TEACHING_REPORT_GRADE_ID_COL ,$gradesListIdArr)
                                ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL ,$TeacherAssignedClass)
                                ->where([
                                    cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                    cn::TEACHING_REPORT_REPORT_TYPE_COL => 'self_learning',
                                    cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  2,
                                    cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                                ])
                                ->with('exams','user','attempt_exams')
                                ->whereHas('exams',function($q){
                                    $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear())
                                    ->where(cn::EXAM_TABLE_FROM_DATE_COLS, '>=', Carbon::now()->subdays(30));
                                })
                                ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')
                                ->paginate($items);

        // Get Current student grade id wise strand list
        $strandsList =  StrandUnitsObjectivesMappings::pluck(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL);
        if($strandsList->isNotEmpty()){
            $strandsIds = array_unique($strandsList->toArray());
            $strandsList = Strands::whereIn(cn::STRANDS_ID_COL, $strandsIds)->get();

            // Get The learning units based on first Strands
            $learningUnitsIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandsList[0]->id)
                                ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL);
            if(!empty($learningUnitsIds)){
                $learningUnitsIds = array_unique($learningUnitsIds->toArray());
                $LearningUnits = LearningsUnits::where('stage_id','<>',3)->whereIn(cn::LEARNING_UNITS_ID_COL, $learningUnitsIds)->get();

                // Get the Learning objectives based on first learning units
                $learningObjectivesIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandsList[0]->id)
                                        ->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$LearningUnits->pluck(cn::LEARNING_UNITS_ID_COL))
                                        ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL);
                if(!empty($learningObjectivesIds)){
                    $learningObjectivesIds = array_unique($learningObjectivesIds->toArray());
                    $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->where('stage_id','<>',3)->whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                }
            }
        }
        
        if(isset($request->filter) && !empty($request->filter)){
            $grade_id = $request->grade_id;//For Filtration Selection
            $class_type_id = $request->class_type_id;
            $gradeId = ($request->grade_id) ? $request->grade_id : $gradesListIdArr;
            $classTypeId = ($request->class_type_id) ? $request->class_type_id : $TeacherAssignedClass;
            $filterOnDate = $request->date_filter ?? '';
            $title = $request->title ?? '';
            // $SelfLearningTestList = MyTeachingReport::Select('*')->with('exams')
            //                         ->whereHas('exams',function($q){
            //                             $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear());
            //                         })
            //                         ->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL, $gradeId)
            //                         ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL,$classTypeId)
            //                         ->where([
            //                             cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
            //                             cn::TEACHING_REPORT_REPORT_TYPE_COL => 'self_learning',
            //                             cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  2,
            //                             cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
            //                         ])
            //                         ->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);

            $Query =  MyTeachingReport::Select('*')->with('exams')
                                    ->whereHas('exams',function($q){
                                        $q->where(cn::EXAM_CURRICULUM_YEAR_ID_COL, $this->GetCurriculumYear());
                                    })
                                    ->whereIn(cn::TEACHING_REPORT_GRADE_ID_COL, $gradeId)
                                    ->whereIn(cn::TEACHING_REPORT_CLASS_ID_COL,$classTypeId)
                                    ->where([
                                        cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::TEACHING_REPORT_REPORT_TYPE_COL => 'self_learning',
                                        cn::TEACHING_REPORT_STUDY_TYPE_COL  =>  2,
                                        cn::TEACHING_REPORT_SCHOOL_ID_COL   =>  $schoolId,
                                    ]);
            if(!empty($title) && isset($title)){
                $Query->whereHas('exams',function($q) use($title){
                    $q->where(cn::EXAM_TABLE_TITLE_COLS,'Like','%'.$title.'%');
                });
            }
            if(!empty($filterOnDate) && isset($filterOnDate)){
                $Query = $this->filterOnDateInReport($Query,$filterOnDate);
            }
            $SelfLearningTestList = $Query->orderBy(cn::TEACHING_REPORT_EXAM_ID_COL,'DESC')->paginate($items);
                        
            //After filtration selected value selected display.
            $GradeClassListDataArr = GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeId)
                                        ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                                        ->where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                        ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$TeacherAssignedClass)->get()->toArray();
            if(!empty($GradeClassListDataArr)){
                foreach($GradeClassListDataArr as $class){
                    $GradeList = Grades::find($class[cn::GRADE_CLASS_MAPPING_GRADE_ID_COL]);
                    $GradeClassListData[strtoupper($class[cn::GRADE_CLASS_MAPPING_ID_COL])]=$GradeList->name.strtoupper($class[cn::GRADE_CLASS_MAPPING_NAME_COL]);
                }
            }
        }
        return view('backend/MyTeachingReports/self_learning_test',compact('SelfLearningTestList','difficultyLevels','items','schoolId','gradesList','grade_id','class_type_id','GradeClassListData','strandsList','LearningUnits','LearningObjectives'));
    }

    /**
     * USE : Get Exams all over summary reports
     */
    public function StudentResultSummaryReport(Request $request){
        $records = $this->getStudentResultSummary($request);
        $result = array();
        if(!empty( $records)){
            $result['html'] = (string)View::make('backend/teacher/student_result_summary',compact('records'));
            return $this->sendResponse($result);
        }else{
            return $this->SendError(__('languages.no_any_students_are_attempt_exams_please_wait_until_students_can_attempt_this_exams'),422);
        }
    }
}
