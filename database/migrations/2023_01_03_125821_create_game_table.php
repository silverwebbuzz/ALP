<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::GAME_TABLE, function (Blueprint $table) {
            $table->bigIncrements(cn::GAME_TABLE_ID_COL);
            $table->String(cn::GAME_NAME_COL)->nullable();
            $table->enum(cn::GAME_STATUS_COL,['active','inactive'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::GAME_TABLE);
    }
}
