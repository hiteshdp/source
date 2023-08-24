<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShowScoreAndShowReccoScreenFieldToQuizIntroScreenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_intro_screen', function (Blueprint $table) {
            $table->enum('show_score', array('0','1'))->comment('0 = Inactive, 1 = Active')->default('1')->after('button_label');
            $table->enum('show_recco_screen', array('0','1'))->comment('0 = Inactive, 1 = Active')->default('1')->after('show_score');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quiz_intro_screen', function (Blueprint $table) {
            $table->dropColumn('show_score');
            $table->dropColumn('show_recco_screen');
        });
    }
}
