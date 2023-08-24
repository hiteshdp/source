<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserIntegrativeProtocolConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_integrative_protocol_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('userId')->comment = 'Reference to User table table';
            $table->foreign('userId')->references('id')->on('users');
            $table->unsignedInteger('userIntProtocolId')->comment = 'Reference to user_integrative_protocol table';
            $table->foreign('userIntProtocolId')->references('id')->on('user_integrative_protocol');
            $table->unsignedInteger('conditionId')->comment = 'Reference to conditions table';
            $table->foreign('conditionId')->references('id')->on('conditions');
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
        Schema::dropIfExists('user_integrative_protocol_conditions');
    }
}
