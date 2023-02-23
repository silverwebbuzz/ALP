<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddFieldStageIdStrandUnitsObjectivesMappings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::OBJECTIVES_MAPPINGS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::OBJECTIVES_MAPPINGS_ID_COL, function($table){
                $table->Integer(cn::OBJECTIVES_MAPPINGS_STAGE_ID_COL)->nullable()->default(4);
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
        Schema::table(cn::OBJECTIVES_MAPPINGS_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::OBJECTIVES_MAPPINGS_STAGE_ID_COL);
        });
    }
}
