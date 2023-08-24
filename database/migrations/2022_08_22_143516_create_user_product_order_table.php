<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProductOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_product_order', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('userId')->comment('Reference to user table')->index('userId');
            $table->foreign('userId')->references('id')->on('users');
            $table->unsignedInteger('productId')->comment('Reference to product table')->index('productId');
            $table->foreign('productId')->references('id')->on('product');
            $table->timestamp('last_purchased')->nullable();
            $table->timestamp('next_refill_date')->nullable();
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
        Schema::dropIfExists('user_product_order');
    }
}
