<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableSchoolsGameBundle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::GAME_SCHOOLS_BUNDLE_TABLE, function (Blueprint $table) {
            $table->id(cn::GAME_SCHOOLS_BUNDLE_ID_COL);
            $table->unsignedBigInteger(cn::GAME_SCHOOLS_BUNDLE_SCHOOL_ID_COL)->nullable();
            $table->unsignedBigInteger(cn::GAME_SCHOOLS_BUNDLE_USER_ID_COL);
            $table->LongText(cn::GAME_SCHOOLS_BUNDLE_VALUES_COL)->nullable();
            $table->tinyInteger(cn::GAME_SCHOOLS_BUNDLE_IS_ADMIN_UPDATED)->comment('if admin updated -> 1');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(cn::GAME_SCHOOLS_BUNDLE_SCHOOL_ID_COL)->references(cn::SCHOOL_ID_COLS)->on(cn::SCHOOL_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
            $table->foreign(cn::GAME_SCHOOLS_BUNDLE_USER_ID_COL)->references(cn::USERS_ID_COL)->on(cn::USERS_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::GAME_SCHOOLS_BUNDLE_TABLE);
    }
}
