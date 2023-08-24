<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_recommendations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('product_id')->comment('Reference of product_id from product table')->index();
            $table->unsignedInteger('therapy_id')->comment('Reference to therapy table')->index();
            $table->foreign('therapy_id')->references('id')->on('therapy');
            $table->string('product_ratings')->length(3)->default('0');
            $table->tinyInteger('product_review_count')->length(5)->default('0')->unsigned();
            $table->string('product_price')->length(5);
            $table->string('product_url');
            $table->tinyInteger('sort_order')->length(2)->unsigned();
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
        Schema::dropIfExists('products_recommendations');
    }
}
