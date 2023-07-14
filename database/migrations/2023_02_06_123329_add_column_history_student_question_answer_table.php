<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnHistoryStudentQuestionAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::HISTORY_STUDENT_QUESTION_ANSWER_TABLE, function (Blueprint $table) {
            $table->after(cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_TRIAL_NO_COL, function($table){
                $table->enum(cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_ANSWERED_FLAG_COL, ['true', 'false']);
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
        Schema::table(cn::HISTORY_STUDENT_QUESTION_ANSWER_TABLE, function (Blueprint $table) {
            $table->dropColumn(cn::HISTORY_STUDENT_QUESTION_ANSWER_IS_ANSWERED_FLAG_COL);
        });
    }
}
