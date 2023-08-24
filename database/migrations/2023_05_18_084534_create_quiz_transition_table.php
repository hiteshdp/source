<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizTransitionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_transition', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',2000)->nullable();
            $table->string('description',2000)->nullable();
            $table->string('image')->nullable();
            $table->string('button_label')->nullable();
            $table->integer('delay')->comment('value in seconds')->nullable();
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
        Schema::dropIfExists('quiz_transition');
    }
}
