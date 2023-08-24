<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConditionSymptomTable extends Migration
{
    public function up()
    {
        Schema::create('condition_symptom', function (Blueprint $table) {

		$table->increments('id');
		$table->unsignedInteger('conditionId')->comment('Reference to conditions table')->index('conditionId');
        $table->foreign('conditionId')->references('id')->on('conditions');
		$table->unsignedInteger('symptomId')->comment('Reference to symptom table')->index('symptomId');
        $table->foreign('symptomId')->references('id')->on('symptom');
		$table->timestamp('created_at')->nullable();
		$table->timestamp('updated_at')->nullable();
		$table->timestamp('deleted_at')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('condition_symptom');
    }
}