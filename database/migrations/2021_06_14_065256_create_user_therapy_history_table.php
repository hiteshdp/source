<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTherapyHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_therapy_history', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('userId')->comment = 'Reference to User table table';
            $table->foreign('userId')->references('id')->on('users');
            $table->unsignedInteger('therapyID')->comment = 'Reference to Therapy table table';
            $table->foreign('therapyID')->references('id')->on('therapy');
            $table->integer('ratings');
            $table->longText('note');
            $table->unsignedBigInteger('provider')->comment = 'Reference to master table';
            $table->foreign('provider')->references('id')->on('master');
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
        Schema::dropIfExists('user_therapy_history');
    }
}
