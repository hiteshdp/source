<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeFieldToMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master', function (Blueprint $table) {
            $table->integer('type')->after('name')->nullable()->comment('1 = Provider, 2 = Gender, 3 = iama, 4 = On a journey against');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
