<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTherapyReferenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('therapy_reference', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('therapyId')->comment = 'Reference to Therapy table';
            $table->foreign('therapyId')->references('id')->on('therapy');
            $table->string('conditionName')->nullable();
            $table->integer('referenceNumber')->nullable();
            $table->longText('referenceApiResponse')->nullable();
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
        Schema::dropIfExists('therapy_reference');
    }
}
