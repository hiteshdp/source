<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToUserProductOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_product_order', function (Blueprint $table) {
            $table->integer('value_in_dollars')->nullable()->after('productId');
            $table->string('order_id')->nullable()->after('value_in_dollars');
            $table->integer('quantity')->nullable()->after('order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_product_order', function (Blueprint $table) {
            $table->dropColumn('value_in_dollars');
            $table->dropColumn('order_id');
            $table->dropColumn('quantity');
        });
    }
}
