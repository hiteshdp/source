<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductIdForeignKeyToProductTherapyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_therapy', function (Blueprint $table) {
            $table->dropForeign(['productId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_therapy', function (Blueprint $table) {
            $table->foreign('productId')->references('id')->on('product');
        });
    }
}
