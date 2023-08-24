<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEffectivenessFieldToTherapyConditionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('therapy_condition', function (Blueprint $table) {
            $table->string('effectiveness')->nullable()->after('therapyId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('therapy_condition', function (Blueprint $table) {
            $table->dropColumn('effectiveness');
        });
    }
}
