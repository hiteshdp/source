<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAffiliateColumnToProductsRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_recommendations', function (Blueprint $table) {
            $table->string('affiliate')->nullable()->after('product_url')->default('Wellkasa');
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
            $table->dropColumn('affiliate');
        });
    }
}
