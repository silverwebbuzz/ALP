<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumPreConfiguredDifficultyValueQuestionTable extends Migration
{
    public function up()
    {
        Schema::table(cn::QUESTION_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::QUESTION_DIFFICULTY_LEVEL_COL, function($table){
                $table->string(cn::QUESTION_PRE_CONFIGURE_DIFFICULTY_VALUE)->nullable();
            });
        });
    }

    public function down()
    {
        Schema::table(cn::QUESTION_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::QUESTION_DIFFICULTY_LEVEL_COL, function($table){
                $table->string(cn::QUESTION_PRE_CONFIGURE_DIFFICULTY_VALUE)->nullable();
            });
        });
    }
}
