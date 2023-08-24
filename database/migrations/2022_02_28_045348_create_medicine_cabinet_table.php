<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicineCabinetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medicine_cabinet', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('userId')->comment = 'Reference to User table table';
            $table->foreign('userId')->references('id')->on('users');
            $table->integer('drugId')->index()->nullable()->comment("Reference to drug table");
            $table->integer('naturalMedicineId')->index()->nullable()->comment("Reference to therapy table");
            $table->string('isTaking','1')->nullable()->comment("0 = Not taking, 1 = Taking");
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
        Schema::dropIfExists('medicine_cabinet');
    }
}
