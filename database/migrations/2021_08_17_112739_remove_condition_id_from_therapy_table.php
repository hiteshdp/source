<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveConditionIdFromTherapyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('therapy', function (Blueprint $table) {
            $table->dropColumn('conditionId');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('therapy', function (Blueprint $table) {
            $table->integer('conditionId')->comment = 'Reference to Conditions table';
        });
    }
}
