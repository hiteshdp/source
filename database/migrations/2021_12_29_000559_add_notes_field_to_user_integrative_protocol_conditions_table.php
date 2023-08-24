<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotesFieldToUserIntegrativeProtocolConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_integrative_protocol_conditions', function (Blueprint $table) {
            $table->longText('notes')->nullable()->after('conditionId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_integrative_protocol_conditions', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
}
