<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicineCabinetDetailsNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medicine_cabinet_details_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('medicineCabinetId')->comment = 'Reference to medicine cabinet table';
            $table->foreign('medicineCabinetId')->references('id')->on('medicine_cabinet');
            $table->longText('conditionIds')->nullable()->comment("Reference to condition table");
            $table->integer('frequency')->nullable();
            $table->integer('dosage')->nullable();
            $table->integer('dosageType')->nullable();
            $table->string('notes','255')->nullable();
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
        Schema::dropIfExists('medicine_cabinet_details_notes');
    }
}
