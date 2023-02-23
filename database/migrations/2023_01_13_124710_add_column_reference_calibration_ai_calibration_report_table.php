<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnReferenceCalibrationAiCalibrationReportTable extends Migration
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
                $table->string(cn::AI_CALIBRATION_REPORT_REFERENCE_CALIBRATION_COL)->nullable();
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
            $table->dropColumn(cn::AI_CALIBRATION_REPORT_REFERENCE_CALIBRATION_COL);
        });
    }
}
