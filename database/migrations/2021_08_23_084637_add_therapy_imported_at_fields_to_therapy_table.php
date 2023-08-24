<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTherapyImportedAtFieldsToTherapyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('therapy', function (Blueprint $table) {
            $table->timestamp('imported_at')->nullable()->after('referenceProcessedAt');;
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
