<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDurationFieldToEventSymptomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_symptoms', function (Blueprint $table) {
            $table->unsignedInteger('durationId')->comment('Reference from duration table')->nullable()->index('durationId')->after('symptomId');
            $table->foreign('durationId')->references('id')->on('duration');
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
            $table->dropColumn('durationId');
        });
    }
}
