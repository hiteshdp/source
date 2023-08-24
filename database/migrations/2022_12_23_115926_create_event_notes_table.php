<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('userId')->comment('Reference to users table');
            $table->foreign('userId')->references('id')->on('users');
            $table->timestamp('eventDate')->nullable();
            $table->longText('notes');
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
        Schema::dropIfExists('event_notes');
    }
}
