<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Constants\DbConstant As cn;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\School;
use App\Models\Role;
use App\Models\Regions;
use App\Traits\Common;
use App\Jobs\DeleteUserDataJob;
use Auth;
use App\Helpers\Helper;
use App\Events\UserActivityLog;

class SchoolUsersController extends Controller
{
    use Common;

    public function __construct(){
        
    }

    /**
     * USE : Listing page school users
     */
    public function index(Request $request){
        try{
            if(!$this->isAdmin() && Auth::user()->{cn::USERS_IS_SCHOOL_ADMIN_PRIVILEGE_ACCESS_COL} == 'no'){
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $items = $request->items ?? 10;
            $schoolList = School::all();
            $Roles = [
                cn::PRINCIPAL_ROLE_ID,
                cn::PANEL_HEAD_ROLE_ID,
                cn::CO_ORDINATOR_ROLE_ID,
                cn::TEACHER_ROLE_ID
            ];
            if($this->isAdmin()){
                $Roles[] = cn::STUDENT_ROLE_ID;
            }
            $roleList = Role::whereIn(cn::ROLES_ID_COL,$Roles)->get();
            $UsersList = [];
            $UsersList = User::with(['roles','schools','Region'])
                        ->where(function($q) use($Roles){
                            if($this->isAdmin()){
                                $q->whereIn(cn::USERS_ROLE_ID_COL,$Roles)
                                ->orWhereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids());
                            }else{
                                $q->whereIn(cn::USERS_ROLE_ID_COL,$Roles);
                            }
                        })
                        ->where(function($q){
                            if(Auth::user()->{cn::USERS_SCHOOL_ID_COL}){
                                $q->where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL});
                            }
                        })
                        ->sortable()
                        ->orderBy(cn::USERS_ID_COL,'DESC')
                        ->paginate($items);
            if(isset($request->filter) && !empty($request->filter)){
                $Query = User::select('*')->with(['roles','schools','Region'])
                        ->where(function($q){
                            if(Auth::user()->{cn::USERS_SCHOOL_ID_COL}){
                                $q->where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL});
                            }
                        })
                        ->where(function($q) use($Roles){
                            if($this->isAdmin()){
                                $q->whereIn(cn::USERS_ROLE_ID_COL,$Roles)
                                ->orWhereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids());
                            }else{
                                $q->whereIn(cn::USERS_ROLE_ID_COL,$Roles);
                            }
                        });
                //search by school
                if(isset($request->school_id) && !empty($request->school_id)){
                    $Query->where(cn::USERS_SCHOOL_ID_COL,$request->school_id);
                }
                //search by Role
                if(isset($request->Role) && !empty($request->Role)){
                    $Query->where(cn::USERS_ROLE_ID_COL,$request->Role);
                }
                //search by username
                if(isset($request->username) && !empty($request->username)){
                    $Query->where(cn::USERS_NAME_EN_COL,'like','%'.$this->encrypt($request->username).'%');
                    $Query->orWhere(cn::USERS_NAME_CH_COL,'like','%'.$this->encrypt($request->username).'%');
                    $Query->orWhere(cn::USERS_NAME_COL,'like','%'.$request->username.'%');
                }
                if(isset($request->email) && !empty($request->email)){
                    $Query->where(cn::USERS_EMAIL_COL,'like','%'.$request->email.'%');
                }
                $UsersList = $Query->orderBy(cn::USERS_ID_COL,'DESC')->sortable()->paginate($items);
            }
            return view('backend.SchoolUsersManagement.list',compact('roleList','UsersList','schoolList','items')); 
            
        }catch(\Exception $exception) {
            return redirect('users')->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Create new school users
     */
    public function create(){
        try{
            if(!$this->isAdmin() && Auth::user()->{cn::USERS_IS_SCHOOL_ADMIN_PRIVILEGE_ACCESS_COL} == 'no'){
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $Regions = Regions::where(cn::REGIONS_STATUS_COL,'active')->get();
            $Schools = School::where(cn::SCHOOL_SCHOOL_STATUS,'active')->orderBy('id','DESC')->get();
            $Roles = Role::whereIn(cn::ROLES_ID_COL,[
                        cn::PRINCIPAL_ROLE_ID,
                        cn::PANEL_HEAD_ROLE_ID,
                        cn::CO_ORDINATOR_ROLE_ID,
                        cn::TEACHER_ROLE_ID
                    ])->get();
            return view('backend.SchoolUsersManagement.add',compact('Roles','Schools','Regions'));
        }catch(\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Store new school users details
     */
    public function store(Request $request){
        try{
            if(!$this->isAdmin() && Auth::user()->{cn::USERS_IS_SCHOOL_ADMIN_PRIVILEGE_ACCESS_COL} == 'no'){
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            // Check validation
            $validator = Validator::make($request->all(), User::rules($request, 'School_Users_Create'), User::rulesMessages('School_Users_Create'));
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Store user detail
            $UserModel = new User;
            $UserModel->{cn::USERS_CURRICULUM_YEAR_ID_COL}                  = $this->GetCurriculumYear();
            $UserModel->{cn::USERS_ROLE_ID_COL}                             = $request->role;
            $UserModel->{cn::USERS_SCHOOL_ID_COL}                           = ($request->school) ? $request->school : Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $UserModel->{cn::USERS_NAME_EN_COL}                             = $this->encrypt($request->name_en);
            $UserModel->{cn::USERS_NAME_CH_COL}                             = $this->encrypt($request->name_ch);
            $UserModel->{cn::USERS_EMAIL_COL}                               = $request->email;
            $UserModel->{cn::USERS_MOBILENO_COL}                            = ($request->mobile_no) ? $this->encrypt($request->mobile_no) : null;
            $UserModel->{cn::USERS_PASSWORD_COL}                            = Hash::make($request->password);
            $UserModel->{cn::USERS_STATUS_COL}                              = $request->status ?? 'active';
            $UserModel->{cn::USERS_CREATED_BY_COL}                          = auth()->user()->{cn::USERS_ID_COL};
            $UserModel->{cn::USERS_IS_SCHOOL_ADMIN_PRIVILEGE_ACCESS_COL}    = $request->is_school_admin_privilege_access;
            $UserModel->{cn::USERS_REGION_ID_COL}                           = ($request->region_id) ? $request->region_id : null;
            $Users = $UserModel->save();
            $Users = $UserModel->latest()->first();
            if($Users){
                $this->StoreAuditLogFunction($request->all(),'User',cn::USERS_ID_COL,'','Create User',cn::USERS_TABLE_NAME,'');
                return redirect('school-users')->with('success_msg', __('languages.user_added_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Edit existing school users
     */
    public function edit($id){
        try{
            if(!$this->isAdmin() && Auth::user()->{cn::USERS_IS_SCHOOL_ADMIN_PRIVILEGE_ACCESS_COL} == 'no'){
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $Regions = Regions::where(cn::REGIONS_STATUS_COL,'active')->get();
            $Schools = School::where(cn::SCHOOL_SCHOOL_STATUS,'active')->orderBy('id','DESC')->get();
            $Roles = Role::whereIn(cn::ROLES_ID_COL,[
                        cn::PRINCIPAL_ROLE_ID,
                        cn::PANEL_HEAD_ROLE_ID,
                        cn::CO_ORDINATOR_ROLE_ID,
                        cn::TEACHER_ROLE_ID
                    ])->get();
            $user = User::find($id);
            return view('backend.SchoolUsersManagement.edit',compact('Roles','Schools','user','Regions'));
        }catch(\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Update school users details
     */
    public function update(Request $request, $id){
        try{
            if(!$this->isAdmin() && Auth::user()->{cn::USERS_IS_SCHOOL_ADMIN_PRIVILEGE_ACCESS_COL} == 'no'){
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            // Check validation
            $validator = Validator::make($request->all(), User::rules($request, 'School_Users_Update', $id), User::rulesMessages('School_Users_Update'));
            if ($validator->fails()){
                return back()->withErrors($validator)->withInput();
            }
            $this->StoreAuditLogFunction($request->all(),'User',cn::USERS_ID_COL,$id,'Update User',cn::USERS_TABLE_NAME,'');

            // Update user detail
            $PostData = array(
                cn::USERS_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                cn::USERS_ROLE_ID_COL       => $request->role,
                cn::USERS_SCHOOL_ID_COL     => ($request->school) ? $request->school : Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                cn::USERS_NAME_EN_COL       => $this->encrypt($request->name_en),
                cn::USERS_NAME_CH_COL       => $this->encrypt($request->name_ch),
                cn::USERS_EMAIL_COL         => $request->email,
                cn::USERS_MOBILENO_COL      => ($request->mobile_no) ? $this->encrypt($request->mobile_no) : null,
                cn::USERS_IS_SCHOOL_ADMIN_PRIVILEGE_ACCESS_COL => $request->is_school_admin_privilege_access,
                cn::USERS_REGION_ID_COL     => ($request->region_id) ? $request->region_id : null,
                cn::USERS_STATUS_COL        => $request->status ?? 'active'
            );
            $User = User::where(cn::USERS_ID_COL,$id)->Update($PostData);            
            if($User){
                return redirect('school-users')->with('success_msg', __('languages.user_updated_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }catch(\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Delete school users
     */
    public function destroy($id){
        //dispatch(new DeleteUserDataJob($id))->delay(now()->addSeconds(1));
        try{
            if(!$this->isAdmin() && Auth::user()->{cn::USERS_IS_SCHOOL_ADMIN_PRIVILEGE_ACCESS_COL} == 'no'){
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $User = User::find($id);
            $this->StoreAuditLogFunction('','Users','','','Delete User ID '.$id,cn::USERS_TABLE_NAME,'');
            if($User->delete()){
                return $this->sendResponse([], __('languages.user_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    /**
     * USE : Delete multiple school user
     */
    public function DeleteMultipleSchoolUser(Request $request){
        dispatch(new DeleteUserDataJob($request->record_ids))->delay(now()->addSeconds(1));
        return $this->sendResponse([], __('languages.user_deleted_successfully'));
    }
}