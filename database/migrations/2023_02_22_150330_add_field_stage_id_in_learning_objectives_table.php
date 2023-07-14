<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddFieldStageIdInLearningObjectivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::LEARNING_OBJECTIVES_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::LEARNING_OBJECTIVES_ID_COL, function($table){
                $table->unsignedBigInteger(cn::LEARNING_OBJECTIVES_STAGE_ID_COL)->nullable();
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
        Schema::table(cn::LEARNING_OBJECTIVES_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::LEARNING_OBJECTIVES_STAGE_ID_COL);
        });
    }
}
