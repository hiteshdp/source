<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDeleteOnCascadeFieldToEventConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_conditions', function (Blueprint $table) {

            $table->dropForeign('event_conditions_conditionid_foreign');
            $table->foreign('conditionId')
            ->references('id')->on('conditions')
            ->onDelete('cascade')->onUpdate('cascade');

            $table->dropForeign('event_conditions_eventid_foreign');
            $table->foreign('eventId')
            ->references('id')->on('event')
            ->onDelete('cascade')->onUpdate('cascade');

            $table->dropForeign('event_conditions_eventsymptomid_foreign');
            $table->foreign('eventSymptomId')
            ->references('id')->on('event_symptoms')
            ->onDelete('cascade')->onUpdate('cascade');
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
            
            $table->dropForeign('event_conditions_conditionid_foreign');
            $table->foreign('conditionId')
            ->references('id')->on('conditions');

            $table->dropForeign('event_conditions_eventid_foreign');
            $table->foreign('eventId')
            ->references('id')->on('event');

            $table->dropForeign('event_conditions_eventsymptomid_foreign');
            $table->foreign('eventSymptomId')
            ->references('id')->on('event_symptoms');
        });
    }
}
