<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class ChangeColumnsDataTypeInHistoryStudentExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::HISTORY_STUDENT_EXAMS_TABLE, function (Blueprint $table) {
            $table->longText(cn::HISTORY_STUDENT_EXAMS_EXAM_FIRST_TRIAL_WRONG_QUESTION_IDS_COL)->nullable()->change();
            $table->longText(cn::HISTORY_STUDENT_EXAMS_FIRST_TRIAL_ANSWERED_FLAG_QUESTION_IDS_COL)->nullable()->change();
            $table->longText(cn::HISTORY_STUDENT_EXAMS_FIRST_TRIAL_NOT_ATTEMPTED_FLAG_QUESTION_IDS_COL)->nullable()->change();
            $table->longText(cn::HISTORY_STUDENT_EXAMS_SECOND_TRIAL_ANSWERED_FLAG_QUESTION_IDS_COL)->nullable()->change();
            $table->longText(cn::HISTORY_STUDENT_EXAMS_SECOND_TRIAL_NOT_ATTEMPTED_FLAG_QUESTION_IDS_COL)->nullable()->change();
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
            $table->String(cn::HISTORY_STUDENT_EXAMS_EXAM_FIRST_TRIAL_WRONG_QUESTION_IDS_COL)->nullable()->change();
            $table->String(cn::HISTORY_STUDENT_EXAMS_FIRST_TRIAL_ANSWERED_FLAG_QUESTION_IDS_COL)->nullable()->change();
            $table->String(cn::HISTORY_STUDENT_EXAMS_FIRST_TRIAL_NOT_ATTEMPTED_FLAG_QUESTION_IDS_COL)->nullable()->change();
            $table->String(cn::HISTORY_STUDENT_EXAMS_SECOND_TRIAL_ANSWERED_FLAG_QUESTION_IDS_COL)->nullable()->change();
            $table->String(cn::HISTORY_STUDENT_EXAMS_SECOND_TRIAL_NOT_ATTEMPTED_FLAG_QUESTION_IDS_COL)->nullable()->change();
        });
    }
}
