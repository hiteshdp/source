<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleLabelColorToQuizScore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_score', function (Blueprint $table) {
            $table->string('title_label_color',100)->nullable()->after('title_label');
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
            $table->dropColumn('title_label_color');
        });
    }
}
