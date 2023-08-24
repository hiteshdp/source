<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntroScreenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intro_screen', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('question_id')->comment('Reference to question table')->index();
            $table->string('first_image')->nullable();
            $table->string('second_image')->nullable();
            $table->string('button_label')->nullable();
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
        Schema::dropIfExists('intro_screen');
    }
}
