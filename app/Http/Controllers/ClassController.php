<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GradeClassMapping;
use App\Models\ClassSubjectMapping;
use App\Models\GradeSchoolMappings;
use App\Models\SubjectSchoolMappings;
use App\Models\User;
use App\Models\Grades;
use App\Models\Subjects;
use App\Constants\DbConstant As cn;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;
use Redirect;

class ClassController extends Controller
{
    use Common, ResponseFormat;

    /**
     * USE : List Page
     */
    public function index(Request $request){
        //  Laravel Pagination set in Cookie
        //$this->paginationCookie('StudentClassList',$request);
        if(!in_array('grade_management_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))){
            return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
        }
        $items = $request->items ?? 10;
        $countData = Grades::all()->count();
        $TotalFilterData = '';
        try{
            $List = GradeSchoolMappings::with('grades')
                    ->where([
                        cn::GRADES_MAPPING_SCHOOL_ID_COL            => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                        cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL   => $this->GetCurriculumYear()
                    ])
                    ->orderBy(cn::GRADES_ID_COL,'DESC')
                    ->sortable()
                    ->paginate($items);
            return view('backend.class.list',compact('List','countData','items','TotalFilterData'));
        } catch (Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Create form for add grade class
     */
    public function create(){
        if(!in_array('grade_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
            return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
        }
        $GradeList = $this->getGradeLists();
        return view('backend.class.add',compact('GradeList'));
    }

    /**
     * USE : Add new Grade & Class
     */
    public function store(Request $request){
        try {
            if(!in_array('grade_management_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $Grades = $Subjects =''; //For  manage Grade and Subject Mapping 
            ini_set('max_execution_time', -1); //for time execution issue
            // Check validation
            $validator = Validator::make($request->all(), GradeClassMapping::rules($request, 'create'),GradeClassMapping::rulesMessages('create'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            
            if(Grades::where(cn::GRADES_NAME_COL,$request->name)->doesntExist()){
                //Create Grade
                $PostData = array(
                    cn::GRADES_NAME_COL => $request->name,
                    cn::GRADES_STATUS_COL => $request->status
                );
                $Grades = Grades::create($PostData);
                if(!empty($request->class_type)){
                    foreach($request->class_type as $classType){
                        $classData = array(
                            cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL  => $this->GetCurriculumYear(),
                            cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL           => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                            cn::GRADE_CLASS_MAPPING_GRADE_ID_COL            => $Grades->{cn::GRADES_ID_COL},
                            cn::GRADE_CLASS_MAPPING_NAME_COL                => $classType
                        );
                        $gradeClassMapping = GradeClassMapping::create($classData);
                    }
                }
            
                if(!empty($Grades)){
                    //check Subject Mathematics Exists or not
                    if(Subjects::where(cn::SUBJECTS_NAME_COL,cn::SUBJECTMATHEMATICS)->doesntExist()){
                        $PostData = array(
                            cn::SUBJECTS_NAME_COL => cn::SUBJECTMATHEMATICS,
                            cn::SUBJECTS_CODE_COL => cn::CODEMATHEMATICS,
                            cn::SUBJECTS_STATUS_COL => 1
                        );
                        $Subjects = Subjects::create($PostData);
                        if(!empty($Subjects)){
                            if(GradeSchoolMappings::create([
                                cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL   => $this->GetCurriculumYear(),
                                cn::GRADES_MAPPING_SCHOOL_ID_COL            => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                cn::GRADES_MAPPING_GRADE_ID_COL             => $Grades->{cn::GRADES_ID_COL}
                            ])->doesntExist()){
                                GradeSchoolMappings::create([
                                    cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL   => $this->GetCurriculumYear(),
                                    cn::GRADES_MAPPING_SCHOOL_ID_COL            => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                    cn::GRADES_MAPPING_GRADE_ID_COL             => $Grades->{cn::GRADES_ID_COL}
                                ]);
                            }
                            $ClassSubjectMapping = ClassSubjectMapping::create([
                                                    cn::CLASS_SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL    => $this->GetCurriculumYear(),
                                                    cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL            => $Subjects->{cn::SUBJECTS_ID_COL},
                                                    cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL              => $Grades->{cn::GRADES_ID_COL},
                                                    cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL             => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                    cn::CLASS_SUBJECT_MAPPING_STATUS_COL                => 1
                                                ]);
                            if(SubjectSchoolMappings::where([
                                cn::SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL  => $this->GetCurriculumYear(),
                                cn::SUBJECT_MAPPING_SCHOOL_ID_COL           => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                cn::SUBJECT_MAPPING_SUBJECT_ID_COL          => $Subjects->{cn::SUBJECTS_ID_COL}
                            ])->doesntExist()){
                                $subjectMapping =   SubjectSchoolMappings::create([
                                                        cn::SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL  => $this->GetCurriculumYear(),
                                                        cn::SUBJECT_MAPPING_SCHOOL_ID_COL           => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                        cn::SUBJECT_MAPPING_SUBJECT_ID_COL          => $Subjects->{cn::SUBJECTS_ID_COL},
                                                        cn::SUBJECT_MAPPING_STATUS_COL              => 'active'
                                                    ]);
                            }
                            // Clone and mapping data
                            //$this->StrandUnitObjectivesMappingClone($Grades->{cn::GRADES_ID_COL},$Subjects->{cn::SUBJECTS_ID_COL});
                            //Log::info('Job Success - Redirect success page');
                            return redirect('class')->with('success_msg', __('languages.class_added_successfully'));
                        }else{
                            return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                        }
                    }else{
                        $Subjects = Subjects::where([cn::SUBJECTS_NAME_COL => cn::SUBJECTMATHEMATICS])->first();
                        if(!empty($Subjects)){
                            if(GradeSchoolMappings::create([
                                cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL   => $this->GetCurriculumYear(),
                                cn::GRADES_MAPPING_SCHOOL_ID_COL            => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                cn::GRADES_MAPPING_GRADE_ID_COL             => $Grades->{cn::GRADES_ID_COL}
                            ])->doesntExist()){
                                GradeSchoolMappings::create([
                                    cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL   => $this->GetCurriculumYear(),
                                    cn::GRADES_MAPPING_SCHOOL_ID_COL            => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                    cn::GRADES_MAPPING_GRADE_ID_COL             => $Grades->{cn::GRADES_ID_COL}
                                ]);
                            }
                            $ClassSubjectMapping = ClassSubjectMapping::create([
                                                    cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL    => $Subjects->{cn::SUBJECTS_ID_COL},
                                                    cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL      => $Grades->{cn::GRADES_ID_COL},
                                                    cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL     => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                    cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL   => $this->GetCurriculumYear(),
                                                    cn::CLASS_SUBJECT_MAPPING_STATUS_COL        => 1
                                                ]);
                            if(SubjectSchoolMappings::where([cn::SUBJECT_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},cn::SUBJECT_MAPPING_SUBJECT_ID_COL => $Subjects->{cn::SUBJECTS_ID_COL}])->doesntExist()){
                                $subjectMapping = SubjectSchoolMappings::create([cn::SUBJECT_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},cn::SUBJECT_MAPPING_SUBJECT_ID_COL => $Subjects->{cn::SUBJECTS_ID_COL},cn::SUBJECT_MAPPING_STATUS_COL => 'active']);
                            }
                            // Clone and mapping data
                            //$this->StrandUnitObjectivesMappingClone($Grades->{cn::GRADES_ID_COL},$Subjects->{cn::SUBJECTS_ID_COL});
                            Log::info('Job Success - Redirect success page');
                            return redirect('class')->with('success_msg', __('languages.class_added_successfully'));                        
                        }else{
                            return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                        }
                    }
                }
            }else{
                $Grades = Grades::where(cn::GRADES_NAME_COL,$request->name)->first();
                if(!empty($request->class_type)){
                    foreach($request->class_type as $classType){
                        if(GradeClassMapping::where([
                            cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL  => $this->GetCurriculumYear(),
                            cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL           => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                            cn::GRADE_CLASS_MAPPING_GRADE_ID_COL            => $Grades->{cn::GRADES_ID_COL},
                            cn::GRADE_CLASS_MAPPING_NAME_COL                => $classType
                        ])->doesntExist()){
                            $classData = array(
                                cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL  => $this->GetCurriculumYear(),
                                cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL           => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                cn::GRADE_CLASS_MAPPING_GRADE_ID_COL            => $Grades->{cn::GRADES_ID_COL},
                                cn::GRADE_CLASS_MAPPING_NAME_COL                => $classType 
                            );
                            GradeClassMapping::create($classData);
                        }
                    }
                }
                if(GradeSchoolMappings::where([
                    cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL   => $this->GetCurriculumYear(),
                    cn::GRADES_MAPPING_SCHOOL_ID_COL            => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                    cn::GRADES_MAPPING_GRADE_ID_COL             => $Grades->{cn::GRADES_ID_COL}
                ])->doesntExist()){
                    if(!empty($Grades)){
                        if(Subjects::where(cn::SUBJECTS_NAME_COL,cn::SUBJECTMATHEMATICS)->doesntExist()){
                            $PostData = array(
                                cn::SUBJECTS_NAME_COL   => cn::SUBJECTMATHEMATICS,
                                cn::SUBJECTS_CODE_COL   => cn::CODEMATHEMATICS,
                                cn::SUBJECTS_STATUS_COL => 1
                            );
                            $Subjects = Subjects::create($PostData);
                            if(!empty($Subjects)){
                                GradeSchoolMappings::create([
                                    cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL   => $this->GetCurriculumYear(),
                                    cn::GRADES_MAPPING_SCHOOL_ID_COL            => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                    cn::GRADES_MAPPING_GRADE_ID_COL             => $Grades->{cn::GRADES_ID_COL}
                                ]);
                                ClassSubjectMapping::create([
                                    cn::CLASS_SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL    => $this->GetCurriculumYear(),
                                    cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL            => $Subjects->{cn::SUBJECTS_ID_COL},
                                    cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL              => $Grades->{cn::GRADES_ID_COL},
                                    cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL             => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                    cn::CLASS_SUBJECT_MAPPING_STATUS_COL                => 1
                                ]);
                                if(SubjectSchoolMappings::where([
                                    cn::SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL  => $this->GetCurriculumYear(),
                                    cn::SUBJECT_MAPPING_SCHOOL_ID_COL           => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                    cn::SUBJECT_MAPPING_SUBJECT_ID_COL          => $Subjects->{cn::SUBJECTS_ID_COL}
                                ])->doesntExist()){
                                    SubjectSchoolMappings::create([
                                        cn::SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL  => $this->GetCurriculumYear(),
                                        cn::SUBJECT_MAPPING_SCHOOL_ID_COL           => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                        cn::SUBJECT_MAPPING_SUBJECT_ID_COL          => $Subjects->{cn::SUBJECTS_ID_COL},
                                        cn::SUBJECT_MAPPING_STATUS_COL              => 'active'
                                    ]);
                                }
                                // Clone and mapping data
                                //$this->StrandUnitObjectivesMappingClone($Grades->{cn::GRADES_ID_COL},$Subjects->{cn::SUBJECTS_ID_COL});
                                Log::info('Job Success - Redirect success page');
                                return redirect('class')->with('success_msg', __('languages.class_added_successfully'));
                            }else{
                                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                            }
                        }else{
                            $Subjects = Subjects::where([cn::SUBJECTS_NAME_COL => cn::SUBJECTMATHEMATICS])->first();
                            if(!empty($Subjects)){
                                GradeSchoolMappings::create([
                                    cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL   => $this->GetCurriculumYear(),
                                    cn::GRADES_MAPPING_SCHOOL_ID_COL            => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                    cn::GRADES_MAPPING_GRADE_ID_COL             => $Grades->{cn::GRADES_ID_COL}
                                ]);
                                ClassSubjectMapping::create([
                                    cn::CLASS_SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL    => $this->GetCurriculumYear(),
                                    cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL            => $Subjects->{cn::SUBJECTS_ID_COL},
                                    cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL              => $Grades->{cn::GRADES_ID_COL},
                                    cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL             => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                    cn::CLASS_SUBJECT_MAPPING_STATUS_COL                => 1
                                ]);                               
                                if(SubjectSchoolMappings::where([
                                    cn::SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL  => $this->GetCurriculumYear(),
                                    cn::SUBJECT_MAPPING_SCHOOL_ID_COL           => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                    cn::SUBJECT_MAPPING_SUBJECT_ID_COL          => $Subjects->{cn::SUBJECTS_ID_COL}
                                ])->doesntExist()){
                                    SubjectSchoolMappings::create([
                                        cn::SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::SUBJECT_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                        cn::SUBJECT_MAPPING_SUBJECT_ID_COL => $Subjects->{cn::SUBJECTS_ID_COL},
                                        cn::SUBJECT_MAPPING_STATUS_COL => 'active'
                                    ]);
                                }
                                // Clone and mapping data
                                //$this->StrandUnitObjectivesMappingClone($Grades->{cn::GRADES_ID_COL},$Subjects->{cn::SUBJECTS_ID_COL});
                                Log::info('Job Success - Redirect success page');
                                return redirect('class')->with('success_msg', __('languages.class_added_successfully'));
                            }else{
                                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                            }
                        }
                    }
                }else{
                    //return back()->with('error_msg', __('validation.grade_is_already_exists'));
                    return redirect('class')->with('success_msg', __('languages.class_added_successfully'));
                }
            }
        }catch(Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }    
    }

    /**
     * USE : Edit page for grade class
     */
    public function edit($id){
        try{
            if(!in_array('grade_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $GradeMapping = GradeSchoolMappings::find($id);
            $GradeList = $this->getGradeLists();
            $data = Grades::with(['classes' => fn($query) => $query->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL, $schoolId)->where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())])
                    ->where(cn::GRADES_ID_COL,$GradeMapping->{cn::GRADES_MAPPING_GRADE_ID_COL})
                    ->first();
            return view('backend.class.edit',compact('data','GradeList'));
        }catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Update data for grade class
     */
    public function update(Request $request, $id){
        try{
            $getClassData = [];
            if(!in_array('grade_management_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $validator = Validator::make($request->all(), GradeClassMapping::rules($request, 'update'),GradeClassMapping::rulesMessages('update'));
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }            
            $flag = 0; //for manage create or update class
            if(!empty($request->class_type) && isset($request->class_type)){
                $getClassData = GradeClassMapping::withTrashed()->where([
                                    cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL  => $this->GetCurriculumYear(),
                                    cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL           => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                    cn::GRADE_CLASS_MAPPING_GRADE_ID_COL            => $id
                                ])
                                ->get()
                                ->toArray();
                foreach($request->class_type as $classType){
                    if(!in_array($classType,array_column($getClassData,cn::GRADE_CLASS_MAPPING_NAME_COL))){
                        $classData = array(
                            cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL  => $this->GetCurriculumYear(),
                            cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL           => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                            cn::GRADE_CLASS_MAPPING_GRADE_ID_COL            => $id,
                            cn::GRADE_CLASS_MAPPING_NAME_COL                => $classType 
                        );
                        $gradeClassMapping = GradeClassMapping::create($classData);
                    }else{
                        GradeClassMapping::withTrashed()->where([
                            cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL  => $this->GetCurriculumYear(),
                            cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL           => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                            cn::GRADE_CLASS_MAPPING_GRADE_ID_COL            => $id,
                            cn::GRADE_CLASS_MAPPING_NAME_COL                => $classType
                        ])
                        ->Update([cn::DELETED_AT_COL => null]);
                    }
                }
                GradeClassMapping::where([
                    cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL           => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                    cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL  => $this->GetCurriculumYear(),
                    cn::GRADE_CLASS_MAPPING_GRADE_ID_COL            => $id
                ])
                ->whereNotIn(cn::GRADE_CLASS_MAPPING_NAME_COL,$request->class_type)
                ->delete();
            }else{
                GradeClassMapping::where([
                    cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL           => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                    cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL  => $this->GetCurriculumYear(),
                    cn::GRADE_CLASS_MAPPING_GRADE_ID_COL            => $id
                ])->delete();
            }
            if(!empty($getClassData)){
                return redirect('class')->with('success_msg', __('languages.class_updated_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
        }
    }
    
    /**
     * USE : Delete grade class
     */
    public function destroy($id){
        try{
            if(!in_array('grade_management_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $GradeMapping = GradeSchoolMappings::find($id);
            if($GradeMapping->delete()){
                return $this->sendResponse([], __('languages.class_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }catch(\Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    /**
     * USE : Assign student into class
     */
    public function AssignStudentForm(Request $request,$school_id){
        try{
            $items = $request->items ?? 10;
            $TotalFilterData = '';
            $StudentList = User::where([cn::USERS_SCHOOL_ID_COL => $school_id,cn::USERS_GRADE_ID_COL => null,cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID])->paginate($items);
            $countStudentData = $StudentList->count();
            if(Auth::user()->{cn::USERS_SCHOOL_ID_COL} || $this->isTeacherLogin()){
                $Grades = Grades::select(cn::GRADES_ID_COL,cn::GRADES_NAME_COL)->whereIn(cn::GRADES_ID_COL,$this->getSchoolByGradeIds(Auth::user()->{cn::USERS_SCHOOL_ID_COL}))->get();
            }else{
                $Grades = Grades::select(cn::GRADES_ID_COL,cn::GRADES_NAME_COL)->get();
            }
            $Query = User::select('*');
            if(isset($request->filter)){
                if(isset($request->searchs) && !empty($request->searchs)){
                    $Query->orWhere(cn::USERS_NAME_EN_COL,'Like','%'.$this->encrypt($request->searchs.'%'));
                    $Query->orWhere(cn::USERS_EMAIL_COL,'Like','%'.$request->searchs.'%');
                    $Query->orWhere(cn::USERS_MOBILENO_COL,'Like','%'.$request->searchs.'%');
                    $Query->orWhere(cn::USERS_CITY_COL,'Like','%'.$request->searchs.'%');
                }
                if(isset($request->gender)){
                    $Query->where(cn::USERS_GENDER_COL,$request->gender);
                }
                if(isset($request->status)){
                    $Query->where(cn::USERS_STATUS_COL,$request->status);
                }
                $TotalFilterData = $Query->count();
                $StudentList =  $Query->where([
                                    cn::USERS_SCHOOL_ID_COL => auth()->user()->{cn::USERS_SCHOOL_ID_COL},
                                    cn::USERS_GRADE_ID_COL => null,
                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                                ])
                                ->sortable()
                                ->paginate($items);
                $this->StoreAuditLogFunction($request->all(),'User',cn::USERS_ID_COL,'','Assign Students Filter',cn::USERS_TABLE_NAME,'');
            }
            return view('backend.class.assign_student_form',compact('StudentList','countStudentData','items','Grades','TotalFilterData'));
        }catch(Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    /**
     * USE : Update student class id
     */
    public function StoreSingleStudent(Request $request){
        try{
            $student_id = $request->student_ids[0];
            $update = User::where(cn::USERS_ID_COL,$student_id)->update([cn::USERS_GRADE_ID_COL => $request->grade_id]);
            if($update){
                $this->StoreAuditLogFunction('','User',cn::USERS_ID_COL,'','Student Assign ID '.$request->student_ids[0].' IN '.$request->grade_id.' GRADE',cn::USERS_TABLE_NAME,'');
                return $this->sendResponse([], __('languages.student_assigned_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }catch(Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    /**
     * USE : Update all students class id
     */
    public function StoreAllStudents(Request $request){
        try{
            $update = User::whereIn(cn::USERS_ID_COL,$request->student_ids)->update([cn::USERS_GRADE_ID_COL => $request->grade_id]);
            if($update){
                return $this->sendResponse([], __('languages.student_assigned_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }catch(Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
        }
    }
}