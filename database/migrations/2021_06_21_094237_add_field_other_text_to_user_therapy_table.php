<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldOtherTextToUserTherapyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_therapy', function (Blueprint $table) {
            $table->string('otherText')->nullable()->after('shareWithOthers');
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
            $table->dropColumn('otherText');
        });
    }
}
