<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_option', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('previous_question_screen_id')->comment('Reference to question table')->index();
            $table->foreign('previous_question_screen_id')->references('id')->on('question');
            $table->string('option_text')->nullable();
            $table->unsignedInteger('next_question_screen_id')->comment('Reference to question or recommendation_screen table')->index();
            $table->tinyInteger('is_last_question')->comment('0 = No, 1 = Yes');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_option');
    }
}
