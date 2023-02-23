<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddIndexColumnInLearningObjectiveOrderingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::LEARNING_OBJECTIVES_ORDERING_TABLE, function (Blueprint $table) {
            $table->after(cn::LEARNING_OBJECTIVES_ORDERING_LEARNING_POSITION_COL, function($table){
                $table->String(cn::LEARNING_OBJECTIVES_ORDERING_LEARNING_INDEX_COL)->nullable();
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
        Schema::table(cn::LEARNING_OBJECTIVES_ORDERING_TABLE, function (Blueprint $table) {
            $table->dropColumn(cn::LEARNING_OBJECTIVES_ORDERING_LEARNING_INDEX_COL);
        });
    }
}
