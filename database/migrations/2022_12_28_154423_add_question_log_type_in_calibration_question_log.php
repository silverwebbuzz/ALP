<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddQuestionLogTypeInCalibrationQuestionLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::CALIBRATION_QUESTION_LOG_TABLE, function (Blueprint $table) {
            $table->after(cn::CALIBRATION_QUESTION_LOG_MEDIAN_OF_DIFFICULTY_LEVEL_COL, function($table){
                $table->enum(cn::CALIBRATION_QUESTION_LOG_QUESTION_LOG_TYPE_COL,['include','exclude'])->nullable();
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
        Schema::table(cn::CALIBRATION_QUESTION_LOG_TABLE, function (Blueprint $table) {
            $table->dropColumn(cn::CALIBRATION_QUESTION_LOG_QUESTION_LOG_TYPE_COL);
        });
    }
}
