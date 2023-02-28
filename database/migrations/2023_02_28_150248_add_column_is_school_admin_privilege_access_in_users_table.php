<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class AddColumnIsSchoolAdminPrivilegeAccessInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(cn::USERS_TABLE_NAME, function (Blueprint $table) {
            $table->after(cn::USERS_PROFILE_PHOTO_COL, function($table){
                $table->enum(cn::USERS_IS_SCHOOL_ADMIN_PRIVILEGE_ACCESS_COL, ['true', 'false'])->default('false')->nullable();
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
            $table->dropColumn(cn::USERS_IS_SCHOOL_ADMIN_PRIVILEGE_ACCESS_COL);
        });
    }
}
