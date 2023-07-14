<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddCalibrationNumberAndExcludedQuestionIdsInAiCalibrationReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::AI_CALIBRATION_REPORT_TABLE, function (Blueprint $table) {
            $table->after(cn::AI_CALIBRATION_REPORT_ID_COL, function($table){
                $table->bigInteger(cn::AI_CALIBRATION_REPORT_CALIBRATION_NUMBER_COL)->nullable();
            });
            $table->after(cn::AI_CALIBRATION_REPORT_INCLUDED_QUESTION_IDS_COL,function($table){
                $table->longText(cn::AI_CALIBRATION_REPORT_EXCLUDED_QUESTION_IDS_COL)->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(cn::AI_CALIBRATION_REPORT_TABLE, function (Blueprint $table) {
            $table->dropColumn(cn::AI_CALIBRATION_REPORT_CALIBRATION_NUMBER_COL);
            $table->dropColumn(cn::AI_CALIBRATION_REPORT_EXCLUDED_QUESTION_IDS_COL);
        });
    }
}
