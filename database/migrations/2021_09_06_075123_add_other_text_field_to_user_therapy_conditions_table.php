<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtherTextFieldToUserTherapyConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_therapy_conditions', function (Blueprint $table) {
            $table->dropForeign('user_therapy_conditions_conditionid_foreign');
            $table->string('otherText',255)->nullable()->after('conditionId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_therapy_conditions', function (Blueprint $table) {
            $table->dropColumn('otherText');
        });
    }
}
