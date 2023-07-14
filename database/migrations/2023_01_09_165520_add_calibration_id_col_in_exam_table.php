<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddCalibrationIdColInExamTable extends Migration
{
    public function up()
    {
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::EXAM_CURRICULUM_YEAR_ID_COL, function($table){
                $table->unsignedBigInteger(cn::EXAM_CALIBRATION_ID_COL)->nullable();
                $table->foreign(cn::EXAM_CALIBRATION_ID_COL)->references(cn::AI_CALIBRATION_REPORT_ID_COL)->on(cn::AI_CALIBRATION_REPORT_TABLE)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            });
        });
    }

    public function down()
    {
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::EXAM_CALIBRATION_ID_COL]);
            $table->dropColumn(cn::EXAM_CALIBRATION_ID_COL);
        });
    }
}
