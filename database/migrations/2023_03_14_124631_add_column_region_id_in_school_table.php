<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnRegionIdInSchoolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::SCHOOL_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::SCHOOL_SCHOOL_CITY, function($table){
                $table->unsignedBigInteger(cn::SCHOOL_REGION_ID_COL)->nullable();
                $table->foreign(cn::SCHOOL_REGION_ID_COL)->references(cn::REGIONS_ID_COL)->on(cn::REGIONS_TABLE)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
        Schema::table(cn::SCHOOL_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::SCHOOL_REGION_ID_COL]);
            $table->dropColumn(cn::SCHOOL_REGION_ID_COL);
        });
    }
}
