<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question', function (Blueprint $table) {
            $table->dropForeign(['intro_screen_id']);
         });

         Schema::table('recommendation_screen', function (Blueprint $table) {
            $table->dropForeign(['intro_screen_id']);
         });

         Schema::table('question_option', function (Blueprint $table) {
            $table->dropForeign(['previous_question_screen_id']);
         });
        
         Schema::rename('intro_screen', 'quiz_intro_screen');
         Schema::rename('question', 'quiz_question');
         Schema::rename('question_option', 'quiz_question_option');
         Schema::rename('recommendation_screen', 'quiz_recommendation_screen');

         Schema::table('quiz_question', function (Blueprint $table) {
            $table->foreign('intro_screen_id')->references('id')->on('quiz_intro_screen');
         });

         Schema::table('quiz_recommendation_screen', function (Blueprint $table) {
            $table->foreign('intro_screen_id')->references('id')->on('quiz_intro_screen');
         });

         Schema::table('quiz_question_option', function (Blueprint $table) {
            $table->foreign('previous_question_screen_id')->references('id')->on('quiz_question');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
