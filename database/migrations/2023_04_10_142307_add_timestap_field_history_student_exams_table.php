<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddTimestapFieldHistoryStudentExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::HISTORY_STUDENT_EXAMS_TABLE, function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(cn::HISTORY_STUDENT_EXAMS_TABLE, function (Blueprint $table) {
            $table->dropTimestamps();
            $table->dropSoftDeletes();
        });
    }
}
