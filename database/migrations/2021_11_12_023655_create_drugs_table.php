<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrugsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drugs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('apiDrugId')->nullable();
            $table->string('name')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('classification')->nullable();
            $table->longText('nutrient_depletions')->nullable();
            $table->longText('drugDetail')->nullable()->comment("stores api response of drug details in json");
            $table->integer('isProcessed')->default('0')->nullable();
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
        Schema::dropIfExists('drugs');
    }
}