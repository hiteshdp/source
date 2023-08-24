<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicineCabinetConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medicine_cabinet_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('medicineCabinetId')->comment('Reference to medicine cabinet table');
            $table->foreign('medicineCabinetId')->references('id')->on('medicine_cabinet')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('conditionId')->default('0')->comment('Reference to conditions table');
            $table->string('customConditionName','255')->nullable();
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
        Schema::dropIfExists('medicine_cabinet_conditions');
    }
}
