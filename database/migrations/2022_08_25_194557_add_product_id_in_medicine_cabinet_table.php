<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdInMedicineCabinetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medicine_cabinet', function (Blueprint $table) {
            $table->integer('productId')->nullable()->after('naturalMedicineId')->comment('Reference to product table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medicine_cabinet', function (Blueprint $table) {
            $table->dropColumn('productId');
        });
    }
}
