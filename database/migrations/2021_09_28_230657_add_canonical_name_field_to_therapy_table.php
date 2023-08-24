<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCanonicalNameFieldToTherapyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('therapy', function (Blueprint $table) {
            $table->string('canonicalName',255)->nullable()->after('therapy');
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
            $table->dropColumn('canonicalName');
        });
    }
}
