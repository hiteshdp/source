<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeverityTable extends Migration
{
    public function up()
    {
        Schema::create('severity', function (Blueprint $table) {

		$table->increments('id');
		$table->string('severityLabel');
		$table->unsignedInteger('symptomId')->comment('Reference to symptom table')->index('symptomId');
        $table->foreign('symptomId')->references('id')->on('symptom');
		$table->string('severityColor');
		$table->timestamp('created_at')->nullable();
		$table->timestamp('updated_at')->nullable();
		$table->timestamp('deleted_at')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('severity');
    }
}