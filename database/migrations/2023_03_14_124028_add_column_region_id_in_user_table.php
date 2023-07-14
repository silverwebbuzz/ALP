<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnRegionIdInUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::USERS_CITY_COL, function($table){
                $table->unsignedBigInteger(cn::USERS_REGION_ID_COL)->nullable();
                $table->foreign(cn::USERS_REGION_ID_COL)->references(cn::REGIONS_ID_COL)->on(cn::REGIONS_TABLE)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
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
        Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
            $table->dropForeign([cn::USERS_REGION_ID_COL]);
            $table->dropColumn(cn::USERS_REGION_ID_COL);
        });
    }
}
