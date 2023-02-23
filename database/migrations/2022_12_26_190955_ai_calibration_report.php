<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AiCalibrationReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::AI_CALIBRATION_REPORT_TABLE, function (Blueprint $table) {
            $table->bigIncrements(cn::AI_CALIBRATION_REPORT_ID_COL);
            $table->Date(cn::AI_CALIBRATION_REPORT_START_DATE_COL)->nullable();
            $table->Date(cn::AI_CALIBRATION_REPORT_END_DATE_COL)->nullable();
            $table->longText(cn::AI_CALIBRATION_REPORT_SCHOOL_IDS_COL)->nullable();
            $table->longText(cn::AI_CALIBRATION_REPORT_STUDENT_IDS_COL)->nullable();
            $table->longText(cn::AI_CALIBRATION_REPORT_TEST_TYPE_COL)->nullable();
            $table->longText(cn::AI_CALIBRATION_REPORT_INCLUDED_QUESTION_IDS_COL)->nullable();
            $table->longText(cn::AI_CALIBRATION_REPORT_INCLUDED_STUDENT_IDS_COL)->nullable();
            $table->Text(cn::AI_CALIBRATION_REPORT_MEDIAN_CALIBRATION_DIFFICULTIES_COL)->nullable();
            $table->Text(cn::AI_CALIBRATION_REPORT_MEDIAN_STUDENT_ABILITY_COL)->nullable();
            $table->Text(cn::AI_CALIBRATION_REPORT_CALIBRATION_CONSTANT_COL)->nullable();
            $table->longText(cn::AI_CALIBRATION_REPORT_CURRENT_QUESTION_DIFFICULTIES_COL)->nullable();
            $table->longText(cn::AI_CALIBRATION_REPORT_CALIBRATED_QUESTION_DIFFICULTIES_COL)->nullable();
            $table->longText(cn::AI_CALIBRATION_REPORT_CURRENT_STUDENT_ABILITY_COL)->nullable();
            $table->longText(cn::AI_CALIBRATION_REPORT_CALIBRATED_STUDENT_ABILITY_COL)->nullable();
            $table->Text(cn::AI_CALIBRATION_REPORT_MEDIAN_CALIBRATION_ABILITY_COL)->nullable();
            $table->longText(cn::AI_CALIBRATION_REPORT_REPORT_DATA_COL)->nullable();
            $table->enum(cn::AI_CALIBRATION_REPORT_STATUS_COL,['pending','complete'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::AI_CALIBRATION_REPORT_TABLE);
    }
}
