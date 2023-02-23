<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateCalibrationQuestionLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::CALIBRATION_QUESTION_LOG_TABLE, function (Blueprint $table) {
            $table->bigIncrements(cn::CALIBRATION_QUESTION_LOG_ID_COL);
            $table->unsignedBigInteger(cn::CALIBRATION_QUESTION_LOG_REPORT_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::CALIBRATION_QUESTION_LOG_QUESTION_ID_COL)->nullable();
            $table->String(cn::CALIBRATION_QUESTION_LOG_PREVIOUS_AI_DIFFICULTY_COL);
            $table->String(cn::CALIBRATION_QUESTION_LOG_CALIBRATION_DIFFICULTY_COL);
            $table->String(cn::CALIBRATION_QUESTION_LOG_CHANGE_DIFFERENCE_COL);
            $table->String(cn::CALIBRATION_QUESTION_LOG_MEDIAN_OF_DIFFICULTY_LEVEL_COL);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(cn::CALIBRATION_QUESTION_LOG_REPORT_ID_COL)->references(cn::AI_CALIBRATION_REPORT_ID_COL)->on(cn::AI_CALIBRATION_REPORT_TABLE)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::CALIBRATION_QUESTION_LOG_QUESTION_ID_COL)->references(cn::QUESTION_TABLE_ID_COL)->on(cn::QUESTION_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
            $table->dropForeign([cn::CALIBRATION_QUESTION_LOG_REPORT_ID_COL]);
            $table->dropForeign([cn::CALIBRATION_QUESTION_LOG_QUESTION_ID_COL]);
            $table->dropColumn(cn::CALIBRATION_QUESTION_LOG_REPORT_ID_COL);
            $table->dropColumn(cn::CALIBRATION_QUESTION_LOG_QUESTION_ID_COL);
        });
        Schema::dropIfExists(cn::CALIBRATION_QUESTION_LOG_TABLE);
    }
}
