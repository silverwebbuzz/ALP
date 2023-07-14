<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\DbConstant as cn;
//use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AICalibrationReport;
use App\Models\Question;

class CalibrationQuestionLog extends Model
{
    use HasFactory;

    protected $table = cn::CALIBRATION_QUESTION_LOG_TABLE;
    
    public $fillable = [
       cn::CALIBRATION_QUESTION_LOG_ID_COL,
       cn::CALIBRATION_QUESTION_LOG_REPORT_ID_COL,
       cn::CALIBRATION_QUESTION_LOG_QUESTION_ID_COL,
       cn::CALIBRATION_QUESTION_LOG_SEED_QUESTION_ID_COL,
       cn::CALIBRATION_QUESTION_LOG_PREVIOUS_AI_DIFFICULTY_COL,
       cn::CALIBRATION_QUESTION_LOG_CALIBRATION_DIFFICULTY_COL,
       cn::CALIBRATION_QUESTION_LOG_CHANGE_DIFFERENCE_COL,
       cn::CALIBRATION_QUESTION_LOG_MEDIAN_OF_DIFFICULTY_LEVEL_COL,
       cn::CALIBRATION_QUESTION_LOG_QUESTION_LOG_TYPE_COL
    ];

    public $timestamps = true;

    public function AICalibrationReport(){
        return $this->hasOne(AICalibrationReport::Class, cn::AI_CALIBRATION_REPORT_ID_COL, cn::CALIBRATION_QUESTION_LOG_REPORT_ID_COL);
    }

    public function question(){
        return $this->hasOne(Question::Class, cn::QUESTION_TABLE_ID_COL, cn::CALIBRATION_QUESTION_LOG_QUESTION_ID_COL);
    }
}
