<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('intro_screen_id')->comment('Reference to intro_screen table')->index();
            $table->foreign('intro_screen_id')->references('id')->on('intro_screen');
            $table->string('question_text',3000)->nullable();
            $table->string('image')->nullable();
            $table->string('option_ids')->nullable();
            $table->string('percent_progress')->nullable();
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
        Schema::dropIfExists('question');
    }
}
