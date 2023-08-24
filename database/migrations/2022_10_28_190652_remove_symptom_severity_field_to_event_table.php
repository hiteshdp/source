<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSymptomSeverityFieldToEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event', function (Blueprint $table) {
            $table->dropForeign('event_symptomid_foreign');
            $table->dropColumn('symptomId');
            $table->dropForeign('event_severityid_foreign');
            $table->dropColumn('severityId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event', function (Blueprint $table) {
            $table->unsignedInteger('symptomId')->comment('Reference to symptom table')->index('symptomId');
            $table->foreign('symptomId')->references('id')->on('symptom');
            $table->unsignedInteger('severityId')->comment('Reference to severit table')->index('severityId');
            $table->foreign('severityId')->references('id')->on('severity');
            
        });
    }
}
