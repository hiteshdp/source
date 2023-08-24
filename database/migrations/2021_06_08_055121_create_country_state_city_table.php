<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryStateCityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('country', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sortname');  
            $table->string('name'); 
            $table->integer('phonecode');            
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
        Schema::create('state', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');     
            $table->integer('country_id')->references('id')->on('country')->comment('reference to country table');                   
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
        Schema::create('city', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('state_id')->references('id')->on('state')->comment('reference to state table');            
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
        Schema::dropIfExists('country');
        Schema::dropIfExists('state');
        Schema::dropIfExists('city');
    }
}
