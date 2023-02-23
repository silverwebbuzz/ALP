<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnMedianDifficultyLevelsInAiCalibrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::AI_CALIBRATION_REPORT_TABLE, function (Blueprint $table) {
            $table->after(cn::AI_CALIBRATION_REPORT_REPORT_DATA_COL, function($table){
                $table->longText(cn::AI_CALIBRATION_REPORT_MEDIAN_DIFFICULTY_LEVELS_COL)->nullable();
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
            $table->dropColumn(cn::AI_CALIBRATION_REPORT_MEDIAN_DIFFICULTY_LEVELS_COL);
        });
    }
}
