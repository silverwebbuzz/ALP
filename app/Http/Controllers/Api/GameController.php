<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Constants\DbConstant As cn;
use App\Traits\ResponseFormat;
use App\Traits\Common;
use App\Models\GamePlanets;
use App\Models\GlobalConfiguration;
use App\Models\StudentGamingModel;
use App\Models\UserCreditPoints;
use App\Models\StudentGameCreditPointHistory;
use App\Models\User;
use App\Models\GameUserInfo;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class GameController extends Controller{
    
    use Common;
    public $GameConfiguration;

    public function __construct(){
        $this->middleware('guest');
        $this->GameConfiguration = [
            'planet_entry_color',
            'planet_castle_color',
            'planet_keys_color',
            'unexplored_planet_color',
            'general_planet_color',
            'planet_deduct_step_color',
            'planet_increase_step_color',
            'game_introduction_video_url',
            'max_deduction_steps',
            'max_addition_steps',
            'no_of_game_spot_keys'
        ];
    }

     // Get Planet Lists
     public function PlanetList(){
        try{
            $gradePlanets = array();
            $responseArray = [];
            $GradeID = GamePlanets::where(cn::GAME_PLANETS_STATUS_COL,'active')->pluck('grade_id')->unique();
            if(!empty($GradeID)){
                foreach($GradeID as $gradeId){
                    $a = array();
                    $planetList = GamePlanets::where('grade_id',$gradeId)->get()->toArray();
                    $a['grade_id'] = $gradeId;
                    $a['planets'] = $planetList;

                    $gradePlanets[] = $a;
                }
                return $this->sendResponse($gradePlanets);
            }  
        }catch(\Exception $ex){
            return $this->sendError($ex);
        }
        
    }

    // Game Configuration
    public function GameConfiguration(){
        try{
            $gameConfigurationDetails = [];
            foreach($this->GameConfiguration as $configuration){
                $gameSetting = GlobalConfiguration::where('key',$configuration)->first();
                if(!empty($gameSetting)){
                    $gameConfigurationDetails[$gameSetting->key] = $gameSetting->value;
                }
            }
            return $this->sendResponse($gameConfigurationDetails);
        }catch(\Exception $ex){
            return $this->sendError($ex);
        }
    }

    /**
     * USE : Get Student Total Credit Points
     */
    public function GetStudentCreditPoints($StudentId){
        $CreditPointData = UserCreditPoints::where(cn::USER_CREDIT_USER_ID_COL,$StudentId)->sum('no_of_credit_points');
        return $CreditPointData;
    }

    //Store Game Detail
    public function StoreGameDetail($StudentId,Request $request){
        $validator = Validator::make($request->all(), StudentGamingModel::rules($request, 'create'), StudentGamingModel::rulesMessages('create'));
        if ($validator->fails()) {
            return $this->sendError("", 422, $validator->errors()->all());
        }
        $gameDetail = [];
        $StudentId = $this->decrypt($StudentId);
        // Check Student Credit point is available or not
        //echo $this->GetStudentCreditPoints($StudentId);die;
        if($this->GetStudentCreditPoints($StudentId) <= 0){
            // If Not enough the redirect api message to not enough credit points
            return $this->sendError('Student will have not enough credit points.',422);
        }
        if(StudentGamingModel::where([
            cn::STUDENT_GAMES_MAPPING_GAME_ID_COL       => $request->game_id,
            cn::STUDENT_GAMES_MAPPING_PLANET_ID_COL     => $request->planet_id,
            cn::STUDENT_GAMES_MAPPING_STUDENT_ID_COL    => $StudentId
        ])->doesntExist()){
            StudentGamingModel::create([
                cn::STUDENT_GAMES_MAPPING_GAME_ID_COL           => $request->game_id,
                cn::STUDENT_GAMES_MAPPING_PLANET_ID_COL         => $request->planet_id,
                cn::STUDENT_GAMES_MAPPING_VISITED_STEPS_COL     => ($request->visited_steps !="") ? implode(',',$request->visited_steps) : null,
                cn::STUDENT_GAMES_MAPPING_KEY_STEP_IDS_COL      => ($request->key_step_ids != "") ? implode(',',$request->key_step_ids) : null,
                cn::STUDENT_GAMES_MAPPING_INCREASED_STEP_IDS_COL => ($request->increased_step_ids != "") ? implode(',',$request->increased_step_ids) : null,
                cn::STUDENT_GAMES_MAPPING_DEDUCTED_STEP_IDS_COL => ($request->deducted_step_ids !="" ) ? implode(',',$request->deducted_step_ids) : null,
                cn::STUDENT_GAMES_MAPPING_STUDENT_ID_COL        => $StudentId,
                cn::STUDENT_GAMES_MAPPING_CURRENT_POSITION_COL  => $request->current_position
            ]);
            $gameData = StudentGamingModel::latest()->first();
        }else{
            $StudentGameMappingData = StudentGamingModel::where([
                                        cn::STUDENT_GAMES_MAPPING_GAME_ID_COL       => $request->game_id,
                                        cn::STUDENT_GAMES_MAPPING_PLANET_ID_COL     => $request->planet_id,
                                        cn::STUDENT_GAMES_MAPPING_STUDENT_ID_COL    => $StudentId
                                    ])->first();
            if(!empty($StudentGameMappingData)){
                StudentGamingModel::where(cn::STUDENT_GAMES_MAPPING_ID_COL,$StudentGameMappingData->{cn::STUDENT_GAMES_MAPPING_ID_COL})
                ->update([
                    cn::STUDENT_GAMES_MAPPING_GAME_ID_COL           => $request->game_id,
                    cn::STUDENT_GAMES_MAPPING_PLANET_ID_COL         => $request->planet_id,
                    cn::STUDENT_GAMES_MAPPING_VISITED_STEPS_COL     => ($request->visited_steps !="") ? implode(',',$request->visited_steps) : null,
                    cn::STUDENT_GAMES_MAPPING_KEY_STEP_IDS_COL      => ($request->key_step_ids != "") ? implode(',',$request->key_step_ids) : null,
                    cn::STUDENT_GAMES_MAPPING_INCREASED_STEP_IDS_COL => ($request->increased_step_ids != "") ? implode(',',$request->increased_step_ids) : null,
                    cn::STUDENT_GAMES_MAPPING_DEDUCTED_STEP_IDS_COL => ($request->deducted_step_ids !="" ) ? implode(',',$request->deducted_step_ids) : null,
                    cn::STUDENT_GAMES_MAPPING_STUDENT_ID_COL        => $StudentId,
                    cn::STUDENT_GAMES_MAPPING_CURRENT_POSITION_COL  => $request->current_position
                ]);
                $gameData = StudentGamingModel::find($StudentGameMappingData->id);                
            }
        }
        // Update credit point history for game
        $this->UpdateStudentCreditPointsHistory($request,$StudentId);

        $gameDetail = $this->sendStoreGameResponse($gameData);
        return $this->sendResponse($gameDetail);
    }

    // return response of store game detail
    public function sendStoreGameResponse($StudentGamingData){
        $gameDetail = [];
        if(!empty($StudentGamingData)){
            $gameDetail['id']               = $StudentGamingData->id;
            $gameDetail['game_id']          = $StudentGamingData->game_id;
            $gameDetail['student_id']       = $StudentGamingData->student_id;
            $gameDetail['planet_id']        = $StudentGamingData->planet_id;
            $gameDetail['current_position'] = $StudentGamingData->current_position ?? null;
            if(!empty($StudentGamingData->visited_steps)){
                $gameDetail['visited_steps']  = $this->StringArrayToConvertArray(explode(',',$StudentGamingData->visited_steps));
            }
            if(!empty($StudentGamingData->key_step_ids)){
                $gameDetail['key_step_ids']  = $this->StringArrayToConvertArray(explode(',',$StudentGamingData->key_step_ids));
            }
            if(!empty($StudentGamingData->increase_step_ids)){
                $gameDetail['increase_step_ids']  = $this->StringArrayToConvertArray(explode(',',$StudentGamingData->increase_step_ids));
            }
            if(!empty($StudentGamingData->{cn::STUDENT_GAMES_MAPPING_DEDUCTED_STEP_IDS_COL})){
                $gameDetail['deducted_step_ids']  = $this->StringArrayToConvertArray(explode(',',$StudentGamingData->{cn::STUDENT_GAMES_MAPPING_DEDUCTED_STEP_IDS_COL}));
            }
            $gameDetail['status']  = $StudentGamingData->status;
        }
        return $gameDetail;
    }

    // Student Game Credit Point History Array
    public function UpdateStudentCreditPointsHistory($request,$StudentId){
        $CreditPointData = UserCreditPoints::where(cn::USER_CREDIT_USER_ID_COL,$StudentId)->first();
        if(!empty($CreditPointData)){
            $CurrentCreditPoint = $CreditPointData->{cn::USER_NO_OF_CREDIT_POINTS_COL};
            $RemainingCreditPoints = $CurrentCreditPoint;
            if($CurrentCreditPoint){
                if(!in_array($request->current_position,$request->visited_steps)){
                    $RemainingCreditPoints = ($RemainingCreditPoints - 1);
                }
                // Increase credit points
                if($request->no_of_increase_step){
                    $RemainingCreditPoints = ($RemainingCreditPoints + $request->no_of_increase_step);
                }
                // Decrease credit points
                if($request->no_of_deduct_step){
                    $RemainingCreditPoints = ($RemainingCreditPoints - $request->no_of_deduct_step);
                }
                // Deduct current step
                $isDeductCurrentStep = 1;
                $RemainingCreditPoints = ($RemainingCreditPoints - $isDeductCurrentStep);

                if(!empty($RemainingCreditPoints)){
                    StudentGameCreditPointHistory::create([
                        cn::STUDENT_GAME_CREDIT_POINT_HISTORY_GAME_ID_COL                   => $request->game_id,
                        cn::STUDENT_GAME_CREDIT_POINT_HISTORY_PLANET_ID_COL                 => $request->planet_id,
                        cn::STUDENT_GAME_CREDIT_POINT_HISTORY_USER_ID_COL                   => $StudentId,
                        cn::STUDENT_GAME_CREDIT_POINT_HISTORY_CURRENT_CREDIT_POINT_COL      => $CurrentCreditPoint,
                        cn::STUDENT_GAME_CREDIT_POINT_HISTORY_DEDUCT_CURRENT_STEP_COL       => $isDeductCurrentStep,
                        cn::STUDENT_GAME_CREDIT_POINT_HISTORY_INCREASED_STEPS_COL           => ($request->no_of_increase_step) ? $request->no_of_increase_step : null,
                        cn::STUDENT_GAME_CREDIT_POINT_HISTORY_DEDUCTED_STEPS_COL            => ($request->no_of_deduct_step) ? $request->no_of_deduct_step : null,
                        cn::STUDENT_GAME_CREDIT_POINT_HISTORY_REMAINING_CREDIT_POINT_COL    => $RemainingCreditPoints
                    ]);
                    // Update users remaining credit points
                    UserCreditPoints::where(cn::USER_CREDIT_USER_ID_COL,$StudentId)->update([cn::USER_NO_OF_CREDIT_POINTS_COL => $RemainingCreditPoints]);
                }
            }
        }
    }

    /**
     * USE : Update Game Current Position
     */
    public function UpdateGamePosition($StudentId, Request $request){
        $StudentId = $this->decrypt($StudentId);
        $StudentGameMappingData = StudentGamingModel::where([
            cn::STUDENT_GAMES_MAPPING_GAME_ID_COL       => $request->game_id,
            cn::STUDENT_GAMES_MAPPING_PLANET_ID_COL     => $request->planet_id,
            cn::STUDENT_GAMES_MAPPING_STUDENT_ID_COL    => $StudentId
        ])->first();
        if(!empty($StudentGameMappingData)){
            StudentGamingModel::where(cn::STUDENT_GAMES_MAPPING_ID_COL,$StudentGameMappingData->{cn::STUDENT_GAMES_MAPPING_ID_COL})
            ->update([
                cn::STUDENT_GAMES_MAPPING_CURRENT_POSITION_COL  => $request->current_position
            ]);
            $gameData = StudentGamingModel::find($StudentGameMappingData->id);
            $gameDetail = $this->sendStoreGameResponse($gameData);
            return $this->sendResponse($gameDetail);
        }else{
            return $this->sendError('Data not found',422);
        }
    }

        
    /**
     * JWT Login
     */

    public function login(Request $request){
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);
        $credentials = $request->only('username', 'password');
        $studentInfo = [];
        Auth::shouldUse('api');
        $token = JWTAuth::attempt($credentials);
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        if(!empty($request->userId)){
            $studentInfo = $this->StudentPayloadData($request->userId);
            return response()->json(compact('token','studentInfo'));
        }
        return response()->json(compact('token'));
    }

    public function StudentPayloadData($StudentID){
        $response = [];
        $studentData = User::find($StudentID);
        $response['Studentinfo']['StudentID'] = $studentData->id;
        $response['Studentinfo']['RewardPoints'] = $studentData->CreditPoints;
        $response['Studentinfo']['AvailableStepBundlePackage'] = [];
        $response['Studentinfo']['ChallengeStatus'] = [];
        return $response;
    }
    /**
     * JWT Logout
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 300,
            'user' => auth()->user()
        ]);
    }
}
