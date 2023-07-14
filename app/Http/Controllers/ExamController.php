<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\User;
use App\Models\Grades;
use App\Models\Question;
use App\Models\Subjects;
use App\Models\School;
use App\Models\AttemptExams;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\Answer;
use App\Models\Nodes;
use App\Models\GradeClassMapping;
use App\Models\TeachersClassSubjectAssign;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\UploadDocuments;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use App\Constants\DbConstant As cn;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Http\Repositories\QuestionsRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use DB;
use App\Http\Services\AIApiService;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Helpers\Helper;
use Carbon\Carbon;
use App\Jobs\UpdateStudentOverAllAbility;
use Illuminate\Database\Eloquent\QueryException;
use App\Http\Controllers\CronJobController;
use App\Http\Services\TeacherGradesClassService;
use App\Http\Services\StudentService;
use App\Models\ExamGradeClassMappingModel;
use App\Models\ExamSchoolMapping;
use App\Models\PeerGroupMember;
use App\Models\PeerGroup;
use App\Models\AttemptExamStudentMapping;
use App\Models\CurriculumYear;
use App\Models\CurriculumYearStudentMappings;
use App\Models\StudentAnswerHistory;
use App\Events\UserActivityLog;
use App\Models\UsedQuestionAnswerCount;

class ExamController extends Controller
{
    // Load Common Traits
    use Common, ResponseFormat;

    protected $AIApiService, $CronJobController, $TeacherGradesClassService, $ExamGradeClassMappingModel, $StudentService;

    public function __construct(){
        ini_set('max_execution_time', -1);
        $this->QuestionsRepository = new QuestionsRepository();
        $this->AIApiService = new AIApiService();
        $this->CronJobController = new CronJobController;
        $this->TeacherGradesClassService = new TeacherGradesClassService;
        $this->ExamGradeClassMappingModel = new ExamGradeClassMappingModel;
        $this->StudentService = new StudentService;
    }

