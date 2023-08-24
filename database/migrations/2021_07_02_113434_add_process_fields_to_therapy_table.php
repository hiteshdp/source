<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProcessFieldsToTherapyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('therapy', function (Blueprint $table) {
            $table->integer('isProcessed')->comment("0 = Unprocess, 1 = process")->default(0)->nullable()->after('oldApiID');
            $table->date('processedAt')->nullable()->after('isProcessed');
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
            $table->dropColumn('isProcessed');
            $table->dropColumn('processedAt');
        });
    }
}
