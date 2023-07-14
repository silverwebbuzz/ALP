<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserCreditPoints;
use App\Constants\DbConstant as cn;

class StudentGamingModel extends Model
{
    use SoftDeletes,HasFactory;

    protected $table = cn::STUDENT_GAMES_MAPPING_TABLE;

    public $fillable = [
        cn::STUDENT_GAMES_MAPPING_GAME_ID_COL,
        cn::STUDENT_GAMES_MAPPING_STUDENT_ID_COL,
        cn::STUDENT_GAMES_MAPPING_PLANET_ID_COL,
        cn::STUDENT_GAMES_MAPPING_CURRENT_POSITION_COL,
        cn::STUDENT_GAMES_MAPPING_VISITED_STEPS_COL,
        cn::STUDENT_GAMES_MAPPING_KEY_STEP_IDS_COL,
        cn::STUDENT_GAMES_MAPPING_INCREASED_STEP_IDS_COL,
        cn::STUDENT_GAMES_MAPPING_STATUS_COL
     ];
 
     public $timestamps = true;

     protected $appends = [
        'CreditPoints'
    ];

     /**
     ** Validation Rules for users
    **/
    public static function rules($request = null, $action = '', $id = null){
        switch ($action) {
            case 'create':
                $rules = [
                    cn::STUDENT_GAMES_MAPPING_PLANET_ID_COL => ['required'],//'regex:/^[0-9]*$/',
                    cn::STUDENT_GAMES_MAPPING_CURRENT_POSITION_COL   => ['required'], 
                    cn::STUDENT_GAMES_MAPPING_VISITED_STEPS_COL => ['required'],
                ];
                break;
            default:
                break;
        }
        return $rules;
    }

    /**
    ** Additional Validation Massages for users
    **/
    public static function rulesMessages($action = ''){
        $messages = [];
        switch ($action) {
            case 'create':
                $messages = [
                    cn::STUDENT_GAMES_MAPPING_PLANET_ID_COL.'.required' => __('languages.planet_id_is_required'),
                    // cn::STUDENT_GAMES_MAPPING_PLANET_ID_COL.'.regex' => __('Enter Only Digits'),    
                    cn::STUDENT_GAMES_MAPPING_CURRENT_POSITION_COL.'.required' => __('languages.current_position_is_required'),
                    // cn::STUDENT_GAMES_MAPPING_CURRENT_POSITION_COL.'.regex' => __('Enter Only Digits'),
                    cn::STUDENT_GAMES_MAPPING_VISITED_STEPS_COL.'.required' => __('languages.please_enter_visited_steps'),
                ];
                break;
            default:
                break;
        }
        return $messages;
    }

     /**
     * USE : Get the student available credit points
     */
    public function getCreditPointsAttribute(){
        $CreditPoints = UserCreditPoints::select('no_of_credit_points')->find($this->id)->toArray();
        if(empty($CreditPoints)){
            return 0;
        }
        return $CreditPoints;
    }
}
