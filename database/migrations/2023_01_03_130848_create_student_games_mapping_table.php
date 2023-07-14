<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateStudentGamesMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::STUDENT_GAMES_MAPPING_TABLE, function (Blueprint $table) {
            $table->bigIncrements(cn::STUDENT_GAMES_MAPPING_ID_COL);
            $table->unsignedBigInteger(cn::STUDENT_GAMES_MAPPING_GAME_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::STUDENT_GAMES_MAPPING_STUDENT_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::STUDENT_GAMES_MAPPING_PLANET_ID_COL)->nullable();
            $table->Integer(cn::STUDENT_GAMES_MAPPING_CURRENT_POSITION_COL)->nullable();
            $table->longText(cn::STUDENT_GAMES_MAPPING_VISITED_STEPS_COL)->nullable();
            $table->longText(cn::STUDENT_GAMES_MAPPING_KEY_STEP_IDS_COL)->nullable();
            $table->longText(cn::STUDENT_GAMES_MAPPING_INCREASED_STEP_IDS_COL)->nullable();
            $table->enum(cn::STUDENT_GAMES_MAPPING_STATUS_COL,['pending','inprogress','complete'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(cn::STUDENT_GAMES_MAPPING_GAME_ID_COL)->references(cn::GAME_TABLE_ID_COL)->on(cn::GAME_TABLE)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::STUDENT_GAMES_MAPPING_STUDENT_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::STUDENT_GAMES_MAPPING_PLANET_ID_COL)->references(cn::GAME_PLANETS_ID_COL)->on(cn::GAME_PLANETS_TABLE)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::STUDENT_GAMES_MAPPING_TABLE);
    }
}
