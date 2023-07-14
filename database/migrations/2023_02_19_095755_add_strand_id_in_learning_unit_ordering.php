<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddStrandIdInLearningUnitOrdering extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::LEARNING_UNIT_ORDERING_TABLE, function (Blueprint $table) {
            $table->after(cn::LEARNING_UNIT_ORDERING_SCHOOL_ID_COL, function($table){
                $table->unsignedBigInteger(cn::LEARNING_UNIT_STRAND_ID_COL)->nullable();
                $table->foreign(cn::LEARNING_UNIT_STRAND_ID_COL)->references(cn::STRANDS_ID_COL)->on(cn::STRANDS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
        Schema::table(cn::LEARNING_UNIT_ORDERING_TABLE, function (Blueprint $table) {
            $table->dropForeign([cn::LEARNING_UNIT_STRAND_ID_COL]);
            $table->dropColumn(cn::LEARNING_UNIT_STRAND_ID_COL);
        });
    }
}
