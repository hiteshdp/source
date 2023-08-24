<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveConditionIdsFieldFromMedicineCabinetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medicine_cabinet', function (Blueprint $table) {
            $table->dropColumn('conditionIds');
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
            $table->longText('conditionIds')->nullable()->comment("Reference to condition table")->after('isTaking');
        });
    }
}
