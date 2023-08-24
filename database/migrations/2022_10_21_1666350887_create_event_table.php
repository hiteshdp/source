<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTable extends Migration
{
    public function up()
    {
        Schema::create('event', function (Blueprint $table) {

		$table->increments('id');
        $table->unsignedBigInteger('userId')->comment = 'Reference to User table table';
        $table->foreign('userId')->references('id')->on('users');
        $table->unsignedInteger('symptomId')->comment('Reference to symptom table')->index('symptomId');
        $table->foreign('symptomId')->references('id')->on('symptom');
		$table->unsignedInteger('severityId')->comment('Reference to severit table')->index('severityId');
        $table->foreign('severityId')->references('id')->on('severity');
		$table->unsignedInteger('timeWindowId')->comment('Reference to timeWindow table')->index('timeWindowId');
        $table->foreign('timeWindowId')->references('id')->on('time_window_day');
		$table->timestamp('created_at')->nullable();
		$table->timestamp('updated_at')->nullable();
		$table->timestamp('deleted_at')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('event');
    }
}