<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserActivities;
use App\Models\User;
use App\Models\School;
use App\Models\Grades;
use App\Models\Role;
use App\Traits\Common;
use App\Constants\DbConstant As cn;
use Auth;
use App\Helpers\Helper;
use App\Events\UserActivityLog;
use App\Models\ActivityLog;
use DB;

class UserActivityController extends Controller
{
    use Common;

    public function index(Request $request){
        try{
            $items = $request->items ?? 10;
            $schoolList = School::all();
            $gradeList = Grades::all();
            if($this->isAdmin()){
                $userList = User::sortable()->orderBy(cn::LOGIN_ACTIVITIES_ID_COL,'DESC')->paginate($items);
                $roleList = Role::whereNotIn('id',[5])->get();
            }else{
                $userList = User::where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                            ->sortable()->orderBy(cn::LOGIN_ACTIVITIES_ID_COL,'DESC')->paginate($items);
                $roleList = Role::whereNotIn('id',[1,5])->get();
            }
            $Query = User::select('*');
            if(isset($request->filter)){
                //search by school
                if(isset($request->school_id) && !empty($request->school_id)){
                    $Query->where(cn::USERS_SCHOOL_ID_COL,$request->school_id)->get();
                }
                //search by grade
                if(isset($request->grade_id) && !empty($request->grade_id)){
                    $Query->whereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids($request->grade_id,'',''));
                }
                //search by role
                if(isset($request->role_id) && !empty($request->role_id)){
                    $Query->where(cn::USERS_ROLE_ID_COL,$request->role_id)->get();
                }
                $userList = $Query->sortable()->paginate($items);
            }
            return view('backend.user_activities.list',compact('userList','schoolList','gradeList','roleList','items')); 
        } catch (\Exception $exception) {
            return redirect('users')->withError($exception->getMessage())->withInput();
        }
    }
   
    /**
     * USE : Check particular user activity history.
     */
    public function show(Request $request, $UserId){
        $items = $request->items ?? 10;
        $UsesDetail = User::with('schools')->find($UserId);
        $ActivityLogsQuery = ActivityLog::with(['school','user'])
                            ->where([
                                cn::ACTIVITY_LOG_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                cn::ACTIVITY_LOG_USER_ID_COL => $UserId
                            ])
                            ->orderBy(cn::ACTIVITY_LOG_ID_COL,'DESC');
        if(isset($request->filter)){
            if(isset($request->school_id) && !empty($request->school_id)){
                $ActivityLogsQuery->where(cn::ACTIVITY_LOG_SCHOOL_ID_COL,$request->school_id);
            }
            if(isset($request->searchText) && !empty($request->searchText)){
                $ActivityLogsQuery->where(cn::ACTIVITY_LOG_ACTIVITY_LOG_COL,'like','%'.$request->searchText.'%');
            }
            if(isset($request->from_date) && !empty($request->from_date) && isset($request->to_date) && !empty($request->to_date)){
                $from_date = $this->DateConvertToYMD($request->from_date);
                $to_date = $this->DateConvertToYMD($request->to_date);                    
                $ActivityLogsQuery->whereBetween(DB::raw('DATE(created_at)'), [$from_date, $to_date]);
            }
        }
        $ActivityLogs = $ActivityLogsQuery->get();
        return view('backend.user_activities.activities_report',compact('UsesDetail','ActivityLogs'));
    }
}
