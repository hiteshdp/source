<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrugsInteractionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drugs_interactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('drugId')->nullable()->comment("Reference to drug table");
            $table->integer('naturalMedicineId')->nullable()->comment("Reference to therapy table");
            $table->integer('drugApiId')->nullable()->comment("Reference to api id in drug table");
            $table->integer('naturalMedicineApiId')->nullable()->comment("Reference to api id in therapy table");
            $table->string('drugName')->nullable()->comment("Reference name field of drug table");;
            $table->string('interactionRating')->nullable();
            $table->string('severity')->nullable();
            $table->string('occurrence')->nullable();
            $table->string('levelOfEvidence')->nullable();
            $table->longText('description')->nullable();
            $table->longText('interactionDetails')->nullable()->comment("stores api response of interaction details in json");
            $table->integer('interactId')->nullable();
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
        Schema::dropIfExists('drugs_interactions');
    }
}