<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewCommentsInTypeColumnToMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master', function (Blueprint $table) {
            $table->integer('type')->comment('1 = Provider, 2 = Gender, 3 = iama, 4 = On a journey against, 5 = Practioner type, 6 = Speciality, 7 = My affiliation is, 8 = Integrative care experience, 9 = Frequency, 10 = Dosage Type')->change();
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
            $table->integer('type')->comment('1 = Provider, 2 = Gender, 3 = iama, 4 = On a journey against, 5 = Practioner type, 6 = Speciality, 7 = My affiliation is, 8 = Integrative care experience')->change();
        });
    }
}
