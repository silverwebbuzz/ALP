<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddFieldsHistoryStudentExams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::HISTORY_STUDENT_EXAMS_TABLE, function (Blueprint $table) {
            $table->after(cn::HISTORY_STUDENT_EXAMS_TOTAL_SECONDS_COL, function($table){
                $table->string(cn::HISTORY_STUDENT_EXAMS_FIRST_TRIAL_ANSWERED_FLAG_QUESTION_IDS_COL)->nullable();
                $table->string(cn::HISTORY_STUDENT_EXAMS_FIRST_TRIAL_NOT_ATTEMPTED_FLAG_QUESTION_IDS_COL)->nullable();
                $table->string(cn::HISTORY_STUDENT_EXAMS_SECOND_TRIAL_ANSWERED_FLAG_QUESTION_IDS_COL)->nullable();
                $table->string(cn::HISTORY_STUDENT_EXAMS_SECOND_TRIAL_NOT_ATTEMPTED_FLAG_QUESTION_IDS_COL)->nullable();
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
        Schema::table(cn::HISTORY_STUDENT_EXAMS_TABLE, function (Blueprint $table) {
            $table->dropColumn(cn::HISTORY_STUDENT_EXAMS_FIRST_TRIAL_ANSWERED_FLAG_QUESTION_IDS_COL);
            $table->dropColumn(cn::HISTORY_STUDENT_EXAMS_FIRST_TRIAL_NOT_ATTEMPTED_FLAG_QUESTION_IDS_COL);
            $table->dropColumn(cn::HISTORY_STUDENT_EXAMS_SECOND_TRIAL_ANSWERED_FLAG_QUESTION_IDS_COL);
            $table->dropColumn(cn::HISTORY_STUDENT_EXAMS_SECOND_TRIAL_NOT_ATTEMPTED_FLAG_QUESTION_IDS_COL);
        });
    }
}
