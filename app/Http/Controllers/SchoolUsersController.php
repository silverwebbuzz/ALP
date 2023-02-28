<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Constants\DbConstant As cn;
use App\Models\User;
use App\Models\School;
use App\Models\Role;
use App\Traits\Common;

class SchoolUsersController extends Controller
{
    use Common;

    public function index()
    {
        try{
            $items = $request->items ?? 10;
            
            $schoolList = School::all();
            $roleList = Role::whereIn(cn::ROLES_ID_COL,[
                            cn::PRINCIPAL_ROLE_ID,
                            cn::PANEL_HEAD_ROLE_ID,
                            cn::CO_ORDINATOR_ROLE_ID,
                            cn::TEACHER_ROLE_ID
                        ])->get();
            $UsersList = [];
            $UsersList = User::with('roles')
                        ->where(function($q){
                            $q->whereIn('role_id',[
                                cn::PRINCIPAL_ROLE_ID,
                                cn::PANEL_HEAD_ROLE_ID,
                                cn::CO_ORDINATOR_ROLE_ID,
                                cn::TEACHER_ROLE_ID
                            ])
                            ->orWhereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids());
                        })
                        ->sortable()
                        ->orderBy(cn::USERS_ID_COL,'DESC')
                        ->paginate($items);
            if(isset($request->filter)){
                $Query = User::select('*')
                        ->where(function($q){
                            $q->whereIn('role_id',[
                                cn::PRINCIPAL_ROLE_ID,
                                cn::PANEL_HEAD_ROLE_ID,
                                cn::CO_ORDINATOR_ROLE_ID,
                                cn::TEACHER_ROLE_ID
                            ])
                            ->orWhereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids());
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

    public function create()
    {
        try{
            $Schools = School::where(cn::SCHOOL_SCHOOL_STATUS,'active')->orderBy('id','DESC')->get();
            $Roles = Role::whereIn(cn::ROLES_ID_COL,[
                        cn::PRINCIPAL_ROLE_ID,
                        cn::PANEL_HEAD_ROLE_ID,
                        cn::CO_ORDINATOR_ROLE_ID,
                        cn::TEACHER_ROLE_ID
                    ])->get();
            return view('backend.SchoolUsersManagement.add',compact('Roles','Schools'));
        }catch(\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
