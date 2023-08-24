<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmpInteractionCheckerAutomationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_interaction_checker_automation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('drugApiId')->nullable();
            $table->string('drugName','255')->nullable();
            $table->integer('naturalMedicineApiId')->nullable();
            $table->string('therapy','255')->nullable();
            $table->string('ratingLabel','255')->nullable();
            $table->string('result','255')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tmp_interaction_checker_automation');
    }
}
