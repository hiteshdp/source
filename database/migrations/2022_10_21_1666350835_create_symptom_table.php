<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSymptomTable extends Migration
{
    public function up()
    {
        Schema::create('symptom', function (Blueprint $table) {

		$table->increments('id');
		$table->string('symptomName');
		$table->string('symptomIcon');
		$table->timestamp('created_at')->nullable();
		$table->timestamp('updated_at')->nullable();
		$table->timestamp('deleted_at')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('symptom');
    }
}