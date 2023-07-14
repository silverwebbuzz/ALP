<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddGroupingTypeColumnInPeerGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::PEER_GROUP_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::PEER_GROUP_SUBJECT_ID_COL, function($table){
                $table->enum(cn::PEER_GROUP_GROUP_TYPE_COL,['peer_group','group'])->default('peer_group')->nullable();
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
        Schema::table(cn::PEER_GROUP_TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(cn::PEER_GROUP_GROUP_TYPE_COL);
        });
    }
}
