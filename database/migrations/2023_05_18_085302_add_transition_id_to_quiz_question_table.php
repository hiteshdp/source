<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransitionIdToQuizQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_question', function (Blueprint $table) {
            $table->unsignedInteger('transition_id')->comment('Reference to transition table')->index()->after('percent_progress')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quiz_question', function (Blueprint $table) {
            $table->dropColumn('transition_id');
        });
    }
}
