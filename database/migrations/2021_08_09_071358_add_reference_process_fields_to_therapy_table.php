<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceProcessFieldsToTherapyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('therapy', function (Blueprint $table) {
            $table->integer('isReferenceProcessed')->comment("0 = Unprocess, 1 = process")->default(0)->nullable()->after('processedAt');
            $table->date('referenceProcessedAt')->nullable()->after('isReferenceProcessed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('therapy', function (Blueprint $table) {
            //
        });
    }
}
