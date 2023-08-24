<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceFieldsToTherapyReferenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('therapy_reference', function (Blueprint $table) {
            $table->integer('referenceId')->nullable();
            $table->longText('referenceDescriptione')->nullable();
            $table->string('medicalPublicationId',50)->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('therapy_reference', function (Blueprint $table) {
            //
        });
    }
}
