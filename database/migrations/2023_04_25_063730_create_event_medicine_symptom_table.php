<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventMedicineSymptomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_medicine_symptom', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('eventId')->comment('Reference to event table')->index();
            $table->foreign('eventId')->references('id')->on('event');
            $table->unsignedBigInteger('userId')->comment('Reference to user table')->index();
            $table->foreign('userId')->references('id')->on('users');
            $table->unsignedInteger('timeWindowId')->comment('Reference to timeWindow table')->index('timeWindowId');
            $table->foreign('timeWindowId')->references('id')->on('time_window_day');
            $table->integer('drugId')->index()->nullable()->comment("Reference to drug table");
            $table->integer('naturalMedicineId')->index()->nullable()->comment("Reference to therapy table");
            $table->integer('productId')->index()->nullable()->comment("Reference to product table");
            $table->integer('dosage')->nullable();
            $table->integer('dosageType')->nullable();
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
        Schema::dropIfExists('event_medicine_symptom');
    }
}
