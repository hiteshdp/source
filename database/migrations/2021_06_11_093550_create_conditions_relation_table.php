<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConditionsRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conditions_relation', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('conditionId')->comment = 'Reference to conditions table';
            $table->foreign('conditionId')->references('id')->on('conditions');
            $table->unsignedInteger('conditionMasterId')->comment = 'Reference to _master table';
            $table->foreign('conditionMasterId')->references('id')->on('conditions_master');
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
        Schema::dropIfExists('conditions_relation');
    }
}
