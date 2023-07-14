<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class UpdateEnumValueExamTableCreatedByUserColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE `".cn::EXAM_TABLE_NAME."` CHANGE `".cn::EXAM_TABLE_CREATED_BY_USER_COL."` `".cn::EXAM_TABLE_CREATED_BY_USER_COL."` ENUM('super_admin','school_admin','principal', 'panel_head','co_ordinator','teacher','student') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("ALTER TABLE `".cn::EXAM_TABLE_NAME."` CHANGE `".cn::EXAM_TABLE_CREATED_BY_USER_COL."` `".cn::EXAM_TABLE_CREATED_BY_USER_COL."` ENUM('super_admin','school_admin','principal','teacher','student') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;");
    }
}
