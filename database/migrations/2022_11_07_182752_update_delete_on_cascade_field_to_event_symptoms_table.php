<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDeleteOnCascadeFieldToEventSymptomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_symptoms', function (Blueprint $table) {
            $table->dropForeign('event_symptoms_eventid_foreign');
            $table->foreign('eventId')
            ->references('id')->on('event')
            ->onDelete('cascade')->onUpdate('cascade');

            $table->dropForeign('event_symptoms_severityid_foreign');
            $table->foreign('severityId')
            ->references('id')->on('severity')
            ->onDelete('cascade')->onUpdate('cascade');

            $table->dropForeign('event_symptoms_symptomid_foreign');
            $table->foreign('symptomId')
            ->references('id')->on('symptom')
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
        Schema::table('event_symptoms', function (Blueprint $table) {
            $table->dropForeign('event_symptoms_eventid_foreign');
            $table->foreign('eventId')
            ->references('id')->on('event');

            $table->dropForeign('event_symptoms_severityid_foreign');
            $table->foreign('severityId')
            ->references('id')->on('severity');

            $table->dropForeign('event_symptoms_symptomid_foreign');
            $table->foreign('symptomId')
            ->references('id')->on('symptom');
        });
    }
}
