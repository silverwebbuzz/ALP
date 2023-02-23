<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddSeedQuestionIdColInCalibrationQuestionLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::CALIBRATION_QUESTION_LOG_TABLE, function (Blueprint $table) {
            $table->after(cn::CALIBRATION_QUESTION_LOG_QUESTION_ID_COL, function($table){
                $table->unsignedBigInteger(cn::CALIBRATION_QUESTION_LOG_SEED_QUESTION_ID_COL)->nullable();
                $table->foreign(cn::CALIBRATION_QUESTION_LOG_SEED_QUESTION_ID_COL)->references(cn::QUESTION_TABLE_ID_COL)->on(cn::QUESTION_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
            $table->dropForeign([cn::CALIBRATION_QUESTION_LOG_SEED_QUESTION_ID_COL]);
            $table->dropColumn(cn::CALIBRATION_QUESTION_LOG_SEED_QUESTION_ID_COL);
        });
    }
}
