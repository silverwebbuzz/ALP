<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddEnumsInCalibrationReportTable extends Migration
{
    public function up()
    {
        \DB::statement("ALTER TABLE `".cn::AI_CALIBRATION_REPORT_TABLE."` CHANGE `".cn::AI_CALIBRATION_REPORT_STATUS_COL."` `".cn::AI_CALIBRATION_REPORT_STATUS_COL."` ENUM('pending','complete','adjusted') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending';");
    }

    public function down()
    {
        \DB::statement("ALTER TABLE `".cn::AI_CALIBRATION_REPORT_TABLE."` CHANGE `".cn::AI_CALIBRATION_REPORT_STATUS_COL."` `".cn::AI_CALIBRATION_REPORT_STATUS_COL."` ENUM('pending','complete') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending';");
    }
}
