<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInMedicineCabinetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medicine_cabinet', function (Blueprint $table) {
            $table->longText('conditionIds')->nullable()->comment("Reference to condition table")->after('isTaking');
            $table->integer('frequency')->nullable()->after('conditionIds');
            $table->integer('dosage')->nullable()->after('frequency');
            $table->integer('dosageType')->nullable()->after('dosage');
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
            $table->dropColumn('conditionIds');
            $table->dropColumn('frequency');
            $table->dropColumn('dosage');
            $table->dropColumn('dosageType');

        });
    }
}
