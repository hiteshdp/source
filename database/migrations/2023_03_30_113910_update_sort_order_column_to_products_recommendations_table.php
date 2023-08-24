<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSortOrderColumnToProductsRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_recommendations', function (Blueprint $table) {
            $table->renameColumn('sort_order', 'rank_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_recommendations', function (Blueprint $table) {
            $table->renameColumn('rank_order', 'sort_order');
        });
    }
}
