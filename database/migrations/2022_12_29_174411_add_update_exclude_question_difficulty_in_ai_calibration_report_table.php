<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddUpdateExcludeQuestionDifficultyInAiCalibrationReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::AI_CALIBRATION_REPORT_TABLE, function (Blueprint $table) {
            $table->after(cn::AI_CALIBRATION_REPORT_STANDARD_DEVIATION_DIFFICULTY_LEVELS_COL, function($table){
                $table->enum(cn::AI_CALIBRATION_REPORT_UPDATE_EXCLUDE_QUESTION_DIFFICULTY_COL,['yes','no'])->nullable();
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
            $table->dropColumn(cn::AI_CALIBRATION_REPORT_UPDATE_EXCLUDE_QUESTION_DIFFICULTY_COL);
        });
    }
}
