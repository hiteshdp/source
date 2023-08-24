<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTherapyConditionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('therapy_condition', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('conditionId')->comment = 'Reference to conditions table';
            $table->foreign('conditionId')->references('id')->on('conditions');
            $table->unsignedInteger('therapyId')->comment = 'Reference to Therapy table';
            $table->foreign('therapyId')->references('id')->on('therapy');
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
        Schema::dropIfExists('therapy_condition');
    }
}
