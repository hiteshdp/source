<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEffectiveDetailFieldToTherapyDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('therapy_details', function (Blueprint $table) {
            $table->longText('effectiveDetail')->nullable()->after('therapyDetail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('therapy_details', function (Blueprint $table) {
            $table->dropColumn('effectiveDetail');

        });
    }
}
