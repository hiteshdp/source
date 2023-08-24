<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldSharewithothersToUserTherapyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_therapy', function (Blueprint $table) {
            $table->integer('shareWithOthers')->comment("0 = Not Checked, 1 = Checked")->default(0)->nullable()->after('provider');
        });
        Schema::table('user_therapy_history', function (Blueprint $table) {
            $table->integer('shareWithOthers')->comment("0 = Not Checked, 1 = Checked")->default(0)->nullable()->after('provider');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_therapy', function (Blueprint $table) {
            $table->dropColumn('shareWithOthers');
        });
        Schema::table('user_therapy_history', function (Blueprint $table) {
            $table->dropColumn('shareWithOthers');
        });
    }
}
