<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_configuration', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('intro_screen_id')->comment('Reference to intro_screen table')->index();
            $table->foreign('intro_screen_id')->references('id')->on('quiz_intro_screen');
            $table->string('question_text_color')->nullable();
            $table->string('answer_text_color')->nullable();
            $table->string('button_color')->nullable();
            $table->string('button_text_color')->nullable();
            $table->string('background_color_screen')->nullable();
            $table->string('arrow_color')->nullable();            
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
        Schema::dropIfExists('quiz_configuration');
    }
}
