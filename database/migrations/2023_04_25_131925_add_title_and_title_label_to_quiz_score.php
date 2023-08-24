<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleAndTitleLabelToQuizScore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_score', function (Blueprint $table) {
            $table->string('title',200)->nullable()->after('id');
            $table->string('title_label',500)->nullable()->after('score_range_low');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quiz_score', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('title_label');
        });
    }
}
