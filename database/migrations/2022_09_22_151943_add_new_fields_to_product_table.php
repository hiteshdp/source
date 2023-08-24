<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->string('skuCode')->nullable()->after('productImageLink');
            $table->string('backUpProductId')->nullable()->after('skuCode');
            $table->string('isActive')->nullable()->after('backUpProductId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn('skuCode');
            $table->dropColumn('backUpProductId');
            $table->dropColumn('isActive');
        });
    }
}
