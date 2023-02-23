<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Constants\DbConstant As cn;
use App\Traits\ResponseFormat;
use App\Traits\Common;
use App\Models\User;
use App\Models\GamePlanets;
use App\Models\StudentGamingModel;
use App\Models\StudentGameCreditPointHistory;

use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    use Common;
    public $Auth;

    public function __construct(){
        $this->middleware('guest');
        $this->Auth = Auth::user();
    }

    public function GetStudentCreditPoints(Request $request){
        try {
            
        } catch (\Exception $ex) {
            return $this->sendError($ex);
        }
    }

    // Get Particular Student Detail
    public function StudentDetail($id){
        try{
            $response = array();
            $gameDetailArray = array();
            $UserId = $this->decrypt($id);
            $userData = User::find($UserId);
            if(!empty($userData)){
                $student['english_name'] = $userData->DecryptNameEn;
                $student['chinese_name'] = $userData->DecryptNameCh;
                $student['credit_point'] = $userData->CreditPoints;
                $student['grade_id'] = $userData->grade_id;
                $student['class_name'] = $userData->class;

                $studentDetail['student_detail'] = $student;

                $gameDetail = StudentGamingModel::where('student_id',$UserId)->get()->toArray();
                if(!empty($gameDetail)){
                    foreach($gameDetail as $gameDetailKey =>  $gameInfo){
                        $gameDetailArray[$gameDetailKey]['id'] = $gameInfo['id'];
                        $gameDetailArray[$gameDetailKey]['game_id'] = $gameInfo['game_id'];
                        $gameDetailArray[$gameDetailKey]['student_id'] = $gameInfo['student_id'];
                        $gameDetailArray[$gameDetailKey]['planet_id'] = $gameInfo['planet_id'];
                        $gameDetailArray[$gameDetailKey]['current_position'] = $gameInfo['current_position'];
                        $gameDetailArray[$gameDetailKey]['visited_steps'] = ($gameInfo['visited_steps']!="") ? $this->StringArrayToConvertArray(explode(',',$gameInfo['visited_steps'])) : [];
                        $gameDetailArray[$gameDetailKey]['key_step_ids'] = ($gameInfo['key_step_ids']!="") ? $this->StringArrayToConvertArray(explode(',',$gameInfo['key_step_ids'])) : [];
                        $gameDetailArray[$gameDetailKey]['increase_step_ids'] = ($gameInfo['increase_step_ids'] != "") ? $this->StringArrayToConvertArray(explode(',',$gameInfo['increase_step_ids'])) : [];
                        $gameDetailArray[$gameDetailKey]['deducted_step_ids']  = ($gameInfo[cn::STUDENT_GAMES_MAPPING_DEDUCTED_STEP_IDS_COL] !="") ? $this->StringArrayToConvertArray(explode(',',$gameInfo[cn::STUDENT_GAMES_MAPPING_DEDUCTED_STEP_IDS_COL])) : [];
                        $gameDetailArray[$gameDetailKey]['status'] = $gameInfo['status'];
                    }
                }
                $studentDetail['game_details'] = $gameDetailArray;
                return $this->sendResponse($studentDetail);
            }else{
                return $this->sendError(__('User Not Found'), 404);
            }
        }catch(\Exception $ex){
            return $this->sendError($ex);
        }
    }

    /**
     * USE : Clear Student Game Data
     */
    public function ClearStudentData($student_id){
        $student_id = $this->decrypt($student_id);
        StudentGamingModel::where('student_id',$student_id)->delete();
        StudentGameCreditPointHistory::where('user_id',$student_id)->delete();
        return $this->sendResponse([],'Data Clear successfully');
    }
}