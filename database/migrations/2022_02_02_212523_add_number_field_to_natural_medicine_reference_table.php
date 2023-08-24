<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumberFieldToNaturalMedicineReferenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('natural_medicine_reference', function (Blueprint $table) {
            $table->integer('number')->after('referenceId')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('natural_medicine_reference', function (Blueprint $table) {
            $table->dropColumn('number');
        });
    }
}
