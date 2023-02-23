<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\DbConstant as cn;

class CreateTableLearningObjectivesExtraSkillsMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(cn::LEARNING_OBJECTIVES_SKILLS_TABLE, function (Blueprint $table) {
            $table->id(cn::LEARNING_OBJECTIVES_SKILLS_ID_COL);
            $table->unsignedBigInteger(cn::LEARNING_OBJECTIVES_SKILLS_LEARNING_OBJECTIVE_ID_COL);
            $table->string(cn::LEARNING_OBJECTIVES_SKILLS_LEARNING_SKILL_COL);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(cn::LEARNING_OBJECTIVES_SKILLS_LEARNING_OBJECTIVE_ID_COL)->references(cn::LEARNING_OBJECTIVES_ID_COL)->on(cn::LEARNING_OBJECTIVES_TABLE_NAME)->onDelete(cn::DB_ENGINE_ONDELETE_NAME);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(cn::LEARNING_OBJECTIVES_SKILLS_TABLE);
    }
}
