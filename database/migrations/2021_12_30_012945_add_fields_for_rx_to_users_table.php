<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsForRxToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('ageRange')->after('gender')->nullable()->comment('Reference to master table');
            $table->integer('practitionerType')->after('ageRange')->nullable()->comment('Reference to master table');
            $table->integer('speciality')->after('practitionerType')->nullable()->comment('Reference to master table');
            $table->integer('myAffiliationIs')->after('speciality')->nullable()->comment('Reference to master table');
            $table->integer('integrativeCareExperience')->after('myAffiliationIs')->nullable()->comment('Reference to master table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ageRange');
            $table->dropColumn('practitionerType');
            $table->dropColumn('speciality');
            $table->dropColumn('myAffiliationIs');
            $table->dropColumn('integrativeCareExperience');
        });
    }
}
