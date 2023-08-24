<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropForeignReferenceUserIdToUserProductOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_product_order', function (Blueprint $table) {
            $table->dropForeign(['userId']);
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
            $table->unsignedBigInteger('userId')->change();
            $table->foreign('userId')->references('id')->on('users');
        });
    }
}
