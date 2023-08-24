<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSeverityPriorityFieldToSeverityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('severity', function (Blueprint $table) {
            $table->integer('severityPriority')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('severity', function (Blueprint $table) {
            $table->dropColumn('severityPriority');
        });
    }
}
