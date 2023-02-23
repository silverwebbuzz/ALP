<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddCollumnCalibrationIdInAttemptExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::ATTEMPT_EXAMS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::ATTEMPT_EXAMS_CURRICULUM_YEAR_ID_COL, function($table){
                $table->unsignedBigInteger(cn::ATTEMPT_EXAMS_CALIBRATION_ID_COL)->nullable();
                $table->foreign(cn::ATTEMPT_EXAMS_CALIBRATION_ID_COL)->references(cn::AI_CALIBRATION_REPORT_ID_COL)->on(cn::AI_CALIBRATION_REPORT_TABLE)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
        Schema::table(cn::ATTEMPT_EXAMS_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::ATTEMPT_EXAMS_CALIBRATION_ID_COL]);
            $table->dropColumn(cn::ATTEMPT_EXAMS_CALIBRATION_ID_COL);
        });
    }
}
