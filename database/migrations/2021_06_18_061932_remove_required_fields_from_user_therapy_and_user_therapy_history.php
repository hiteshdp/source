<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveRequiredFieldsFromUserTherapyAndUserTherapyHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_therapy', function (Blueprint $table) {
            $table->integer('ratings')->nullable(true)->change();
            $table->longText('note')->nullable(true)->change();
        });
        Schema::table('user_therapy_history', function (Blueprint $table) {
            $table->integer('ratings')->nullable(true)->change();
            $table->longText('note')->nullable(true)->change();
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
            $table->integer('ratings')->nullable(false)->change();
            $table->longText('note')->nullable(false)->change();
        });
        Schema::table('user_therapy_history', function (Blueprint $table) {
            $table->integer('ratings')->nullable(false)->change();
            $table->longText('note')->nullable(false)->change();
        });
    }
}
