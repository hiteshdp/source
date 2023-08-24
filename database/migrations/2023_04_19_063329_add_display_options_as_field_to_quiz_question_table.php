<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisplayOptionsAsFieldToQuizQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_question', function (Blueprint $table) {
            $table->enum('display_options_as', array('1','2'))->comment('1 = Radio button, 2 = Dropdown')->default('1')->after('option_ids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quiz_question', function (Blueprint $table) {
            $table->dropColumn('display_options_as');
        });
    }
}
