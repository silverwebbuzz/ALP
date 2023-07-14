<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\DbConstant as cn;

class AICalibrationReport extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = cn::AI_CALIBRATION_REPORT_TABLE;

    public $fillable = [
        cn::AI_CALIBRATION_REPORT_REFERENCE_CALIBRATION_COL,
        cn::AI_CALIBRATION_REPORT_CALIBRATION_NUMBER_COL,
        cn::AI_CALIBRATION_REPORT_START_DATE_COL,
        cn::AI_CALIBRATION_REPORT_END_DATE_COL,
        cn::AI_CALIBRATION_REPORT_SCHOOL_IDS_COL,
        cn::AI_CALIBRATION_REPORT_STUDENT_IDS_COL,
        cn::AI_CALIBRATION_REPORT_TEST_TYPE_COL,
        cn::AI_CALIBRATION_REPORT_INCLUDED_QUESTION_IDS_COL,
        cn::AI_CALIBRATION_REPORT_EXCLUDED_QUESTION_IDS_COL,
        cn::AI_CALIBRATION_REPORT_INCLUDED_STUDENT_IDS_COL,
        cn::AI_CALIBRATION_REPORT_MEDIAN_CALIBRATION_DIFFICULTIES_COL,
        cn::AI_CALIBRATION_REPORT_MEDIAN_STUDENT_ABILITY_COL,
        cn::AI_CALIBRATION_REPORT_CALIBRATION_CONSTANT_COL,
        cn::AI_CALIBRATION_REPORT_CURRENT_QUESTION_DIFFICULTIES_COL,
        cn::AI_CALIBRATION_REPORT_CALIBRATED_QUESTION_DIFFICULTIES_COL,
        cn::AI_CALIBRATION_REPORT_CURRENT_STUDENT_ABILITY_COL,
        cn::AI_CALIBRATION_REPORT_CALIBRATED_STUDENT_ABILITY_COL,
        cn::AI_CALIBRATION_REPORT_MEDIAN_CALIBRATION_ABILITY_COL,
        cn::AI_CALIBRATION_REPORT_REPORT_DATA_COL,
        cn::AI_CALIBRATION_REPORT_MEDIAN_DIFFICULTY_LEVELS_COL,
        cn::AI_CALIBRATION_REPORT_STANDARD_DEVIATION_DIFFICULTY_LEVELS_COL,
        cn::AI_CALIBRATION_REPORT_UPDATE_EXCLUDE_QUESTION_DIFFICULTY_COL,
        cn::AI_CALIBRATION_REPORT_STATUS_COL,
    ];

    public $timestamps = true;

    /**
     * Appends
     */
    protected $appends = ['ReferenceAdjustedCalibration'];

    public function getReferenceAdjustedCalibrationAttribute(){
        $ReferenceAdjustedCalibration = null;
        if($this->{cn::AI_CALIBRATION_REPORT_REFERENCE_CALIBRATION_COL} == 'initial_conditions'){
            $ReferenceAdjustedCalibration = __('languages.initial_condition');
        }else{
            $CalibrationData = Self::find($this->{cn::AI_CALIBRATION_REPORT_REFERENCE_CALIBRATION_COL});
            $ReferenceAdjustedCalibration = $CalibrationData->{cn::AI_CALIBRATION_REPORT_CALIBRATION_NUMBER_COL} ?? null;
        }
        return $ReferenceAdjustedCalibration;
    }
}
