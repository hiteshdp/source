<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizScoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_score', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('quiz_id')->comment('Reference to quiz_intro_screen table')->index();
            $table->foreign('quiz_id')->references('id')->on('quiz_intro_screen');
            $table->string('score_range_low')->nullable();
            $table->string('score_range_high')->nullable();
            $table->string('score_description',2000)->nullable();
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
        Schema::dropIfExists('quiz_score');
    }
}
