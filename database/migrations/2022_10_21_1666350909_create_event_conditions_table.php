<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventConditionsTable extends Migration
{
    public function up()
    {
        Schema::create('event_conditions', function (Blueprint $table) {

		$table->increments('id');
		$table->unsignedInteger('eventId')->comment('Reference to event table')->index('eventId');
		$table->foreign('eventId')->references('id')->on('event');
        $table->unsignedInteger('conditionId')->comment('Reference to conditions table')->index('conditionId');
        $table->foreign('conditionId')->references('id')->on('conditions');
		$table->timestamp('created_at')->nullable();
		$table->timestamp('updated_at')->nullable();
		$table->timestamp('deleted_at')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('event_conditions');
    }
}