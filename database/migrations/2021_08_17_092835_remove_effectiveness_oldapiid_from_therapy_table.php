<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveEffectivenessOldapiidFromTherapyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('therapy', function (Blueprint $table) {
            $table->dropColumn('effectiveness');
            $table->dropColumn('oldApiID');
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
            $table->string('effectiveness')->after('conditionId')->nullable();
            $table->integer('oldApiID')->after('apiID')->nullable();
        });
    }
}