    /**
     * USE : Delete existing exams
     */
    public function destroy($id){
        try{
            if(!in_array('exam_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))){
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            if($this->isAdmin()){
                // Delete exams school mapping
                $exam = Exam::find($id);
                if($exam->use_of_mode === 2){
                    $ChildExamIds = array();
                    $ChildExamIds = Exam::where('parent_exam_id',$id)->pluck('id')->toArray();
                    // Delete Exam school mapping
                    ExamSchoolMapping::whereIn(cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL,$ChildExamIds)->delete();

                    // Delete All child exams
                    Exam::where('parent_exam_id',$id)->delete();
                    if($exam->delete()){
                        return $this->sendResponse([], __('languages.exam_deleted_successfully'));
                    }else{
                        return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                    }
                }else{
                    if(ExamSchoolMapping::where(cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL,$id)->delete()){
                        if($exam->delete()){
                            return $this->sendResponse([], __('languages.exam_deleted_successfully'));
                        }else{
                            return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                        }
                    }else{
                        return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                    }
                }
            }else{
                $exam = Exam::find($id);
                if($exam->use_of_mode == 1){
                    // Delete Exam school grade class mapping
                    ExamGradeClassMappingModel::where([
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $id,
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                    ])->delete();

                    // Delete Exam school mapping
                    ExamSchoolMapping::where([
                        cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL => $id,
                        cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                    ])->delete();
                    
                    if($exam->created_by_user != 'super_admin'){
                        // Delete main exam
                        if($exam->delete()){
                            return $this->sendResponse([], __('languages.exam_deleted_successfully'));
                        }else{
                            return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                        }
                    }else{
                        $ExistingSchoolIds = explode(',',$exam->school_id);
                        $RemoveSchoolIds = array(Auth::user()->{cn::USERS_SCHOOL_ID_COL});
                        $RemainingSchoolIds = implode(',',array_diff($ExistingSchoolIds, $RemoveSchoolIds));
                        $update = Exam::find($id)->Update(['school_id' => $RemainingSchoolIds]);
                        if($update){
                            return $this->sendResponse([], __('languages.exam_deleted_successfully'));
                        }else{
                            return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                        }
                    }
                }else{ // Use of mode == 2
                    // First we need to remove exam grade class mapping table records
                    ExamGradeClassMappingModel::where([
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $id,
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                    ])->delete();
                    
                    // Delete Exam school mapping
                    ExamSchoolMapping::where([
                        cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL => $id,
                        cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                    ])->delete();

                    // Get the parent exam id
                    $ParentExamId = $exam->parent_exam_id;
                    $ParentExamData = Exam::find($ParentExamId);
                    $ExistingSchoolIds = explode(',',$ParentExamData->school_id);
                    $RemoveSchoolIds = array(Auth::user()->{cn::USERS_SCHOOL_ID_COL});
                    $RemainingSchoolIds = implode(',',array_diff($ExistingSchoolIds, $RemoveSchoolIds));
                    // Update Parent exam records
                    $update = Exam::find($ParentExamId)->Update(['school_id' => $RemainingSchoolIds]);
                    if($update){
                        // delete the exams
                        Exam::find($id)->delete();
                        return $this->sendResponse([], __('languages.exam_deleted_successfully'));
                    }else{
                        return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                    }
                }
            }
        }catch (Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    /**
     * USE : Delete multiple exams
     */
    // public function deleteMultipleExams(Request $request){
    //     try{
    //         $AttemptExams = AttemptExams::whereIn(cn::ATTEMPT_EXAMS_EXAM_ID,$request->examIds)->get();
    //         if(isset($AttemptExams) && !$AttemptExams->isEmpty()){
    //             $this->StoreAuditLogFunction('','AttemptExams','','','Delete Attempt Exams ID '.implode(',',array_unique($request->examIds)),cn::ATTEMPT_EXAMS_TABLE_NAME,'');
    //             $DeleteAttemptExams = AttemptExams::whereIn(cn::ATTEMPT_EXAMS_EXAM_ID,$request->examIds)->delete();
    //             if($DeleteAttemptExams){
    //                 $DeleteExams = Exam::whereIn(cn::EXAM_TABLE_ID_COLS,$request->examIds)->delete();
    //                 if($DeleteExams){
    //                     return $this->sendResponse([], __('languages.exam_deleted_successfully'));
    //                 }else{
    //                     return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
    //                 }
    //             }else{
    //                 return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
    //             }
    //         }else{
    //             $this->StoreAuditLogFunction('','','','','Delete Exam ID '.implode(',',array_unique($request->examIds)),cn::EXAM_TABLE_NAME,'');
    //             $DeleteExams = Exam::whereIn(cn::EXAM_TABLE_ID_COLS,$request->examIds)->delete();
    //             if($DeleteExams){
    //                 return $this->sendResponse([], __('languages.exam_deleted_successfully'));
    //             }else{
    //                 return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
    //             }
    //         }
    //     }catch(Exception $exception) {
    //         return $this->sendError($exception->getMessage(), 404);
    //     }
    // }

    /**
     * USE : Get the all assigned exams list for students
     */
    public function getStudentExamList(Request $request){
        try{
            $userId = Auth::id();
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
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
                                    ->get();
            // Get Test Exams List
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
                                ->get();
            return view('backend/exams/student_exam_list',compact('data','difficultyLevels','active_tab','schoolId','userId','roleId'));
            
        }catch(Exception $exception){
            return back()->withError($exception->getMessage());
        }
    }


    public function getStudentExerciseExamList(Request $request){
        try{
            $userId = (Auth::user()->role_id == 3) ? Auth::user()->id : request()->id;
            $studentId = $userId;
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
            $studentData = User::find($userId);
            $ExamList = array();
            $active_tab = "";
            $difficultyLevels = PreConfigurationDiffiltyLevel::get();
            if(isset($request->active_tab) && !empty($request->active_tab)){
                $active_tab = $request->active_tab;
            }
            $data = array();
            // Get Student assigned exam ids
            $GetStudentAssignedExamsIds = [];
            $GetStudentAssignedExamsIds = $this->StudentService->GetStudentAssignedExamsIds($userId);
            // Get Exercise Exams List
            // $data['exerciseExam'] = Exam::with(['attempt_exams' => fn($query) => $query->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, Auth::user()->{cn::USERS_ID_COL})])
            //                         ->with(['ExamGradeClassConfigurations' => function($q){
            //                             $q->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
            //                             ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
            //                             ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,Auth::user()->CurriculumYearGradeId)
            //                             ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,Auth::user()->CurriculumYearClassId)
            //                             ->orWhereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL,$this->getStudentPeerGroupIds());
            //                         }])
            //                         ->with('examSchoolGradeClass', function($q) use($userId){
            //                             $q->whereRaw("find_in_set($userId,student_ids)");
            //                         })
            //                         ->whereRaw("find_in_set($userId,student_ids)")
            //                         ->where(cn::EXAM_TYPE_COLS,2)
            //                         ->where(cn::EXAM_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
            //                         ->where(cn::EXAM_TABLE_IS_GROUP_TEST_COL,0)
            //                         ->whereIn(cn::EXAM_TABLE_ID_COLS,$GetStudentAssignedExamsIds)
            //                         ->where(cn::EXAM_TABLE_STATUS_COLS,'publish')
            //                         ->orderBy(cn::EXAM_TABLE_CREATED_AT,'DESC')
            //                         ->get();
            $data['exerciseExam'] = Exam::with(['attempt_exams' => fn($query) => $query->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, $userId)])
                                    ->with(['ExamGradeClassConfigurations' => function($q) use($schoolId,$studentData){
                                        $q->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                                        ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                        ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,$studentData->CurriculumYearGradeId)
                                        ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,$studentData->CurriculumYearClassId)
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
                                    ->get();
                if(Auth::user()->role_id != 3){
                    return view('backend/exams/student_exercise_exam_list',compact('data','difficultyLevels','active_tab','schoolId','userId','roleId','studentId','studentData'));
                }else{
                    return view('backend/exams/exercise_exam_list',compact('data','difficultyLevels','active_tab','schoolId','userId','roleId'));
                }                    
            
        }catch(Exception $exception){
            return back()->withError($exception->getMessage());
        }
    }


    public function getStudentTestExamList(Request $request){
        try{
            $userId = (Auth::user()->role_id == 3) ? Auth::user()->id : request()->id;
            $studentId = $userId;
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
            $studentData = User::find($userId);
            $ExamList = array();
            $active_tab = "";
            $difficultyLevels = PreConfigurationDiffiltyLevel::get();
            if(isset($request->active_tab) && !empty($request->active_tab)){
                $active_tab = $request->active_tab;
            }
            $data = array();
            // Get Student assigned exam ids
            $GetStudentAssignedExamsIds = [];
            $GetStudentAssignedExamsIds = $this->StudentService->GetStudentAssignedExamsIds($userId);
            // Get Test Exams List
            // $data['testExam'] = Exam::with(['attempt_exams' => fn($query) => $query->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, Auth::user()->{cn::USERS_ID_COL})])
            //                     ->with(['ExamGradeClassConfigurations' => function($q){
            //                         $q->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
            //                         ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,Auth::user()->CurriculumYearGradeId)
            //                         ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,Auth::user()->CurriculumYearClassId)
            //                         ->orWhereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL,$this->getStudentPeerGroupIds());
            //                     }])
            //                     ->with('examSchoolGradeClass', function($q) use($userId){
            //                         $q->whereRaw("find_in_set($userId,student_ids)");
            //                     })
            //                     ->whereRaw("find_in_set($userId,student_ids)")
            //                     ->where(cn::EXAM_TYPE_COLS,3)
            //                     ->where(cn::EXAM_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
            //                     ->where(cn::EXAM_TABLE_IS_GROUP_TEST_COL,0)
            //                     ->whereIn(cn::EXAM_TABLE_ID_COLS,$GetStudentAssignedExamsIds)
            //                     ->where(cn::EXAM_TABLE_STATUS_COLS,'publish')
            //                     ->orderBy(cn::EXAM_TABLE_CREATED_AT,'DESC')
            //                     ->get();   
            $data['testExam'] = Exam::with(['attempt_exams' => fn($query) => $query->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, $userId)])
                                ->with(['ExamGradeClassConfigurations' => function($q) use($schoolId,$studentData){
                                    $q->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                                    ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,$studentData->CurriculumYearGradeId)
                                    ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,$studentData->CurriculumYearClassId)
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
                                ->get(); 
                if(Auth::user()->role_id != 3){
                    return view('backend/exams/student_test_exam_list',compact('data','difficultyLevels','active_tab','schoolId','userId','roleId','studentId','studentData'));
                }else{
                    return view('backend/exams/test_exam_list',compact('data','difficultyLevels','active_tab','schoolId','userId','roleId'));
                }      
                
        }catch(Exception $exception){
            return back()->withError($exception->getMessage());
        }
    }

    /**
     * USE : Store Exam Student Mapping entry while attempting the exams.
     */
    public function CreateExamStudentMapping($examId){
        if(AttemptExamStudentMapping::where([
            cn::ATTEMPT_EXAM_STUDENT_MAPPING_EXAM_ID_COL => $examId,
            cn::ATTEMPT_EXAM_STUDENT_MAPPING_STUDENT_ID_COL => $this->LoggedUserId()
        ])->exists()){
            AttemptExamStudentMapping::where([
                cn::ATTEMPT_EXAM_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                cn::ATTEMPT_EXAM_STUDENT_MAPPING_EXAM_ID_COL => $examId,
                cn::ATTEMPT_EXAM_STUDENT_MAPPING_STUDENT_ID_COL => $this->LoggedUserId()
            ])->Update([cn::ATTEMPT_EXAM_STUDENT_MAPPING_STATUS_COL => '1']);
        }else{
            AttemptExamStudentMapping::Create([
                cn::ATTEMPT_EXAM_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                cn::ATTEMPT_EXAM_STUDENT_MAPPING_EXAM_ID_COL => $examId,
                cn::ATTEMPT_EXAM_STUDENT_MAPPING_STUDENT_ID_COL => $this->LoggedUserId(),
                cn::ATTEMPT_EXAM_STUDENT_MAPPING_STATUS_COL => '1'
            ]);
        }
    }

    /**
     * USE : Student can attempt can exams
     */
    public function studentAttemptExam(Request $request){
        try{
            $selectedOldAnswer = '';
            $SelectedAnswersArray = [];
            $taking_exam_timing = '00:00:00';
            $examId = request()->route('id');
            $arrayOfExams = explode(',',$examId);
            $questionSize = 0;
            $testType = 0;
            $examType = 'single';

            $examDetail = Exam::where(cn::EXAM_TABLE_ID_COLS,$examId)->first();
            if(isset($examId)){
                $checkAlreadyAttemptExams = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)
                                            ->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$this->LoggedUserId())
                                            ->count();
                if($checkAlreadyAttemptExams){
                    return redirect('student/exam')->with('error_msg', __('validation.you_are_already_attempt_this_exams'));
                }
                // if($examDetail->exam_type==1){
                //     if(AttemptExamStudentMapping::where([
                //         cn::ATTEMPT_EXAM_STUDENT_MAPPING_EXAM_ID_COL => $examId,
                //         cn::ATTEMPT_EXAM_STUDENT_MAPPING_STUDENT_ID_COL => $this->LoggedUserId(),
                //         cn::ATTEMPT_EXAM_STUDENT_MAPPING_STATUS_COL => '1'
                //     ])->exists()){
                //         return redirect('student/exam')->with('error_msg', __("languages.sorry_you_cant_attempt_exam_after_page_refresh"));
                //     }
                // }
            }
            // Store Exam Student Mapping Data like if student can attempt only one time exams.
            //$this->CreateExamStudentMapping($examId);

            $examLanguage = 'en';
            if(isset($request->language)){
                if(!empty($request->SelectedAnswersArray)){
                    $SelectedAnswersArray = json_decode($request->SelectedAnswersArray);
                }
                $taking_exam_timing = $request->exam_taking_timing;
                $validator = Validator::make($request->all(), AttemptExams::rules($request, 'change_exam_language', []), AttemptExams::rulesMessages('change_exam_language'));
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }
                $examLanguage = $request->language;
            }
            //Update Column Is_my_teaching_sync
            Exam::find($examId)->update([cn::EXAM_TABLE_IS_TEACHING_REPORT_SYNC =>'true']);
            $totalSeconds = Exam::where(cn::EXAM_TABLE_ID_COLS,$examId)
                            ->where(cn::EXAM_TABLE_IS_UNLIMITED,0)
                            ->sum(cn::EXAM_TABLE_TIME_DURATIONS_COLS);
            if(empty($totalSeconds)){
                $totalSeconds = 'unlimited_time';
            }
            if(isset($examDetail) && !empty($examDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL})){
                $question_ids = explode(',',$examDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL});
                $questionSize = sizeof($question_ids);
            }
            
            $Questions = Question::with('answers')
                        ->whereIn(cn::QUESTION_TABLE_ID_COL,$question_ids)
                        ->orderByRaw('FIELD(id,'.$examDetail->question_ids.')')
                        ->limit(1)
                        ->get();

            if(StudentAnswerHistory::where([
                'exam_id' => $examId,
                'student_id' => Auth::user()->{cn::USERS_ID_COL},
                'question_id' => $Questions[0]->id]
            )->exists()){
                $selectedOldAnswer = StudentAnswerHistory::where([
                                        'exam_id' => $examId,
                                        'student_id' => Auth::user()->{cn::USERS_ID_COL},
                                        'question_id' => $Questions[0]->id
                                    ])->first();
                (int)$selectedOldAnswer = $selectedOldAnswer->selected_answer;
            }
            
            $UploadDocumentsData = array();
            if($examLanguage == 'en'){
                if(isset($Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}) && $Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}!=""){
                    $UploadDocumentsData = UploadDocuments::find($Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN});
                }else{
                    $arrayOfQuestion = explode('-',$Questions[0]->{cn::QUESTION_QUESTION_CODE_COL});
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
                if(isset($Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}) && $Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}!=""){
                    $UploadDocumentsData = UploadDocuments::find($Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH});
                }else{
                    $arrayOfQuestion = explode('-',$Questions[0]->{cn::QUESTION_QUESTION_CODE_COL});
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

            if($examDetail->created_by == Auth::user()->{cn::USERS_ID_COL}){
                // This exam is personal then 1
                $testType = 1;
            }
            return view('backend/exams/student_exam_attempt',compact('examType','examId','selectedOldAnswer','Questions','examLanguage','examDetail', 'SelectedAnswersArray','taking_exam_timing','UploadDocumentsData','testType','questionSize','totalSeconds'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage());
        }
    }

    /**
     * USE : Get the next question from the database
     */
    public function NextQuestion(Request $request){
        try{
            $selectedOldAnswer = '';
            $attempAnswer = $request->attempt_ans;
            $currentid = $request->currentid;
            $SelectedAnswersArray = [];
            $taking_exam_timing = '00:00:00';
            $examId = $request->examid;
            $arrayOfExams = explode(',',$examId);
            $allQuestionIds = '';
            $arrayQuestionsIds = '';
            $wrong_ans_position = array();
            $examType = 'single';
            $allQuestionIds = Exam::where(cn::EXAM_TABLE_ID_COLS,$examId)
                                ->select(cn::EXAM_TABLE_QUESTION_IDS_COL)->first();
            if(!empty($allQuestionIds)){
                $arrayQuestionsIds = explode(',',$allQuestionIds->question_ids);
            }
            $questionIdsList = $allQuestionIds->question_ids;
            if(isset($examId)){
                $checkAlreadyAttemptExams = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)
                                            ->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$this->LoggedUserId())
                                            ->count();
                if($checkAlreadyAttemptExams){
                    return redirect('student/exam')->with('error_msg', __('validation.you_are_already_attempt_this_exams'));
                }
            }
            $examLanguage = 'en';
            if(isset($request->language)){
                if(!empty($request->SelectedAnswersArray)){
                    $SelectedAnswersArray = json_decode($request->SelectedAnswersArray);
                }
                $taking_exam_timing = $request->exam_taking_timing;
                $validator = Validator::make($request->all(), AttemptExams::rules($request, 'change_exam_language', []), AttemptExams::rulesMessages('change_exam_language'));
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }
                $examLanguage = $request->language;
            }
            $examDetail = Exam::where(cn::EXAM_TABLE_ID_COLS,'=',$examId)->first();
            if(isset($examDetail) && !empty($examDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL})){
                $question_ids = explode(',',$examDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL});
                $questionIdsList=implode(',',$question_ids);
            }

            if(isset($request->attempt_ans) && $request->attempt_ans == 1 && isset($request->wrong_ans) && !empty($request->wrong_ans)){
                $wrong_ans = $request->wrong_ans;
                $wrong_ans = array_column($wrong_ans,'question_id');
                $question_ids = array_unique($wrong_ans);
                $questionIdsList=implode(',',$question_ids);
                $wrong_ans_position = array_column($request->wrong_ans,'questionNo','question_id');
            }
            $QuestionsCount = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,$question_ids)->get()->count();
            $Questionsasc = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,$question_ids)->orderByRaw('FIELD(id,'.$questionIdsList.')')->get()->toArray();
            $Questionsdesc = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,$question_ids)->orderByRaw('FIELD(id,'.$questionIdsList.')')->get()->toArray();
            $Questionsfirstid = $question_ids[0];
            $Questionslastid = end($question_ids);

            if($request->examaction == 'Next'){
                $NextQuestionKey = array_search($request->currentid, $question_ids);
                if(isset($question_ids[$NextQuestionKey+1])){
                    $Questions = Question::with('answers')->where(cn::QUESTION_TABLE_ID_COL,$question_ids[$NextQuestionKey+1])->limit(1)->get();
                    if(StudentAnswerHistory::where(['exam_id'=>$examId ,'student_id' => Auth::user()->{cn::USERS_ID_COL},'question_id' =>$question_ids[$NextQuestionKey+1]])->exists()){
                        $selectedOldAnswer = StudentAnswerHistory::select('selected_answer')->where(['exam_id'=> $examId,'student_id' => Auth::user()->{cn::USERS_ID_COL},'question_id' => $question_ids[$NextQuestionKey+1]])->first();
                        (int)$selectedOldAnswer = $selectedOldAnswer->selected_answer;
                    }
                }
            }else{
                $NextQuestionKey=array_search($request->currentid, $question_ids);
                if(isset($question_ids[$NextQuestionKey-1])){
                    $Questions = Question::with('answers')->where(cn::QUESTION_TABLE_ID_COL,$question_ids[$NextQuestionKey-1])->limit(1)->get();
                    if(StudentAnswerHistory::where(['exam_id'=>$examId ,'student_id' => Auth::user()->{cn::USERS_ID_COL},'question_id' =>$question_ids[$NextQuestionKey-1]])->exists()){
                        $selectedOldAnswer = StudentAnswerHistory::where(['exam_id'=> $examId,'student_id' => Auth::user()->{cn::USERS_ID_COL},'question_id' => $question_ids[$NextQuestionKey-1]])->first();
                        (int)$selectedOldAnswer = $selectedOldAnswer->selected_answer;
                    }
                }
            }
            
            if(isset($request->examactionset) && $request->examactionset=='current'){
                $Questions = Question::with('answers')->where(cn::QUESTION_TABLE_ID_COL, $request->currentid)->orderByRaw('FIELD(id,'.$questionIdsList.')')->limit(1)->get();
                if(StudentAnswerHistory::where(['exam_id'=>$examId ,'student_id' => Auth::user()->{cn::USERS_ID_COL},'question_id' =>$question_ids[$NextQuestionKey]])->exists()){
                    $selectedOldAnswer = StudentAnswerHistory::where(['exam_id'=> $examId,'student_id' => Auth::user()->{cn::USERS_ID_COL},'question_id' => $question_ids[$NextQuestionKey]])->first();
                    (int)$selectedOldAnswer = $selectedOldAnswer->selected_answer;
                }
            }
            $UploadDocumentsData = array();
            if($examLanguage == 'en'){
                if(isset($Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}) && $Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}!=""){
                    $UploadDocumentsData = UploadDocuments::find($Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN});
                }else{
                    $arrayOfQuestion = explode('-',$Questions[0]->{cn::QUESTION_QUESTION_CODE_COL});
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
                if(isset($Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}) && $Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}!=""){
                    $UploadDocumentsData = UploadDocuments::find($Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH});
                }else{
                    $arrayOfQuestion = explode('-',$Questions[0]->{cn::QUESTION_QUESTION_CODE_COL});
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
            $questionNo = $request->questionNo;
            $question_position = array();
            if(isset($request->question_position)){
                $question_position = array_column($request->question_position,'position','question_id');
            }
            return view('backend/exams/nextquestion',compact('examType','examId','Questions','examLanguage','examDetail','selectedOldAnswer' ,'SelectedAnswersArray','taking_exam_timing','questionNo','Questionsfirstid','Questionslastid','UploadDocumentsData','attempAnswer','arrayQuestionsIds','wrong_ans_position','question_position'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage());
        }
    }

    public function StoreStudentExamHistory(Request $request){
        if(StudentAnswerHistory::where([
            cn::STUDENT_ATTEMPT_EXAM_HISTORY_EXAM_ID_COL        => $request->examid,
            cn::STUDENT_ATTEMPT_EXAM_HISTORY_STUDENT_ID_COL     => Auth::user()->{cn::USERS_ID_COL},
            cn::STUDENT_ATTEMPT_EXAM_HISTORY_QUESTION_ID_COL    => $request->questionid])
        ->doesntExist()){
            StudentAnswerHistory::create([
                cn::STUDENT_ATTEMPT_EXAM_HISTORY_EXAM_ID_COL        => $request->examid,
                cn::STUDENT_ATTEMPT_EXAM_HISTORY_QUESTION_ID_COL    => $request->questionid,
                cn::STUDENT_ATTEMPT_EXAM_HISTORY_STUDENT_ID_COL     => Auth::user()->{cn::USERS_ID_COL},
                cn::STUDENT_ATTEMPT_EXAM_HISTORY_SELECTED_ANSWER_COL => $request->answer,
                cn::STUDENT_ATTEMPT_EXAM_HISTORY_LANGUAGE_COL       => $request->language,
            ]);
        }else{
            StudentAnswerHistory::where([
                cn::STUDENT_ATTEMPT_EXAM_HISTORY_EXAM_ID_COL        => $request->examid,
                cn::STUDENT_ATTEMPT_EXAM_HISTORY_STUDENT_ID_COL     => Auth::user()->{cn::USERS_ID_COL},
                cn::STUDENT_ATTEMPT_EXAM_HISTORY_QUESTION_ID_COL    => $request->questionid
            ])->update([
                cn::STUDENT_ATTEMPT_EXAM_HISTORY_EXAM_ID_COL            => $request->examid,
                cn::STUDENT_ATTEMPT_EXAM_HISTORY_QUESTION_ID_COL        => $request->questionid,
                cn::STUDENT_ATTEMPT_EXAM_HISTORY_STUDENT_ID_COL         => Auth::user()->{cn::USERS_ID_COL},
                cn::STUDENT_ATTEMPT_EXAM_HISTORY_SELECTED_ANSWER_COL    => $request->answer,
                cn::STUDENT_ATTEMPT_EXAM_HISTORY_LANGUAGE_COL           => $request->language,
            ]);
        }
    }

    /**
     * USE : Student can change attempt exam language
     */
    public function studentChangeLanguageAttemtExam(Request $request, $examId){
       try {
            if(!isset($request->language)){
                return $this->sendError('languages.please_select_language', 422);
            }
            $questionNo = $request->questionNo;
            $question_id = $request->question_id;
            $arrayOfExams = explode(',',$examId);
            $examType = 'singleTest';
            $examLanguage = $request->language;
            $SelectedAnswersArray = $request->SelectedAnswersArray;
            $examDetail = Exam::where(cn::EXAM_TABLE_ID_COLS,$examId)->first();
            if(isset($examDetail) && !empty($examDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL})){
                $question_ids = explode(',',$examDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL});
            }
            $Questions = Question::with('answers')
                        ->where(cn::QUESTION_TABLE_ID_COL,$question_id)
                        ->get();
            $QuestionsCount = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,$question_ids)->get()->count();
            $Questionsasc = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,$question_ids)->orderBy(cn::QUESTION_TABLE_ID_COL,'asc')->get()->toArray();
            $Questionsdesc = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,$question_ids)->orderBy(cn::QUESTION_TABLE_ID_COL,'desc')->get()->toArray();
            $Questionsfirstid = $Questionsasc[0][cn::QUESTION_TABLE_ID_COL];
            $Questionslastid = $Questionsdesc[0][cn::QUESTION_TABLE_ID_COL];
            $UploadDocumentsData = array();
            if($examLanguage == 'en'){
                if(isset($Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}) && $Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}!=""){
                    $UploadDocumentsData = UploadDocuments::find($Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN});
                }
            }else{
                if(isset($Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}) && $Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}!=""){
                    $UploadDocumentsData = UploadDocuments::find($Questions[0]->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH});
                }
            }

            $question_position=array();
            if(isset($request->question_position)){
                $question_position=array_column($request->question_position,'position','question_id');
            }
            $result['html'] = (string)View::make('backend.exams.change_language_exams_html',compact('examType','examLanguage','examDetail','Questions','SelectedAnswersArray','question_id','questionNo','Questionsfirstid','Questionslastid','UploadDocumentsData','question_position'));
            return $this->sendResponse($result);
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    /**
     * USE : Student Attempt exams answer save
     */
    public function studentExamAnswerSave(Request $request){
        try{
            $examId = $request->exam_id;
            if(isset($examId)){
                $checkAlreadyAttemptExams = AttemptExams::where([
                                                cn::ATTEMPT_EXAMS_EXAM_ID => $examId,
                                                cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID => $this->LoggedUserId()
                                            ])->count();
                if($checkAlreadyAttemptExams){
                    return redirect('student/exam')->with('error_msg', __('validation.you_are_already_attempt_this_exams'));
                }
            }
            $examDetail = Exam::find($examId);
            /* This code to using personal exam start */
            $apiData = [];
            
            if(Auth::user()->{cn::USERS_ID_COL} == $examDetail->{cn::EXAM_TABLE_CREATED_BY_COL} && $examDetail->{cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL} == 2){
                $question_ids_data = explode(',',$examDetail->{cn::EXAM_TABLE_QUESTION_IDS_COL});
                $question_ids_size = sizeof($question_ids_data);
                if(isset($request->questions_ans) && !empty($request->questions_ans)){
                    $questions_ans_data = json_decode($request->questions_ans,true);
                    $questions_ans = array_column($questions_ans_data,'question_id');
                    if(sizeof($questions_ans) != $question_ids_size){
                        $not_attempt_questions = array_diff($question_ids_data, $questions_ans);
                        if(isset($not_attempt_questions) && !empty($not_attempt_questions)){
                            foreach($not_attempt_questions as $not_attempt_key => $not_attempt_value){
                                $QuestionAnswerDetail = Question::where(cn::QUESTION_TABLE_ID_COL,$not_attempt_value)->with('answers')->first();
                                if(isset($QuestionAnswerDetail)){
                                    $random_number_array = range(1,4);
                                    shuffle($random_number_array);
                                    $random_number_array = array_slice($random_number_array ,0,4);
                                    $correct_ans = $QuestionAnswerDetail->answers->{'correct_answer_'.$request->language};
                                    unset($random_number_array[array_search($correct_ans,$random_number_array)]);
                                    $random_number_array = array_values($random_number_array);
                                    $questions_ans_data[] = array(
                                                                'question_id' => $not_attempt_value,
                                                                'answer' => $random_number_array[0],
                                                                'language' => $request->language,
                                                                'duration_second' => '0:00:10'
                                                            );
                                }
                            }
                        }
                        //$questions_ans_data=$this->json_encode($questions_ans_data);
                        $questions_ans_data = json_encode($questions_ans_data);
                        $request['questions_ans'] = $questions_ans_data;
                    }
                }
            }
            
            /* This code to using personal exam end */
            $wrong_ans_list_json = "";
            $wrong_ans_list = array();
            if(isset($request->wrong_ans) && !empty($request->wrong_ans)){
                $wrong_ans_array_data = json_decode($request->wrong_ans);
                $wrong_ans_array = array_column($wrong_ans_array_data,'answer','question_id');
                $wrong_ans_array_language = array_column($wrong_ans_array_data,'language','question_id');
            }

            if(isset($request->questions_ans) && !empty($request->questions_ans)){
                $NoOfCorrectAnswers = 0;
                $NoOfWrongAnswers = 0;
                foreach (json_decode($request->questions_ans) as $key => $value) {
                    $QuestionId = $value->question_id;

                    // Update question count
                    $this->UpdateUsedQuestionAnswerCounts($QuestionId,'question_count');

                    // Get Questions Answers and difficulty level
                    $responseData = $this->GetQuestionNumOfAnswerAndDifficultyValue($value->question_id,$examDetail->{cn::EXAM_CALIBRATION_ID_COL});
                    $apiData['num_of_ans_list'][] = $responseData['noOfAnswers'];
                    $apiData['difficulty_list'][] = $responseData['difficulty_value'];
                    $apiData['max_student_num'] = 1;

                    if(isset($wrong_ans_array) && isset($wrong_ans_array[$QuestionId])){
                        $wrong_ans_list[] = array(
                                                'question_id'   => $QuestionId,
                                                'answer'        => $wrong_ans_array[$QuestionId],
                                                'language'      => $wrong_ans_array_language[$QuestionId]
                                            );
                    }
                    $answer = $value->answer;
                    $QuestionAnswerDetail = Question::where(cn::QUESTION_TABLE_ID_COL,$QuestionId)->with('answers')->first();
                    if(isset($QuestionAnswerDetail)){
                        if($QuestionAnswerDetail->answers->{'correct_answer_'.$value->language} == $answer){
                            $NoOfCorrectAnswers = ($NoOfCorrectAnswers + 1);
                            $apiData['questions_results'][] = true;
                        }else{
                            $NoOfWrongAnswers = ($NoOfWrongAnswers + 1);
                            $apiData['questions_results'][] = false;
                        }
                    }
                    // Update question selected answer count
                    $this->UpdateUsedQuestionAnswerCounts($QuestionId,'answer_count',($answer ?? 5));
                }
            }
            if(isset($wrong_ans_list) && !empty($wrong_ans_list)){
                $wrong_ans_list_json = json_encode($wrong_ans_list);
            }

            $StudentAbility = '';
            if(!empty($apiData)){
                // Get the student ability from calling AIApi
                //$StudentAbility = $this->GetAIStudentAbility($apiData);
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
            }
            $PostData = [
                cn::ATTEMPT_EXAMS_CURRICULUM_YEAR_ID_COL        => $this->GetCurriculumYear(),
                cn::ATTEMPT_EXAMS_CALIBRATION_ID_COL            => $this->GetCurrentAdjustedCalibrationId(),
                cn::ATTEMPT_EXAMS_EXAM_ID                       => $examId,
                cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID            => $this->LoggedUserId(),
                cn::ATTEMPT_EXAMS_STUDENT_GRADE_ID              => Auth::user()->CurriculumYearGradeId,
                cn::ATTEMPT_EXAMS_STUDENT_CLASS_ID              => Auth::user()->CurriculumYearClassId,
                cn::ATTEMPT_EXAMS_LANGUAGE_COL                  => $request->language,
                // cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL        => (!empty($request->questions_ans)) ? $request->questions_ans : null,
                cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL           => $this->setQuestionDifficultyTypeAndDifficultyValue($request->questions_ans,$examDetail->{cn::EXAM_CALIBRATION_ID_COL}),
                cn::ATTEMPT_EXAMS_WRONG_ANSWER_COL              => '',
                // cn::ATTEMPT_EXAMS_ATTEMPT_FIRST_TRIAL_COL    => $request->attempt_first_trial_data_new,
                //cn::ATTEMPT_EXAMS_ATTEMPT_SECOND_TRIAL_COL      => $wrong_ans_list_json,
                cn::ATTEMPT_EXAMS_ATTEMPT_FIRST_TRIAL_COL       => !empty($request->attempt_first_trial_data_new) ? $this->setQuestionDifficultyTypeAndDifficultyValue($request->attempt_first_trial_data_new,$examDetail->{cn::EXAM_CALIBRATION_ID_COL}) : null,
                cn::ATTEMPT_EXAMS_ATTEMPT_SECOND_TRIAL_COL      => !empty($wrong_ans_list_json) ? $this->setQuestionDifficultyTypeAndDifficultyValue($wrong_ans_list_json,$examDetail->{cn::EXAM_CALIBRATION_ID_COL}) : null,
                cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS         => $NoOfCorrectAnswers,
                cn::ATTEMPT_EXAMS_TOTAL_WRONG_ANSWERS           => $NoOfWrongAnswers,
                cn::ATTEMPT_EXAMS_EXAM_TAKING_TIMING            => $request->exam_taking_timing,
                cn::ATTEMPT_EXAMS_STUDENT_ABILITY_COL           => ($StudentAbility!='') ? $StudentAbility : null,
                cn::ATTEMPT_EXAMS_SERVER_DETAILS_COL            => json_encode($this->serverData()) ?? null,
                cn::ATTEMPT_EXAMS_BEFORE_EXAM_SURVEY_COL        => $request->before_exam_survey ?? null,
                cn::ATTEMPT_EXAMS_AFTER_EXAM_SURVEY_COL         => $request->after_exam_survey ?? null
            ];
            $save = AttemptExams::create($PostData);
            // Get the selected student answers
           
            if($save){
                $this->UserActivityLog(
                    Auth::user()->{cn::USERS_ID_COL},
                    '<p>'.Auth::user()->DecryptNameEn.' '.__('activity_history.exam_attempted').'. </p>'.
                    '<p>'.__('activity_history.test_type').$this->ActivityTestType($examDetail).'</p>'.
                    '<p>'.__('activity_history.title_is').$examDetail->title.'. </p>'.
                    '<p>'.__('activity_history.exam_reference_is').' '.$examDetail->reference_no.'. </p>'.
                    '<p>'.__('activity_history.exam_start_date_time').$examDetail->{cn::CREATED_AT_COL}.'</p>'.
                    '<p>'.__('activity_history.exam_completion_date_time').$examDetail->{cn::UPDATED_AT_COL}.'</p>'
                );
                //Update Column Is_my_teaching_sync
                Exam::find($examId)->update([cn::EXAM_TABLE_IS_TEACHING_REPORT_SYNC =>'true']);
                
                /** Start Update overall ability for the student **/
                if($examDetail->exam_type == 3 || ($examDetail->exam_type == 1 && $examDetail->self_learning_test_type == 2)){
                    $this->CronJobController->UpdateStudentOverAllAbility();
                }

                /** Update My Teaching Table Via Cron Job */
                $this->CronJobController->UpdateMyTeachingTable(Auth::user()->{cn::USERS_SCHOOL_ID_COL}, $examId);

                if($examDetail->exam_type == 2 || ($examDetail->exam_type == 1 && $examDetail->self_learning_test_type == 1)){
                    /** Update Student Credit Points via cron job */
                    $this->CronJobController->UpdateStudentCreditPoints($examId, Auth::user()->{cn::USERS_ID_COL});
                }
                
                /** End Update overall ability for the student **/
                $this->StoreAuditLogFunction('','Exams','','','Attempt Exam',cn::EXAM_TABLE_NAME,'');
                StudentAnswerHistory::where('exam_id',$examId)->delete();
                return redirect()->route('exams.result',['examid' => $examId, 'studentid' => Auth::user()->{cn::USERS_ID_COL}]);
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return back()->withError($exception->getMessage());
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
     * USE : View attemped exams related students
     */
    public function getListAttemptedExamsStudents(Request $request, $id){
        try{
            $studentList = [];
            $attemptedExamStudentIds = [];
            $items = $request->items ?? 10;
            $AttemptedChildExams =  Exam::where(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$id)
                                    ->where(cn::EXAM_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                    ->pluck(cn::EXAM_TABLE_ID_COLS)
                                    ->toArray();
            if($this->isAdmin()){
                if(!empty($AttemptedChildExams)){
                    $examsData = Exam::with('attempt_exams')->whereIn(cn::EXAM_TABLE_ID_COLS,$AttemptedChildExams)->get();
                    if(isset($examsData) && !empty($examsData)){
                        $attemptedStudents = array();
                        foreach($examsData as $exams){
                            $attemptedStudents[] = array_column($exams->attempt_exams->toArray(),cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID);
                        }
                        $attemptedExamStudentIds = $this->array_flatten($attemptedStudents);
                    }
                }else{
                    $examsData = Exam::with('attempt_exams')->find($id);
                }
            }

            if($this->isTeacherLogin() || $this->isSchoolLogin() || $this->isPrincipalLogin()|| $this->isPanelHeadLogin() || $this->isCoOrdinatorLogin()){
                $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
                $examsData = Exam::with('attempt_exams')->where(cn::EXAM_TABLE_ID_COLS,$id)->whereRaw("find_in_set($schoolId,school_id)")->first();
                
            }
            if(isset($examsData)){
                $attemptedExamStudentIds = array_column($examsData->attempt_exams->toArray(),cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID);
            }
            $countStudentData = 0;
            if(isset($examsData) && !empty($examsData->{cn::EXAM_TABLE_STUDENT_IDS_COL})){
                $countStudentData = User::whereIn(cn::USERS_ID_COL,explode(',',$examsData->{cn::EXAM_TABLE_STUDENT_IDS_COL}))
                                    ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                    ->with('grades')
                                    ->count(); 
                                                  
                if($this->isAdmin()){
                    $studentList = User::whereIn(cn::USERS_ID_COL,explode(',',$examsData->{cn::EXAM_TABLE_STUDENT_IDS_COL}))
                                    ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                    ->with('grades')
                                    ->paginate($items);
                }
                if($this->isTeacherLogin() || $this->isSchoolLogin() || $this->isPrincipalLogin() || $this->isPanelHeadLogin() || $this->isCoOrdinatorLogin()){
                    $studentList = User::whereIn(cn::USERS_ID_COL,explode(',',$examsData->{cn::EXAM_TABLE_STUDENT_IDS_COL}))
                                    ->where([
                                        cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                        cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                                    ])
                                    ->with('grades')
                                    ->paginate($items);
                }
            }
            $Grades = Grades::all();
            return view('backend/exams/list_attemped_exams_student',compact('studentList','Grades','attemptedExamStudentIds','examsData','countStudentData','items'));
        }catch(Exception $exception){
            return back()->withError($exception->getMessage());
        }
    }

    /**
     * USE : Get using AjaxResult for exams
     */
    public function getAjaxExamResult(Request $request, $examId, $studentId = 0){
        try{
            $isGroupId = (isset($request->isGroupId) && !empty($request->isGroupId)) ? $request->isGroupId : '';

            ini_set('max_execution_time', -1);
            $totalQuestionDifficulty =  [
                                            'Level1' => 0,
                                            'Level2' => 0,
                                            'Level3' => 0,
                                            'Level4' => 0,
                                            'Level5' => 0,
                                            'correct_Level1' => 0,
                                            'correct_Level2' => 0,
                                            'correct_Level3' => 0,
                                            'correct_Level4' => 0,
                                            'correct_Level5' => 0
                                        ];
            $difficultyLevels = PreConfigurationDiffiltyLevel::get();
            $arrayOfExams = explode(',',$examId);
            // Get the per question speed to attempt student
            $PerQuestionSpeed = Helper::getQuestionPerSpeed($examId, $studentId);

            $examType = 'singleTest';
            $studentId = ($studentId) ? $studentId : Auth::user()->{cn::USERS_ID_COL};
            $ExamData = Exam::find($examId);
            if(isset($ExamData)){
                $Questions = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,explode(',',$ExamData->{cn::EXAM_TABLE_QUESTION_IDS_COL}))->get();
            }
            $ResultList = [];
            $SchoolList = School::all();
            $GradeList = Grades::all();
            $ExamList = Exam::all();
            $QuestionSkills = [];
            $studentCount = 0;
            $totalQuestions = 0;
            $students = [];
            $answerPercentage = [];
            $CountStudentAnswer = [];
            $AllWeakness=[];
            /*****************************/
            if(!empty($ExamData)){
                $ResultList['examDetails'] = $ExamData->toArray();
                if(!empty($ExamData->{cn::EXAM_TABLE_STUDENT_IDS_COL})){
                    $studentIds = explode(',',$ExamData->{cn::EXAM_TABLE_STUDENT_IDS_COL});
                    foreach($studentIds as $studentKey => $studentIds){
                        if($studentId == $studentIds){
                            // Get correct answer detail
                            $AttemptExamData = AttemptExams::where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentIds)->where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)->first();
                            if(isset($AttemptExamData) && !empty($AttemptExamData)){
                                $StudentDetail = User::find($studentIds);
                                $students[$studentKey]['student_id'] = $StudentDetail->{cn::USERS_ID_COL};
                                $students[$studentKey]['student_grade'] = $StudentDetail->CurriculumYearGradeId ?? 0;
                                $students[$studentKey]['student_number'] = $StudentDetail->{cn::USERS_ID_COL};
                                $students[$studentKey]['student_name'] = $StudentDetail->{cn::USERS_NAME_EN_COL};
                                $students[$studentKey]['student_status'] = 'Active';
                                $students[$studentKey]['countStudent'] = (++$studentCount);
                                $students[$studentKey]['total_correct_answer'] = $AttemptExamData->{cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS};
                                $students[$studentKey]['total_wrong_answers'] = $AttemptExamData->{cn::ATTEMPT_EXAMS_TOTAL_WRONG_ANSWERS};
                                $students[$studentKey]['exam_status'] = (($AttemptExamData->{cn::ATTEMPT_EXAMS_STATUS_COL}) && $AttemptExamData->{cn::ATTEMPT_EXAMS_STATUS_COL} == 1) ? 'Complete' : 'Pending';
                                if(!empty($ExamData->{cn::EXAM_TABLE_QUESTION_IDS_COL})){
                                    $questionIds = explode(',',$ExamData->{cn::EXAM_TABLE_QUESTION_IDS_COL});
                                    $QuestionList = Question::with(['answers'])->whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)->get();
                                    if(isset($QuestionList) && !empty($QuestionList)){
                                        $totalQuestions = count($QuestionList);
                                        $students[$studentKey]['countQuestions'] = count($QuestionList);
                                        foreach($QuestionList as $questionKey => $question){
                                            $countanswer = [];
                                            if(isset($question)){
                                                // Store question natural ability and normalized ability
                                                $difficultiesValue = $this->GetDifficultiesValueByCalibrationId($ExamData->{cn::EXAM_CALIBRATION_ID_COL},$question->id);
                                                $difficultValue = [
                                                    'natural_difficulty' => $difficultiesValue ?? '',
                                                    'normalized_difficulty' => $this->getNormalizedAbility($difficultiesValue)
                                                ];
                                                $Questions[$questionKey]['difficultyValue'] = $difficultValue ?? [];

                                                //For display in Question Difficulty in percent in result
                                                if($question->dificulaty_level == 1){
                                                    $totalQuestionDifficulty['Level1'] =  $totalQuestionDifficulty['Level1'] + 1;
                                                }else if($question->dificulaty_level == 2){
                                                    $totalQuestionDifficulty['Level2'] =  $totalQuestionDifficulty['Level2'] + 1;
                                                }else if($question->dificulaty_level == 3){
                                                    $totalQuestionDifficulty['Level3'] =  $totalQuestionDifficulty['Level3'] + 1;
                                                }else if($question->dificulaty_level == 4){
                                                    $totalQuestionDifficulty['Level4'] =  $totalQuestionDifficulty['Level4'] + 1;
                                                }else if($question->dificulaty_level == 5){
                                                    $totalQuestionDifficulty['Level5'] =  $totalQuestionDifficulty['Level5'] + 1;
                                                }

                                                if(isset($AttemptExamData[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                    $filterattempQuestionAnswer = array_filter(json_decode($AttemptExamData[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL]), function ($var) use($question){
                                                        if($var->question_id == $question[cn::QUESTION_TABLE_ID_COL]){
                                                            return $var ?? [];
                                                        }
                                                    });
                                                }
                                            }
                                            if(isset($filterattempQuestionAnswer) && !empty($filterattempQuestionAnswer)){
                                                foreach($filterattempQuestionAnswer as $fanswer){
                                                    if($fanswer->answer == $question->answers->{'correct_answer_'.$fanswer->language}){
                                                        $students[$studentKey]['Q'.(++$questionKey)] = 'true';
                                                        $CountStudentAnswer[$questionKey] = (($CountStudentAnswer[$questionKey] ?? 0) + 1);

                                                        //For display in Question Difficulty in percent in Restult
                                                        if($question->dificulaty_level == 1){
                                                            $totalQuestionDifficulty['correct_Level1'] = $totalQuestionDifficulty['correct_Level1'] + 1;
                                                        }else if($question->dificulaty_level == 2){
                                                            $totalQuestionDifficulty['correct_Level2'] = $totalQuestionDifficulty['correct_Level2'] + 1;
                                                        }else if($question->dificulaty_level == 3){
                                                            $totalQuestionDifficulty['correct_Level3'] = $totalQuestionDifficulty['correct_Level3'] + 1;
                                                        }else if($question->dificulaty_level == 4){
                                                            $totalQuestionDifficulty['correct_Level4'] = $totalQuestionDifficulty['correct_Level4'] + 1;
                                                        }else if($question->dificulaty_level == 5){
                                                            $totalQuestionDifficulty['correct_Level5'] = $totalQuestionDifficulty['correct_Level5'] + 1;
                                                        }
                                                    }else{
                                                        $answers_node_id_check = $question->answers->{'answer'.$fanswer->answer.'_node_relation_id_'.$fanswer->language};
                                                        if($answers_node_id_check != ""){
                                                            if(array_key_exists($answers_node_id_check,$AllWeakness)){
                                                                $AllWeakness[$answers_node_id_check] = $AllWeakness[$answers_node_id_check]+1;
                                                            }else{
                                                                $AllWeakness[$answers_node_id_check]=1;   
                                                            }
                                                        }else{
                                                            //Manoj Changes
                                                            $arrayOfQuestion = explode('-',$question[cn::QUESTION_QUESTION_CODE_COL]);
                                                            if(count($arrayOfQuestion) == 8){
                                                                unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                                                                $newQuestionCode = implode('-',$arrayOfQuestion);
                                                                $newQuestionData = Question::with('answers')->where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                                                                if(isset($newQuestionData->answers) && !empty($newQuestionData->answers)){
                                                                    $answers_node_id_check = $newQuestionData->answers->{'answer'.$fanswer->answer.'_node_relation_id_en'};
                                                                    if($answers_node_id_check != ""){
                                                                        if(array_key_exists($answers_node_id_check,$AllWeakness)){
                                                                            $AllWeakness[$answers_node_id_check] = $AllWeakness[$answers_node_id_check]+1;
                                                                        }else{
                                                                            $AllWeakness[$answers_node_id_check] = 1;   
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        $students[$studentKey]['Q'.(++$questionKey)] = 'false';
                                                        $CountStudentAnswer[$questionKey] = ($CountStudentAnswer[$questionKey] ?? 0);
                                                    }
                                                }
                                            }else{
                                                $students[$studentKey]['Q'.(++$questionKey)] = 'false';
                                                $CountStudentAnswer[$questionKey] = 0;
                                            }
                                            // Store exams skill array
                                            $QuestionSkills[$questionKey] = $question->{cn::QUESTION_E_COL};
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if(!empty($totalQuestions)){
                for($i=1; $i <= $totalQuestions; ++$i){
                    if(isset($CountStudentAnswer[$i]) && !empty($CountStudentAnswer[$i])){
                        $answerPercentage[$i] = round(((100 * $CountStudentAnswer[$i]) / $studentCount), 2).'%';
                    }else{
                        $answerPercentage[$i] = '0%';
                    }
                }
            }
            arsort($AllWeakness);
            $ResultList['stundet_correct_answer'] = $CountStudentAnswer;
            $ResultList['students'] = $students;
            $ResultList['percentage_rate_correct_answer'] = $answerPercentage;
            $ResultList['skills'] = $QuestionSkills;

            /**************************/
            //$percentageOfAnswer = $this->getPercentageOfSelectedAnswer($examId,$isGroupId);
            $percentageOfAnswer = $this->getClassPerformancePercentageOfSelectedAnswer($request);
            $percentageOfAnswerSchool = $this->getPercentageOfSelectedAnswerSchool($request);
            //$percentageOfAnswerAllSchool = $this->getPercentageOfSelectedAnswerALLSchool($examId);
            $percentageOfAnswerAllSchool = $this->getPercentageOfBasedOnAllUsedQuestionAnswerAllTest($examId);

            $AttemptExamData = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentId)->first();
            $nodeList = Nodes::where(cn::NODES_STATUS_COL,'active')->get();
            $nodeWeaknessList = array();
            $nodeWeaknessListCh = array();
            if (!empty($nodeList)) { 
                $nodeListToArray = $nodeList->toArray();
                $nodeWeaknessList = array_column($nodeListToArray,cn::NODES_WEAKNESS_NAME_EN_COL,cn::NODES_NODE_ID_COL);
                $nodeWeaknessListCh = array_column($nodeListToArray,cn::NODES_WEAKNESS_NAME_CH_COL,cn::NODES_NODE_ID_COL);
            }
            if(!empty($AttemptExamData)){
                // Get Percentage of difficulty level
                $questionDifficultyGraph = $this->GetPercentageQuestionDifficultyLevel($totalQuestionDifficulty);
                $result['html'] = (string)View::make('backend.reports.admin_report_result_show',compact('difficultyLevels','examType','nodeWeaknessList','nodeWeaknessListCh',
                'Questions','AttemptExamData','ExamData','percentageOfAnswer','AllWeakness','questionDifficultyGraph','PerQuestionSpeed','percentageOfAnswerSchool',
                'percentageOfAnswerAllSchool','studentId'));
                return $this->sendResponse($result);
            }
        } catch (Exception $ex) {
            return back()->withError($ex->getMessage());
        }
    }

    /**
     * USE : Get using AjaxResult for exams
     */
    public function getAjaxExamSingleResult(Request $request, $examId, $studentId = 0){
        try {
            $totalQuestionDifficulty = ['Level1' => 0,'Level2' => 0,'Level3' => 0,'Level4' => 0,'Level5' => 0,'correct_Level1' => 0,'correct_Level2' => 0,'correct_Level3' => 0,'correct_Level4' => 0,'correct_Level5' => 0];
            // $difficultyLevels = PreConfigurationDiffiltyLevel::all();
            $difficultyLevels = PreConfigurationDiffiltyLevel::get();
            $arrayOfExams = explode(',',$examId);
            // Get the per question speed to attempt student
            $PerQuestionSpeed = Helper::getQuestionPerSpeed($examId, $studentId);
            $examType = 'singleTest';
            $studentId = ($studentId) ? $studentId : Auth::user()->{cn::USERS_ID_COL};
            $ExamData = Exam::find($examId);
            if(isset($ExamData)){
                $Questions = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,explode(',',$ExamData->{cn::EXAM_TABLE_QUESTION_IDS_COL}))->get();
            }
            $ResultList = [];
            $SchoolList = School::all();
            $GradeList = Grades::all();
            $ExamList = Exam::all();
            $QuestionSkills = [];
            $studentCount = 0;
            $totalQuestions = 0;
            $students = [];
            $answerPercentage = [];
            $CountStudentAnswer = [];
            $AllWeakness=[];
            /*****************************/
            if(!empty($ExamData)){
                $ResultList['examDetails'] = $ExamData->toArray();
                if(!empty($ExamData->{cn::EXAM_TABLE_STUDENT_IDS_COL})){
                    $studentIds = explode(',',$ExamData->{cn::EXAM_TABLE_STUDENT_IDS_COL});
                    foreach($studentIds as $studentKey => $studentIds){
                        // Get correct answer detail
                        $AttemptExamData = AttemptExams::where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentIds)->where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)->first();
                        if(isset($AttemptExamData) && !empty($AttemptExamData)){
                            $StudentDetail = User::find($studentIds);
                            $students[$studentKey]['student_id'] = $StudentDetail->{cn::USERS_ID_COL};
                            //$students[$studentKey]['student_grade'] = $StudentDetail->{cn::USERS_GRADE_ID_COL} ?? 0;
                            $students[$studentKey]['student_grade'] = $StudentDetail->CurriculumYearGradeId ?? 0;
                            $students[$studentKey]['student_number'] = $StudentDetail->{cn::USERS_ID_COL};
                            $students[$studentKey]['student_name'] = $StudentDetail->{cn::USERS_NAME_EN_COL};
                            $students[$studentKey]['student_status'] = 'Active';
                            $students[$studentKey]['countStudent'] = (++$studentCount);
                            $students[$studentKey]['total_correct_answer'] = $AttemptExamData->{cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS};
                            $students[$studentKey]['total_wrong_answers'] = $AttemptExamData->{cn::ATTEMPT_EXAMS_TOTAL_WRONG_ANSWERS};
                            $students[$studentKey]['exam_status'] = (($AttemptExamData->{cn::ATTEMPT_EXAMS_STATUS_COL}) && $AttemptExamData->{cn::ATTEMPT_EXAMS_STATUS_COL} == 1) ? 'Complete' : 'Pending';
                            if(!empty($ExamData->{cn::EXAM_TABLE_QUESTION_IDS_COL})){
                                $questionIds = explode(',',$ExamData->{cn::EXAM_TABLE_QUESTION_IDS_COL});
                                $QuestionList = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)->get();
                                if(isset($QuestionList) && !empty($QuestionList)){
                                    $totalQuestions = count($QuestionList);
                                    $students[$studentKey]['countQuestions'] = count($QuestionList);
                                    foreach($QuestionList as $questionKey => $question){
                                        $countanswer = [];
                                        if(isset($question)){
                                            $difficultiesValue = $this->GetDifficultiesValueByCalibrationId($ExamData->{cn::EXAM_CALIBRATION_ID_COL},$question->id);
                                            $difficultValue = [
                                                'natural_difficulty' => $difficultiesValue ?? '',
                                                'normalized_difficulty' => $this->getNormalizedAbility($difficultiesValue)
                                            ];
                                            $Questions[$questionKey]['difficultyValue'] = $difficultValue ?? [];

                                            //For display in Question Difficulty in percent in Restult
                                            if($question->dificulaty_level == 1){
                                                $totalQuestionDifficulty['Level1'] =  $totalQuestionDifficulty['Level1'] + 1;
                                            }else if($question->dificulaty_level == 2){
                                                $totalQuestionDifficulty['Level2'] =  $totalQuestionDifficulty['Level2'] + 1;
                                            }else if($question->dificulaty_level == 3){
                                                $totalQuestionDifficulty['Level3'] =  $totalQuestionDifficulty['Level3'] + 1;
                                            }else if($question->dificulaty_level == 4){
                                                $totalQuestionDifficulty['Level4'] =  $totalQuestionDifficulty['Level4'] + 1;
                                            }else if($question->dificulaty_level == 5){
                                                $totalQuestionDifficulty['Level5'] =  $totalQuestionDifficulty['Level5'] + 1;
                                            }

                                            if(isset($AttemptExamData[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                $filterattempQuestionAnswer = array_filter(json_decode($AttemptExamData[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL]), function ($var) use($question){
                                                    if($var->question_id == $question[cn::QUESTION_TABLE_ID_COL]){
                                                        return $var ?? [];
                                                    }
                                                });
                                            }
                                        }
                                        if(isset($filterattempQuestionAnswer) && !empty($filterattempQuestionAnswer)){
                                            foreach($filterattempQuestionAnswer as $fanswer){
                                                if($fanswer->answer == $question->answers->{'correct_answer_'.$fanswer->language}){
                                                    $students[$studentKey]['Q'.(++$questionKey)] = 'true';
                                                    $CountStudentAnswer[$questionKey] = (($CountStudentAnswer[$questionKey] ?? 0) + 1);

                                                    //For display in Question Difficulty in percent in Restult
                                                    if($question->dificulaty_level == 1){
                                                        $totalQuestionDifficulty['correct_Level1'] = $totalQuestionDifficulty['correct_Level1'] + 1;
                                                    }else if($question->dificulaty_level == 2){
                                                        $totalQuestionDifficulty['correct_Level2'] = $totalQuestionDifficulty['correct_Level2'] + 1;
                                                    }else if($question->dificulaty_level == 3){
                                                        $totalQuestionDifficulty['correct_Level3'] = $totalQuestionDifficulty['correct_Level3'] + 1;
                                                    }else if($question->dificulaty_level == 4){
                                                        $totalQuestionDifficulty['correct_Level4'] = $totalQuestionDifficulty['correct_Level4'] + 1;
                                                    }else if($question->dificulaty_level == 5){
                                                        $totalQuestionDifficulty['correct_Level5'] = $totalQuestionDifficulty['correct_Level5'] + 1;
                                                    }

                                                }else{
                                                    $answers_node_id_check = $question->answers->{'answer'.$fanswer->answer.'_node_relation_id_'.$fanswer->language};
                                                    if($answers_node_id_check != ""){
                                                        if(array_key_exists($answers_node_id_check,$AllWeakness)){
                                                            $AllWeakness[$answers_node_id_check] = $AllWeakness[$answers_node_id_check]+1;
                                                        }else{
                                                            $AllWeakness[$answers_node_id_check]=1;   
                                                        }
                                                    }else{
                                                        $arrayOfQuestion = explode('-',$question[cn::QUESTION_QUESTION_CODE_COL]);
                                                        if(count($arrayOfQuestion) == 8){
                                                            unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                                                            $newQuestionCode = implode('-',$arrayOfQuestion);
                                                            $newQuestionData = Question::with('answers')->where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                                                            if(isset($newQuestionData->answers) && !empty($newQuestionData->answers)){
                                                                $answers_node_id_check = $newQuestionData->answers->{'answer'.$fanswer->answer.'_node_relation_id_en'};
                                                                if($answers_node_id_check != ""){
                                                                    if(array_key_exists($answers_node_id_check,$AllWeakness)){
                                                                        $AllWeakness[$answers_node_id_check] = $AllWeakness[$answers_node_id_check]+1;
                                                                    }else{
                                                                        $AllWeakness[$answers_node_id_check] = 1;   
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    $students[$studentKey]['Q'.(++$questionKey)] = 'false';
                                                    $CountStudentAnswer[$questionKey] = ($CountStudentAnswer[$questionKey] ?? 0);
                                                }
                                            }
                                        }else{
                                            $students[$studentKey]['Q'.(++$questionKey)] = 'false';
                                            $CountStudentAnswer[$questionKey] = 0;
                                        }
                                        // Store exams skill array
                                        $QuestionSkills[$questionKey] = $question->{cn::QUESTION_E_COL};
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if(!empty($totalQuestions)){
                for($i=1; $i <= $totalQuestions; ++$i){
                    if(isset($CountStudentAnswer[$i]) && !empty($CountStudentAnswer[$i])){
                        $answerPercentage[$i] = round(((100 * $CountStudentAnswer[$i]) / $studentCount), 2).'%';
                    }else{
                        $answerPercentage[$i] = '0%';
                    }
                }
            }
            arsort($AllWeakness);
            $ResultList['stundet_correct_answer'] = $CountStudentAnswer;
            $ResultList['students'] = $students;
            $ResultList['percentage_rate_correct_answer'] = $answerPercentage;
            $ResultList['skills'] = $QuestionSkills;

            /**************************/
            $percentageOfAnswer = $this->getPercentageOfSelectedAnswer($examId);
            $AttemptExamData = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentId)->first();
            $nodeList = Nodes::where(cn::NODES_STATUS_COL,'active')->get();
            $nodeWeaknessList = array();
            $nodeWeaknessListCh = array();
            if (!empty($nodeList)) { 
                $nodeListToArray = $nodeList->toArray();
                $nodeWeaknessList = array_column($nodeListToArray,cn::NODES_WEAKNESS_NAME_EN_COL,cn::NODES_NODE_ID_COL);
                $nodeWeaknessListCh = array_column($nodeListToArray,cn::NODES_WEAKNESS_NAME_CH_COL,cn::NODES_NODE_ID_COL);
            }
            if(!empty($AttemptExamData)){
                // Get Percentage of difficulty level
                $questionDifficultyGraph = $this->GetPercentageQuestionDifficultyLevel($totalQuestionDifficulty);
                $result['html'] = (string)View::make('backend.reports.student_report_result_show',compact('difficultyLevels','examType','nodeWeaknessList','nodeWeaknessListCh','Questions','AttemptExamData','ExamData','percentageOfAnswer','AllWeakness','questionDifficultyGraph','PerQuestionSpeed'));
                return $this->sendResponse($result);
            }
        } catch (Exception $ex) {
            return back()->withError($ex->getMessage());
        }
    }

    /**
     * USE : Get Result for exams
     */
    public function getExamResult(Request $request, $examId, $studentId = 0){
        try {
            $totalQuestionDifficulty = ['Level1' => 0,'Level2' => 0,'Level3' => 0,'Level4' => 0,'Level5' => 0,'correct_Level1' => 0,'correct_Level2' => 0,'correct_Level3' => 0,'correct_Level4' => 0,'correct_Level5' => 0];
            //$difficultyLevels = PreConfigurationDiffiltyLevel::all();
            $difficultyLevels = PreConfigurationDiffiltyLevel::get();
            $speed = 0;
            $arrayOfExams = explode(',',$examId);
            $studentId = ($studentId) ? $studentId : Auth::user()->{cn::USERS_ID_COL};
            $ExamData = Exam::find($examId);
            if(date('Y-m-d',strtotime($ExamData->result_date)) <= date('Y-m-d')){
            }else{
                return back()->with('error_msg', __('languages.result_not_declared_message'));
            }
            $isSelfLearningExam = ($ExamData->{cn::EXAM_TYPE_COLS} == '1') ? true : false;
            $isExerciseExam = ($ExamData->{cn::EXAM_TYPE_COLS} == '2') ? true : false;
            $isTestExam = ($ExamData->{cn::EXAM_TYPE_COLS} == '3') ? true : false;
            $isSelfLearningExercise = ($ExamData->{cn::EXAM_TYPE_COLS} == 1 && $ExamData->{cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL} == 1) ? true : false;
            $isSelfLearningTestingZone = ($ExamData->{cn::EXAM_TYPE_COLS} == 1 && $ExamData->{cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL} == 2) ? true : false;
            if(isset($ExamData)){
                $Questions = Question::with(['answers'])
                            ->whereIn(cn::QUESTION_TABLE_ID_COL,explode(',',$ExamData->{cn::EXAM_TABLE_QUESTION_IDS_COL}))
                            ->orderByRaw('FIELD(id,'.$ExamData->question_ids.')')
                            ->get();
                if(isset($Questions) && !empty($Questions)){
                    foreach($Questions as $QueKey => $QuestionsVal){
                        $difficultiesValue = $this->GetDifficultiesValueByCalibrationId($ExamData->{cn::EXAM_CALIBRATION_ID_COL},$QuestionsVal->id);
                        $difficultValue = [
                            'natural_difficulty' => $difficultiesValue ?? '',
                            'normalized_difficulty' => $this->getNormalizedAbility($difficultiesValue)
                        ];
                        $Questions[$QueKey]['difficultyValue'] = $difficultValue ?? [];
                    }
                }
            }
            $ResultList = [];
            $SchoolList = School::all();
            $GradeList = Grades::all();
            $QuestionSkills = [];
            $studentCount = 0;
            $totalQuestions = 0;
            $students = [];
            $answerPercentage = [];
            $CountStudentAnswer = [];
            $AllWeakness = [];
            $apiData = [];
            /*****************************/
            $studentOverAllPercentile = $this->GetStudentPercentileRank($examId, $studentId);
            if(!empty($ExamData)){
                $ResultList['examDetails'] = $ExamData->toArray();
                if(!empty($ExamData->{cn::EXAM_TABLE_STUDENT_IDS_COL})){
                    $studentIds = explode(',',$ExamData->{cn::EXAM_TABLE_STUDENT_IDS_COL});
                    foreach($studentIds as $studentKey => $studentIds){
                        if(Auth::user()->{cn::USERS_ID_COL} == $studentIds){
                            // Get correct answer detail
                            $AttemptExamData = AttemptExams::where([
                                                cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID => $studentIds,
                                                cn::ATTEMPT_EXAMS_EXAM_ID => $examId
                                            ])->first();
                            if(isset($AttemptExamData) && !empty($AttemptExamData)){
                                $StudentDetail = User::find($studentIds);
                                $students[$studentKey]['student_id'] = $StudentDetail->{cn::USERS_ID_COL};
                                //$students[$studentKey]['student_grade'] = $StudentDetail->{cn::USERS_GRADE_ID_COL} ?? 0;
                                $students[$studentKey]['student_grade'] = $StudentDetail->CurriculumYearGradeId;
                                $students[$studentKey]['student_number'] = $StudentDetail->{cn::USERS_ID_COL};
                                $students[$studentKey]['student_name'] = $StudentDetail->{cn::USERS_NAME_EN_COL};
                                $students[$studentKey]['student_status'] = 'Active';
                                $students[$studentKey]['countStudent'] = (++$studentCount);
                                $students[$studentKey]['total_correct_answer'] = $AttemptExamData->{cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS};
                                $students[$studentKey]['total_wrong_answers'] = $AttemptExamData->{cn::ATTEMPT_EXAMS_TOTAL_WRONG_ANSWERS};
                                $students[$studentKey]['exam_status'] = (($AttemptExamData->{cn::ATTEMPT_EXAMS_STATUS_COL}) && $AttemptExamData->{cn::ATTEMPT_EXAMS_STATUS_COL} == 1) ? 'Complete' : 'Pending';
                                if(!empty($ExamData->{cn::EXAM_TABLE_QUESTION_IDS_COL})){
                                    $questionIds = explode(',',$ExamData->{cn::EXAM_TABLE_QUESTION_IDS_COL});
                                    //$QuestionList = Question::with(['answers','PreConfigurationDifficultyLevel'])->whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)->get();
                                    $QuestionList = Question::with(['answers'])->whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)->get();
                                    if(isset($QuestionList) && !empty($QuestionList)){
                                        $totalQuestions = count($QuestionList);
                                        $students[$studentKey]['countQuestions'] = count($QuestionList);
                                        foreach($QuestionList as $questionKey => $question){
                                            // Get Questions Answers and difficulty level

                                            $responseData = $this->GetQuestionNumOfAnswerAndDifficultyValue($question->id,$ExamData->{cn::EXAM_CALIBRATION_ID_COL});
                                            $apiData['num_of_ans_list'][] = $responseData['noOfAnswers'];
                                            $apiData['difficulty_list'][] = $responseData['difficulty_value'];
                                            $apiData['max_student_num'] = 1;

                                            $countanswer = [];
                                            if(isset($question)){
                                                //For display in Question Difficulty in percent in Restult
                                                if($question->dificulaty_level == 1){
                                                    $totalQuestionDifficulty['Level1'] =  $totalQuestionDifficulty['Level1'] + 1;
                                                }else if($question->dificulaty_level == 2){
                                                    $totalQuestionDifficulty['Level2'] =  $totalQuestionDifficulty['Level2'] + 1;
                                                }else if($question->dificulaty_level == 3){
                                                    $totalQuestionDifficulty['Level3'] =  $totalQuestionDifficulty['Level3'] + 1;
                                                }else if($question->dificulaty_level == 4){
                                                    $totalQuestionDifficulty['Level4'] =  $totalQuestionDifficulty['Level4'] + 1;
                                                }else if($question->dificulaty_level == 5){
                                                    $totalQuestionDifficulty['Level5'] =  $totalQuestionDifficulty['Level5'] + 1;
                                                }
                                                if(isset($AttemptExamData[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                    $filterattempQuestionAnswer = array_filter(json_decode($AttemptExamData[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL]), function ($var) use($question){
                                                        if($var->question_id == $question[cn::QUESTION_TABLE_ID_COL]){
                                                            return $var ?? [];
                                                        }
                                                    });
                                                }
                                            }
                                            if(isset($filterattempQuestionAnswer) && !empty($filterattempQuestionAnswer)){
                                                foreach($filterattempQuestionAnswer as $fanswer){
                                                    if($fanswer->answer == $question->answers->{'correct_answer_'.$fanswer->language}){
                                                        //For display in Question Difficulty in percent in Restult
                                                        if($question->dificulaty_level == 1){
                                                            $totalQuestionDifficulty['correct_Level1'] = $totalQuestionDifficulty['correct_Level1'] + 1;
                                                        }else if($question->dificulaty_level == 2){
                                                            $totalQuestionDifficulty['correct_Level2'] = $totalQuestionDifficulty['correct_Level2'] + 1;
                                                        }else if($question->dificulaty_level == 3){
                                                            $totalQuestionDifficulty['correct_Level3'] = $totalQuestionDifficulty['correct_Level3'] + 1;
                                                        }else if($question->dificulaty_level == 4){
                                                            $totalQuestionDifficulty['correct_Level4'] = $totalQuestionDifficulty['correct_Level4'] + 1;
                                                        }else if($question->dificulaty_level == 5){
                                                            $totalQuestionDifficulty['correct_Level5'] = $totalQuestionDifficulty['correct_Level5'] + 1;
                                                        }
                                                        $students[$studentKey]['Q'.(++$questionKey)] = 'true';
                                                        $CountStudentAnswer[$questionKey] = (($CountStudentAnswer[$questionKey] ?? 0) + 1);
                                                        $apiData['questions_results'][] = true;
                                                    }else{
                                                        $answers_node_id_check = $question->answers->{'answer'.$fanswer->answer.'_node_relation_id_'.$fanswer->language};
                                                        if($answers_node_id_check != ""){
                                                            if(array_key_exists($answers_node_id_check,$AllWeakness)){
                                                                $AllWeakness[$answers_node_id_check] = $AllWeakness[$answers_node_id_check]+1;
                                                            }else{
                                                                $AllWeakness[$answers_node_id_check] = 1;   
                                                            }
                                                        }else{
                                                            $arrayOfQuestion = explode('-',$question[cn::QUESTION_QUESTION_CODE_COL]);
                                                            if(count($arrayOfQuestion) == 8){
                                                                unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                                                                $newQuestionCode = implode('-',$arrayOfQuestion);
                                                                $newQuestionData = Question::with('answers')->where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                                                                if(isset($newQuestionData->answers) && !empty($newQuestionData->answers)){
                                                                    $answers_node_id_check = $newQuestionData->answers->{'answer'.$fanswer->answer.'_node_relation_id_en'};
                                                                    if($answers_node_id_check != ""){
                                                                        if(array_key_exists($answers_node_id_check,$AllWeakness)){
                                                                            $AllWeakness[$answers_node_id_check] = $AllWeakness[$answers_node_id_check]+1;
                                                                        }else{
                                                                            $AllWeakness[$answers_node_id_check] = 1;   
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        $students[$studentKey]['Q'.(++$questionKey)] = 'false';
                                                        $CountStudentAnswer[$questionKey] = ($CountStudentAnswer[$questionKey] ?? 0);
                                                        $apiData['questions_results'][] = false;
                                                    }
                                                }
                                            }else{
                                                $students[$studentKey]['Q'.(++$questionKey)] = 'false';
                                                $CountStudentAnswer[$questionKey] = 0;
                                                $apiData['questions_results'][] = false;
                                            }
                                            // Store exams skill array
                                            $QuestionSkills[$questionKey] = $question->{cn::QUESTION_E_COL};
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }            
            /**************************/
            //$percentageOfAnswer = $this->getPercentageOfSelectedAnswer($examId);
            $percentageOfAnswer = $this->getPercentageOfBasedOnAllUsedQuestionAnswerAllTest($examId);
            $AttemptExamData =  AttemptExams::where([
                                    cn::ATTEMPT_EXAMS_EXAM_ID => $examId,
                                    cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID => $studentId
                                ])->first();
            $nodeList = Nodes::where(cn::NODES_STATUS_COL,'active')->get();
            $nodeWeaknessList = array();
            $nodeWeaknessListCh = array();
            if(!empty($nodeList)){
                $nodeListToArray = $nodeList->toArray();
                $nodeWeaknessList = array_column($nodeListToArray,cn::NODES_WEAKNESS_NAME_EN_COL,cn::NODES_NODE_ID_COL);
                $nodeWeaknessListCh = array_column($nodeListToArray,cn::NODES_WEAKNESS_NAME_CH_COL,cn::NODES_NODE_ID_COL);
            }
            if(!empty($AttemptExamData)){
                // Get Percentage of difficulty level
                $questionDifficultyGraph = $this->GetPercentageQuestionDifficultyLevel($totalQuestionDifficulty);
                $this->UserActivityLog(
                    Auth::user()->{cn::USERS_ID_COL},
                    '<p>'.Auth::user()->DecryptNameEn.' '.__('activity_history.has_view_report').'.'.'</p>'.
                    '<p>'.__('activity_history.test_type').$this->ActivityTestType($ExamData).'</p>'.
                    '<p>'.__('activity_history.report_type').__('activity_history.progress_report').'</p>'.
                    '<p>'.__('activity_history.title_is').$ExamData->title.'.'.'</p>'.
                    '<p>'.__('activity_history.exam_reference_is').$ExamData->reference_no.'</p>'
                );
                return view('backend.exams.exams_result',compact('studentOverAllPercentile','isExerciseExam','isTestExam','difficultyLevels','isSelfLearningExam','isSelfLearningExercise',
                'isSelfLearningTestingZone','studentId','nodeWeaknessList','nodeWeaknessListCh','Questions','AttemptExamData','ExamData','percentageOfAnswer',
                'AllWeakness','questionDifficultyGraph'));
                
            }
        } catch (Exception $ex) {
            return back()->withError($ex->getMessage());
        }
    }

    public function getSecondTrialResult(Request $request,$examId,$studentId){
        $ExamData = Exam::find($examId);
        $difficultyLevels = PreConfigurationDiffiltyLevel::get();
        $isSelfLearningExam = ($ExamData->{cn::EXAM_TYPE_COLS} == '1') ? true : false;
        $percentageOfAnswer = $this->getPercentageOfSelectedAnswer($examId);
        $AttemptExamData =  AttemptExams::where([
            cn::ATTEMPT_EXAMS_EXAM_ID => $examId,
            cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID => $studentId
        ])->first();
        $secondAttemptQuestionIdsData =json_decode($AttemptExamData->attempt_second_trial,true);
        $questionIds = array_column($secondAttemptQuestionIdsData,'question_id');

        $Questions = Question::with(['answers'])->whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)->get();
        if(isset($Questions) && !empty($Questions)){
            foreach($Questions as $QueKey => $QuestionsVal){
                $difficultiesValue = $this->GetDifficultiesValueByCalibrationId($ExamData->{cn::EXAM_CALIBRATION_ID_COL},$QuestionsVal->id);
                $difficultValue = [
                    'natural_difficulty' => $difficultiesValue ?? '',
                    'normalized_difficulty' => $this->getNormalizedAbility($difficultiesValue)
                ];
                $Questions[$QueKey]['difficultyValue'] = $difficultValue ?? [];
            }
        }
        if(!empty($AttemptExamData)){
            // echo "<pre>";print_r($ExamData->toArray());
            // echo "<pre>";print_r($AttemptExamData->toArray());die;
            return (string)View::make('backend.exams.student_result_progress',compact('difficultyLevels','studentId','Questions','AttemptExamData','ExamData','percentageOfAnswer'));
        }
    }
    /**
     * USE : Get Admin Result for exams
     */
    public function getAdminExamResult(Request $request, $examId, $studentId = 0){ 
        try {
            $totalQuestionDifficulty = ['Level1' => 0,'Level2' => 0,'Level3' => 0,'Level4' => 0,'Level5' => 0,'correct_Level1' => 0,'correct_Level2' => 0,'correct_Level3' => 0,'correct_Level4' => 0,'correct_Level5' => 0];
            // $difficultyLevels = PreConfigurationDiffiltyLevel::all();
            $difficultyLevels = PreConfigurationDiffiltyLevel::get();
            $speed = 0;
            $arrayOfExams = explode(',',$examId);
            $ExamData = Exam::find($examId);
            $isSelfLearningExam = ($ExamData->{cn::EXAM_TYPE_COLS} == '1') ? true : false;
            if(isset($ExamData)){
                $Questions = Question::with(['answers'])->whereIn(cn::QUESTION_TABLE_ID_COL,explode(',',$ExamData->{cn::EXAM_TABLE_QUESTION_IDS_COL}))->get();
                if(isset($Questions) && !empty($Questions)){
                    foreach($Questions as $QueKey => $QuestionsVal){
                        $difficultiesValue = $this->GetDifficultiesValueByCalibrationId($ExamData->{cn::EXAM_CALIBRATION_ID_COL},$QuestionsVal->id);
                        $difficultValue = [
                            'natural_difficulty' => $difficultiesValue ?? '',
                            'normalized_difficulty' => $this->getNormalizedAbility($difficultiesValue)
                        ];
                        $Questions[$QueKey]['difficultyValue'] = $difficultValue ?? [];
                    }
                }
            }

            $ResultList = [];
            $SchoolList = School::all();
            $GradeList = Grades::all();
            $ExamList = Exam::all();
            $QuestionSkills = [];
            $studentCount = 0;
            $totalQuestions = 0;
            $students = [];
            $answerPercentage = [];
            $CountStudentAnswer = [];
            $AllWeakness=[];
            $apiData = [];
            /*****************************/
            if(!empty($ExamData)){
                $ResultList['examDetails'] = $ExamData->toArray();
                if(!empty($ExamData->{cn::EXAM_TABLE_STUDENT_IDS_COL})){
                    $studentIds = explode(',',$ExamData->{cn::EXAM_TABLE_STUDENT_IDS_COL});
                    foreach($studentIds as $studentKey => $studentIds){
                        if($studentId == $studentIds){
                            // Get correct answer detail
                            $AttemptExamData = AttemptExams::where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentIds)->where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)->first();
                            if(isset($AttemptExamData) && !empty($AttemptExamData)){
                                $StudentDetail = User::find($studentIds);
                                $students[$studentKey]['student_id'] = $StudentDetail->{cn::USERS_ID_COL};
                                //$students[$studentKey]['student_grade'] = $StudentDetail->{cn::USERS_GRADE_ID_COL} ?? 0;
                                $students[$studentKey]['student_grade'] = $StudentDetail->CurriculumYearGradeId ?? 0;
                                $students[$studentKey]['student_number'] = $StudentDetail->CurriculumYearData->{cn::USERS_ID_COL} ?? 0;
                                $students[$studentKey]['student_name'] = $StudentDetail->{cn::USERS_NAME_EN_COL};
                                $students[$studentKey]['student_status'] = 'Active';
                                $students[$studentKey]['countStudent'] = (++$studentCount);
                                $students[$studentKey]['total_correct_answer'] = $AttemptExamData->{cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS};
                                $students[$studentKey]['total_wrong_answers'] = $AttemptExamData->{cn::ATTEMPT_EXAMS_TOTAL_WRONG_ANSWERS};
                                $students[$studentKey]['exam_status'] = (($AttemptExamData->{cn::ATTEMPT_EXAMS_STATUS_COL}) && $AttemptExamData->{cn::ATTEMPT_EXAMS_STATUS_COL} == 1) ? 'Complete' : 'Pending';
                                if(!empty($ExamData->{cn::EXAM_TABLE_QUESTION_IDS_COL})){
                                    $questionIds = explode(',',$ExamData->{cn::EXAM_TABLE_QUESTION_IDS_COL});
                                    //$QuestionList = Question::with(['answers','PreConfigurationDifficultyLevel'])->whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)->get();
                                    $QuestionList = Question::with(['answers'])->whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)->get();
                                    if(isset($QuestionList) && !empty($QuestionList)){
                                        $totalQuestions = count($QuestionList);
                                        $students[$studentKey]['countQuestions'] = count($QuestionList);
                                        foreach($QuestionList as $questionKey => $question){
                                            // Get Questions Answers and difficulty level
                                            $responseData = $this->GetQuestionNumOfAnswerAndDifficultyValue($question->id,$ExamData->{cn::EXAM_CALIBRATION_ID_COL});
                                            $apiData['num_of_ans_list'][] = $responseData['noOfAnswers'];
                                            $apiData['difficulty_list'][] = $responseData['difficulty_value'];
                                            $apiData['max_student_num'] = 1;

                                            $countanswer = [];
                                            if(isset($question)){
                                                //For display in Question Difficulty in percent in Restult
                                                if($question->dificulaty_level == 1){
                                                    $totalQuestionDifficulty['Level1'] =  $totalQuestionDifficulty['Level1'] + 1;
                                                }else if($question->dificulaty_level == 2){
                                                    $totalQuestionDifficulty['Level2'] =  $totalQuestionDifficulty['Level2'] + 1;
                                                }else if($question->dificulaty_level == 3){
                                                    $totalQuestionDifficulty['Level3'] =  $totalQuestionDifficulty['Level3'] + 1;
                                                }else if($question->dificulaty_level == 4){
                                                    $totalQuestionDifficulty['Level4'] =  $totalQuestionDifficulty['Level4'] + 1;
                                                }else if($question->dificulaty_level == 5){
                                                    $totalQuestionDifficulty['Level5'] =  $totalQuestionDifficulty['Level5'] + 1;
                                                }
                                                if(isset($AttemptExamData[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                    $filterattempQuestionAnswer = array_filter(json_decode($AttemptExamData[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL]), function ($var) use($question){
                                                        if($var->question_id == $question[cn::QUESTION_TABLE_ID_COL]){
                                                            return $var ?? [];
                                                        }
                                                    });
                                                }
                                            }
                                            if(isset($filterattempQuestionAnswer) && !empty($filterattempQuestionAnswer)){
                                                foreach($filterattempQuestionAnswer as $fanswer){
                                                    if($fanswer->answer == $question->answers->{'correct_answer_'.$fanswer->language}){
                                                        //For display in Question Difficulty in percent in Restult
                                                        if($question->dificulaty_level == 1){
                                                            $totalQuestionDifficulty['correct_Level1'] = $totalQuestionDifficulty['correct_Level1'] + 1;
                                                        }else if($question->dificulaty_level == 2){
                                                            $totalQuestionDifficulty['correct_Level2'] = $totalQuestionDifficulty['correct_Level2'] + 1;
                                                        }else if($question->dificulaty_level == 3){
                                                            $totalQuestionDifficulty['correct_Level3'] = $totalQuestionDifficulty['correct_Level3'] + 1;
                                                        }else if($question->dificulaty_level == 4){
                                                            $totalQuestionDifficulty['correct_Level4'] = $totalQuestionDifficulty['correct_Level4'] + 1;
                                                        }else if($question->dificulaty_level == 5){
                                                            $totalQuestionDifficulty['correct_Level5'] = $totalQuestionDifficulty['correct_Level5'] + 1;
                                                        }
                                                        $students[$studentKey]['Q'.(++$questionKey)] = 'true';
                                                        $CountStudentAnswer[$questionKey] = (($CountStudentAnswer[$questionKey] ?? 0) + 1);
                                                        $apiData['questions_results'][] = true;
                                                    }else{
                                                        $answers_node_id_check = $question->answers->{'answer'.$fanswer->answer.'_node_relation_id_'.$fanswer->language};
                                                        if($answers_node_id_check != ""){
                                                            if(array_key_exists($answers_node_id_check,$AllWeakness)){
                                                                $AllWeakness[$answers_node_id_check] = $AllWeakness[$answers_node_id_check]+1;
                                                            }else{
                                                                $AllWeakness[$answers_node_id_check] = 1;   
                                                            }
                                                        }else{
                                                            $arrayOfQuestion = explode('-',$question[cn::QUESTION_QUESTION_CODE_COL]);
                                                            if(count($arrayOfQuestion) == 8){
                                                                unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                                                                $newQuestionCode = implode('-',$arrayOfQuestion);
                                                                $newQuestionData = Question::with('answers')->where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                                                                if(isset($newQuestionData->answers) && !empty($newQuestionData->answers)){
                                                                    $answers_node_id_check = $newQuestionData->answers->{'answer'.$fanswer->answer.'_node_relation_id_en'};
                                                                    if($answers_node_id_check != ""){
                                                                        if(array_key_exists($answers_node_id_check,$AllWeakness)){
                                                                            $AllWeakness[$answers_node_id_check] = $AllWeakness[$answers_node_id_check]+1;
                                                                        }else{
                                                                            $AllWeakness[$answers_node_id_check] = 1;   
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        $students[$studentKey]['Q'.(++$questionKey)] = 'false';
                                                        $CountStudentAnswer[$questionKey] = ($CountStudentAnswer[$questionKey] ?? 0);
                                                        $apiData['questions_results'][] = false;
                                                    }
                                                }
                                            }else{
                                                $students[$studentKey]['Q'.(++$questionKey)] = 'false';
                                                $CountStudentAnswer[$questionKey] = 0;
                                                $apiData['questions_results'][] = false;
                                            }
                                            // Store exams skill array
                                            $QuestionSkills[$questionKey] = $question->{cn::QUESTION_E_COL};
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }            
            /**************************/
            $percentageOfAnswer = $this->getPercentageOfSelectedAnswer($examId);
            $AttemptExamData =  AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)
                                ->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentId)
                                ->first();
            $nodeList = Nodes::where(cn::NODES_STATUS_COL,'active')->get();
            $nodeWeaknessList = array();
            $nodeWeaknessListCh = array();
            if(!empty($nodeList)){
                $nodeListToArray = $nodeList->toArray();
                $nodeWeaknessList = array_column($nodeListToArray,cn::NODES_WEAKNESS_NAME_EN_COL,cn::NODES_NODE_ID_COL);
                $nodeWeaknessListCh = array_column($nodeListToArray,cn::NODES_WEAKNESS_NAME_CH_COL,cn::NODES_NODE_ID_COL);
            }
            if(!empty($AttemptExamData)){
                // Get Percentage of difficulty level
                $questionDifficultyGraph = $this->GetPercentageQuestionDifficultyLevel($totalQuestionDifficulty);
                // Get Page name
                $menuItem = '';
                if(isset($ExamData->id) && !empty($ExamData->id)){
                    $menuItem = $this->GetPageName($ExamData->id);
                }
                return view('backend.exams.admin_exams_result',compact('difficultyLevels','isSelfLearningExam','studentId','nodeWeaknessList','nodeWeaknessListCh','Questions','AttemptExamData','ExamData','percentageOfAnswer','AllWeakness','questionDifficultyGraph','menuItem'));
            }
        } catch (Exception $ex) {
            return back()->withError($ex->getMessage());
        }
    }

    /***
     * USE : Get a7 Count selected answer percentage of question
     */
    public function getPercentageOfSelectedAnswer($examId,$isGroupId=""){
        $data = [];
        $ExamDetails = Exam::find($examId);
        if(isset($ExamDetails)){
            if(!$this->isAdmin()){
                $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
                $studentidlist = User::where([
                                    cn::USERS_SCHOOL_ID_COL => $schoolId,
                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                                ])
                                ->pluck(cn::USERS_ID_COL)
                                ->toArray();
            }else{
                $studentidlist = User::where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->pluck(cn::USERS_ID_COL)->toArray();
            }
           
            if($this->isTeacherLogin()){
                $gradeClassId = array();
                $gradesListId = TeachersClassSubjectAssign::where([
                                    cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                    cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth::user()->{cn::USERS_ID_COL}
                                ])
                                ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                                ->toArray();
                $gradeClass =   TeachersClassSubjectAssign::where([
                                    cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                    cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth::user()->{cn::USERS_ID_COL}
                                ])
                                ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)
                                ->toArray();
                if(isset($gradeClass) && !empty($gradeClass)){
                    $gradeClass = implode(',', $gradeClass);
                    $gradeClassId = explode(',',$gradeClass);
                }
                // $studentidlist = User::whereIn(cn::USERS_GRADE_ID_COL,$gradesListId)
                //                 ->whereIn(cn::USERS_CLASS_ID_COL,$gradeClassId)
                //                 ->where(cn::USERS_SCHOOL_ID_COL,$schoolId)
                //                 ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                //                 ->pluck(cn::USERS_ID_COL)
                //                 ->toArray();
                $studentidlist = User::where([
                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                    cn::USERS_SCHOOL_ID_COL => $schoolId
                                ])
                                ->get()
                                ->whereIn('CurriculumYearGradeId',$gradesListId)
                                ->whereIn('CurriculumYearClassId',$gradeClassId)
                                ->pluck(cn::USERS_ID_COL)
                                ->toArray();
                if(isset($isGroupId) && !empty($isGroupId)){
                    $studentidlist =    PeerGroupMember::where([
                                            cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL => $isGroupId
                                        ])
                                        ->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL)
                                        ->unique()
                                        ->toArray();
                }
                $AttemptExamData = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)->whereIn(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentidlist)->get();
            }

            $Questions = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,explode(',',$ExamDetails->{cn::EXAM_TABLE_QUESTION_IDS_COL}))->get();
            if($this->isStudentLogin()){
                //$studentClassId = Auth::user()->{cn::USERS_GRADE_ID_COL};
                $studentGradeId = Auth::user()->CurriculumYearGradeId ?? Auth::user()->{cn::USERS_GRADE_ID_COL};
                $studentidlist = User::where(cn::USERS_SCHOOL_ID_COL,$schoolId)
                                    ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                    ->get()
                                    ->where('CurriculumYearGradeId',$studentGradeId)
                                    ->pluck(cn::USERS_ID_COL)
                                    ->toArray();
                $AttemptExamData = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)
                                    ->whereIn(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentidlist)
                                    ->get();
            }
            $AttemptExamData = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)->whereIntegerInRaw(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentidlist)->get();
            $noOfStudentAttemptExam = count($AttemptExamData) ?? 0;
            if(!empty($Questions)){
                foreach($Questions as $queKey => $question){
                    $data[$question->id][1] = 0;
                    $data[$question->id][2] = 0;
                    $data[$question->id][3] = 0;
                    $data[$question->id][4] = 0;
                    $data[$question->id][5] = 0;
                    if(!empty($question->answers)){
                        for($i=1; $i <= 5; $i++){
                            if(!empty($AttemptExamData)){
                                foreach($AttemptExamData as $attempExamKey => $attemptValue){
                                    if(!empty($attemptValue[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                        foreach(json_decode($attemptValue[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL]) as $answersData){
                                            if(($answersData->question_id == (int)$question->{cn::QUESTION_TABLE_ID_COL}) && ((int)$answersData->answer == $i)){
                                                $data[$question->{cn::QUESTION_TABLE_ID_COL}][$answersData->answer] = ($data[$question->{cn::QUESTION_TABLE_ID_COL}][$answersData->answer] + 1);
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
        $percentage = [];
        if(!empty($data)){
            foreach($data as $quesKey => $questionArray){
                foreach($questionArray as $answerKey => $answerData){
                    if($noOfStudentAttemptExam){
                        $percentage[$quesKey][$answerKey] = round(((100 * $answerData) / $noOfStudentAttemptExam), 2);
                    }else{
                        $percentage[$quesKey][$answerKey] = 0;
                    }
                }
            }
        }
        return $percentage;
    }

    /**
     * USE : Get the student selected answer percentage value based on selected classid, or peer groups
     */
    public function getClassPerformancePercentageOfSelectedAnswer($request){
        $data = [];
        $studentidlist = [];
        $ExamDetails = Exam::find($request->exam_id);
        if((isset($request->exam_school_id) && !empty($request->exam_school_id)) || (isset($request->SchoolId) && !empty($request->SchoolId))){
            $schoolId = $request->SchoolId;
        }else{
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        }
        if(isset($ExamDetails)){
            if($ExamDetails->{cn::EXAM_TYPE_COLS}==1){
                $studentidlist = explode(',',$ExamDetails->{cn::EXAM_TABLE_STUDENT_IDS_COL});
            }
            if(isset($request->ClassIds) && !empty($request->ClassIds)){
                $studentidlist = User::where([
                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                    cn::USERS_SCHOOL_ID_COL => $schoolId
                                ])
                                ->get()
                                ->whereIn('CurriculumYearClassId',$request->ClassIds)
                                ->pluck(cn::USERS_ID_COL)
                                ->toArray();
            }
            if(isset($request->isGroupId) && !empty($request->isGroupId)){
                if(isset($request->isGroupId) && !empty($request->isGroupId)){
                    $studentidlist =    PeerGroupMember::where([
                                            cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL => $request->isGroupId
                                        ])
                                        ->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL)
                                        ->unique()
                                        ->toArray();
                }
            }
            $Questions = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,explode(',',$ExamDetails->{cn::EXAM_TABLE_QUESTION_IDS_COL}))->get();
            $AttemptExamData = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$request->exam_id)->whereIntegerInRaw(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentidlist)->get();
            $noOfStudentAttemptExam = count($AttemptExamData) ?? 0;
            if(!empty($Questions)){
                foreach($Questions as $queKey => $question){
                    $data[$question->id][1] = 0;
                    $data[$question->id][2] = 0;
                    $data[$question->id][3] = 0;
                    $data[$question->id][4] = 0;
                    $data[$question->id][5] = 0;
                    if(!empty($question->answers)){
                        for($i=1; $i <= 5; $i++){
                            if(!empty($AttemptExamData)){
                                foreach($AttemptExamData as $attempExamKey => $attemptValue){
                                    if(!empty($attemptValue[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                        foreach(json_decode($attemptValue[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL]) as $answersData){
                                            if(($answersData->question_id == (int)$question->{cn::QUESTION_TABLE_ID_COL}) && ((int)$answersData->answer == $i)){
                                                $data[$question->{cn::QUESTION_TABLE_ID_COL}][$answersData->answer] = ($data[$question->{cn::QUESTION_TABLE_ID_COL}][$answersData->answer] + 1);
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
        $percentage = [];
        if(!empty($data)){
            foreach($data as $quesKey => $questionArray){
                foreach($questionArray as $answerKey => $answerData){
                    if($noOfStudentAttemptExam){
                        $percentage[$quesKey][$answerKey] = round(((100 * $answerData) / $noOfStudentAttemptExam), 2);
                    }else{
                        $percentage[$quesKey][$answerKey] = 0;
                    }
                }
            }
        }
        return $percentage;
    }

    /***
     * USE : Get a7 Count selected answer percentage of question school
     */
    public function getPercentageOfSelectedAnswerSchool($request){
        $examId = $request->exam_id;
        if(isset($request->SchoolId) && !empty($request->SchoolId)){
            $schoolId = $request->SchoolId;
        }else{
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        }
        $data = [];
        $ExamDetails = Exam::find($examId);
        if(isset($ExamDetails)){
            $Questions = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,explode(',',$ExamDetails->{cn::EXAM_TABLE_QUESTION_IDS_COL}))->get();
            $studentidlist = User::where([
                                cn::USERS_SCHOOL_ID_COL => $schoolId,
                                cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                            ])
                            ->pluck(cn::USERS_ID_COL)
                            ->toArray();
            $AttemptExamData = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)
                                ->whereIn(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentidlist)->get();
            $noOfStudentAttemptExam = count($AttemptExamData) ?? 0;
            if(!empty($Questions)){
                foreach($Questions as $queKey => $question){
                    $data[$question->id][1] = 0;
                    $data[$question->id][2] = 0;
                    $data[$question->id][3] = 0;
                    $data[$question->id][4] = 0;
                    $data[$question->id][5] = 0;
                    if(!empty($question->answers)){
                        for($i=1; $i <= 5; $i++){
                            if(!empty($AttemptExamData)){
                                foreach($AttemptExamData as $attempExamKey => $attemptValue){
                                    if(isset($attemptValue[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL]) && !empty($attemptValue[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                        foreach(json_decode($attemptValue[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL]) as $answersData){
                                            if(($answersData->question_id == (int)$question->{cn::QUESTION_TABLE_ID_COL}) && ((int)$answersData->answer == $i)){
                                                $data[$question->{cn::QUESTION_TABLE_ID_COL}][$answersData->answer] = ($data[$question->{cn::QUESTION_TABLE_ID_COL}][$answersData->answer] + 1);
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
        $percentage = [];
        if(!empty($data)){
            foreach($data as $quesKey => $questionArray){
                foreach($questionArray as $answerKey => $answerData){
                    if($noOfStudentAttemptExam){
                        $percentage[$quesKey][$answerKey] = round(((100 * $answerData) / $noOfStudentAttemptExam), 2);
                    }else{
                        $percentage[$quesKey][$answerKey] = 0;
                    }
                }
            }
        }
        return $percentage;
    }

    /***
     * USE : Get a7 Count selected answer percentage of question All school
     */
    public function getPercentageOfSelectedAnswerALLSchool($examId){
        $data = [];
        $ExamDetails = Exam::find($examId);
        if(isset($ExamDetails)){
            $student_id_all = explode(',',$ExamDetails->{cn::EXAM_TABLE_STUDENT_IDS_COL});
            $Questions = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,explode(',',$ExamDetails->{cn::EXAM_TABLE_QUESTION_IDS_COL}))->get();
            $AttemptExamData = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)->whereIn(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$student_id_all)->get();
            $noOfStudentAttemptExam = count($AttemptExamData) ?? 0;
            if(!empty($Questions)){
                foreach($Questions as $queKey => $question){
                    $data[$question->id][1] = 0;
                    $data[$question->id][2] = 0;
                    $data[$question->id][3] = 0;
                    $data[$question->id][4] = 0;
                    $data[$question->id][5] = 0;

                    if(!empty($question->answers)){
                        for($i=1; $i <= 5; $i++){
                            if(!empty($AttemptExamData)){
                                foreach($AttemptExamData as $attempExamKey => $attemptValue){
                                    if(isset($attemptValue[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL]) && !empty($attemptValue[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                        foreach(json_decode($attemptValue[cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL]) as $answersData){
                                            if(($answersData->question_id == (int)$question->{cn::QUESTION_TABLE_ID_COL}) && ((int)$answersData->answer == $i)){
                                                $data[$question->{cn::QUESTION_TABLE_ID_COL}][$answersData->answer] = ($data[$question->{cn::QUESTION_TABLE_ID_COL}][$answersData->answer] + 1);                                                
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
        $percentage = [];
        if(!empty($data)){
            foreach($data as $quesKey => $questionArray){
                foreach($questionArray as $answerKey => $answerData){
                    if($noOfStudentAttemptExam){
                        $percentage[$quesKey][$answerKey] = round(((100 * $answerData) / $noOfStudentAttemptExam), 2);
                    }else{
                        $percentage[$quesKey][$answerKey] = 0;
                    }
                }
            }
        }
        return $percentage;
    }

    public function CheckAnswer(Request $request){
        $wrongAns = array();
        if(isset($request->questionid) && !empty($request->questionid)){
            foreach ($request->questionid as $qKey => $qValue) {
                $language = $qValue['language'];
                $answer = Answer::where(cn::ANSWER_QUESTION_ID_COL,$qValue['question_id'])->get()->toArray();
                if ($answer[0]['correct_answer_'.$language] != $qValue['answer']) {
                    $qValue['questionNo'] = ($qKey + 1);
                    $wrongAns[] = $qValue;
                }
            }
        }
        if(isset($wrongAns) && !empty($wrongAns)){
            return $this->sendResponse($wrongAns);
        }else{
            return $this->sendResponse([]);
        }
    }

    public function getStudentQuestionsByDifficultyAndSpeed($exam_id){
        $studentId = Auth::user()->{cn::USERS_ID_COL};
        $exam = Exam::with('attempt_exams')
                ->where(cn::EXAM_TABLE_ID_COLS,$exam_id)
                ->whereRaw("find_in_set($studentId,student_ids)")
                ->get()
                ->toArray();
        $question_ids = $exam[0]['question_ids'];
        $exam_taking_timing = $exam[0]['attempt_exams'][0]['exam_taking_timing'];
        $exam_taking_timing_second = $this->timeToSecond($exam_taking_timing);
        if($question_ids != ""){
            $question_ids_size = sizeof(explode(',',$question_ids));
        }
        $per_question_time = round($exam_taking_timing_second / $question_ids_size,2);
        $progressQuestions = \App\Helpers\Helper::getQuestionDifficultiesLevelPercent($exam_id,$studentId);
        $result['html'] = (string)View::make('backend.student.student_graph',compact('progressQuestions','per_question_time'));
        return $this->sendResponse($result, '');
    }

    public function GetSchoolAndStudentIds(Request $request){
        $response = [];
        if($this->isAdmin()){
            $schoolData = User::where([cn::USERS_ROLE_ID_COL => cn::SCHOOL_ROLE_ID, cn::USERS_STATUS_COL => 'active'])->get();
            $schoolIds = $schoolData->pluck(cn::USERS_SCHOOL_ID_COL);
            $studentIds = User::whereIn(cn::USERS_SCHOOL_ID_COL,$schoolIds)->where(cn::USERS_ROLE_ID_COL, cn::STUDENT_ROLE_ID)->get()->pluck(cn::USERS_ID_COL);
            if(isset($schoolIds) && !empty($schoolIds)){
                $response['schoolIds'] = implode(',',$schoolIds->toArray());
            }
            if(isset($studentIds) && !empty($studentIds)){
                $response['studentIds'] = implode(',',$studentIds->toArray());
            }
        }
        if($this->isSchoolLogin() || $this->isPrincipalLogin() || $this->isPanelHeadLogin() || $this->isCoOrdinatorLogin()){
            $school_id = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            if(isset($school_id) && !empty($school_id)){
                $response['schoolIds'] = $school_id;
            }
            $studentIds = User::where(cn::USERS_SCHOOL_ID_COL,$school_id)->where(cn::USERS_ROLE_ID_COL, cn::STUDENT_ROLE_ID)->get()->pluck(cn::USERS_ID_COL);
            if(isset($studentIds) && !empty($studentIds)){
                $response['studentIds'] = implode(',',$studentIds->toArray());
            }
        }
        if($this->isTeacherLogin()){
            $school_id = $this->isTeacherLogin();
            if(isset($school_id) && !empty($school_id)){
                $response['schoolIds'] = $school_id;
            }
        }
        return $response;
    }

    public function getSchools(Request $request){
       $examData = Exam::find($request->examId);
       $schoolList = '';
       $html = '';
       $responseData = [];
       if(!empty($examData)){
           if(!empty($examData->{cn::EXAM_TABLE_SCHOOL_COLS})){
                $schoolIds = explode(',',$examData->{cn::EXAM_TABLE_SCHOOL_COLS});
                $schoolList = School::whereNotIn(cn::SCHOOL_ID_COLS,$schoolIds)->where(cn::SCHOOL_SCHOOL_STATUS,'active')->get();
                if($schoolList->isNotEmpty()){
                    foreach($schoolList as $school){
                        $responseData[] = array(
                            'id' => $school->id,
                            'school_name' => $this->getSchoolName($school),
                        );
                    }                    
                }
           }
       }
        return $this->sendResponse($responseData);
    }

    public function getSchoolName($school){
        if(isset($school) && !empty($school)){
            if(app()->getLocale() == 'ch'){
                return mb_convert_encoding($school->DecryptSchoolNameCh, 'UTF-8', 'UTF-8');
            }else{
                return mb_convert_encoding($school->DecryptSchoolNameEn, 'UTF-8', 'UTF-8');
            }
        }
    }

    public function updateSchoolAssingStatus(Request $request){
        $updateSchoolAssignStatus = '';
        $examData = Exam::find($request->examid);
        if($examData->use_of_mode == 2){
            $updateRecord = Exam::where(cn::EXAM_TABLE_ID_COLS,$request->examid)->update([cn::EXAM_TABLE_ASSIGN_SCHOOL_STATUS => $request->status]);
            if($updateRecord){
                $updateSchoolAssignStatus = Exam::Where(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$request->examid)->update([cn::EXAM_TABLE_ASSIGN_SCHOOL_STATUS => $request->status]);
            }
        }else{
            $updateSchoolAssignStatus = Exam::where(cn::EXAM_TABLE_ID_COLS,$request->examid)->update([cn::EXAM_TABLE_ASSIGN_SCHOOL_STATUS => $request->status]);
        }

        if($updateSchoolAssignStatus){
            return $this->sendResponse([], __('languages.status_updated_successfully'));
        }else{
            return $this->sendError(__('languages.please_try_again'), 422);
        }
    }
}