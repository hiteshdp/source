<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeWindowDayFieldToEventNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_notes', function (Blueprint $table) {
            $table->unsignedInteger('timeWindowDay')->comment('Reference from time_window_day table')->nullable()->index('timeWindowDay')->after('eventDate');
            $table->foreign('timeWindowDay')->references('id')->on('time_window_day');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_notes', function (Blueprint $table) {
            $table->dropColumn('timeWindowDay');
        });
    }
}
