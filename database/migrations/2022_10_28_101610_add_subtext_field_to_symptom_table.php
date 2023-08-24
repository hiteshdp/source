<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubtextFieldToSymptomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('symptom', function (Blueprint $table) {
            $table->string('symptomSubText')->nullable()->after('symptomIcon');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('symptom', function (Blueprint $table) {
            $table->dropColumn('symptomSubText');
        });
    }
}
