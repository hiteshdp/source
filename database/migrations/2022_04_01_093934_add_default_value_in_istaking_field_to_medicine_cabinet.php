<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultValueInIstakingFieldToMedicineCabinet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medicine_cabinet', function (Blueprint $table) {
            $table->string('isTaking','1')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medicine_cabinet', function (Blueprint $table) {
            $table->string('isTaking','1')->nullable()->change();
        });
    }
}
