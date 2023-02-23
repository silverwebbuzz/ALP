<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnStageIdInQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::QUESTION_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::QUESTION_TABLE_ID_COL, function($table){
                $table->Integer(cn::QUESTION_TABLE_STAGE_ID_COL)->default(4);
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
        Schema::table(cn::QUESTION_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::QUESTION_TABLE_STAGE_ID_COL);
        });
    }
}
