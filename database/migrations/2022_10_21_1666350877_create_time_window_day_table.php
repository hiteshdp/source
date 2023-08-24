<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeWindowDayTable extends Migration
{
    public function up()
    {
        Schema::create('time_window_day', function (Blueprint $table) {

		$table->increments('id');
		$table->string('label');
		$table->timestamp('created_at')->nullable();
		$table->timestamp('updated_at')->nullable();
		$table->timestamp('deleted_at')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('time_window_day');
    }
}