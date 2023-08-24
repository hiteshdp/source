<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTherapyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_therapy', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('productId')->comment('Reference to product table')->index('productId');
            $table->foreign('productId')->references('id')->on('product');
            $table->unsignedInteger('therapyId')->comment('Reference to therapy table')->index('therapyId');
            $table->foreign('therapyId')->references('id')->on('therapy');
            $table->string('interaction_display_name')->nullable();
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
        Schema::dropIfExists('product_therapy');
    }
}
