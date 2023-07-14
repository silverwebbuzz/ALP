<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIsAnswerHistoryStudentQuestionAnswer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE `history_student_question_answer` CHANGE `is_answered_flag` `is_answered_flag` ENUM('true','false') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
