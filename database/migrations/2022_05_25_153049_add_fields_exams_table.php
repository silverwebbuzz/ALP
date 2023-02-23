<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddFieldsExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::EXAM_TABLE_TO_DATE_COLS,function($table){
                $table->string(cn::EXAM_TABLE_START_TIME_COL)->nullable();
                $table->string(cn::EXAM_TABLE_END_TIME_COL)->nullable();
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
        Schema::table(cn::EXAM_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::EXAM_TABLE_START_TIME_COL);
            $table->dropColumn(cn::EXAM_TABLE_END_TIME_COL);
        });
    }
}
