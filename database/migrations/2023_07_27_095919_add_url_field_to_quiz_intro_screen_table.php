<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrlFieldToQuizIntroScreenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_intro_screen', function (Blueprint $table) {
            $table->string('condition_name')->after('question_id')->nullable();
            $table->string('quiz_name')->after('condition_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quiz_intro_screen', function (Blueprint $table) {
            $table->dropColumn('condition_name');
            $table->dropColumn('quiz_name');
        });
    }
}
