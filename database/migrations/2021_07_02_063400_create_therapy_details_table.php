<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTherapyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('therapy_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('therapyId')->comment = 'Reference to therapy table';
            $table->foreign('therapyId')->references('id')->on('therapy');
            $table->longText('therapyDetail')->nullable()->comment("stores api response of therapy details in json");
            $table->timestamp('therapyReviewedAt')->nullable();
            $table->timestamp('therapyUpdatedAt')->nullable();
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
        Schema::dropIfExists('therapy_details');
    }
}
