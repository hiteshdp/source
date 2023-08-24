<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventSymptomsIdToEventConditionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_conditions', function (Blueprint $table) {
            $table->unsignedInteger('eventSymptomId')->comment('Reference to event_symptoms table')->index('eventSymptomId')->after('eventId');
            $table->foreign('eventSymptomId')->references('id')->on('event_symptoms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_conditions', function (Blueprint $table) {
            $table->dropColumn('eventSymptomId');
        });
    }
}
