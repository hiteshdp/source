<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsprocessedFieldToTherapyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('therapy_details', function (Blueprint $table) {
            $table->integer('isProcessed')->default('0')->nullable()->after('effectiveDetail')->comment="0 = Not processed, 1 = Processed, 2 = Processed not imported found duplicate, 3 = Condition name empty";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('therapy_details', function (Blueprint $table) {
            $table->dropColumn('isProcessed');
        });
    }
}
