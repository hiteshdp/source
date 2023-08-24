<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventSymptomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_symptoms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('eventId')->comment('Reference to event table')->index('eventId');
            $table->foreign('eventId')->references('id')->on('event');
            $table->unsignedInteger('symptomId')->comment('Reference to symptom table')->index('symptomId');
            $table->foreign('symptomId')->references('id')->on('symptom');
            $table->unsignedInteger('severityId')->comment('Reference to severity table')->index('severityId');
            $table->foreign('severityId')->references('id')->on('severity');
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
        Schema::dropIfExists('event_symptoms');
    }
}
