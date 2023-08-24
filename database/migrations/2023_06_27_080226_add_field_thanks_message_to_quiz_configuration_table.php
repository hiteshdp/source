<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldThanksMessageToQuizConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_configuration', function (Blueprint $table) {
            $table->string('thanks_message')->after('arrow_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quiz_configuration', function (Blueprint $table) {
            $table->dropColumn('thanks_message');
        });
    }
}
