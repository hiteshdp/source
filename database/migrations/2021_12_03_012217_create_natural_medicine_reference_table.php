<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNaturalMedicineReferenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('natural_medicine_reference', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('referenceId')->nullable();
            $table->longText('description')->nullable();
            $table->integer('medicalPublicationId')->nullable();
            $table->longText('referenceApiResponse')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('natural_medicine_reference');
    }
}
