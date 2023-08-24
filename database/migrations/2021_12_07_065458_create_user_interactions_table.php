<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInteractionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_interactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('userInteractionReportId')->nullable()->comment = 'Reference to User Interaction Report table';
            $table->foreign('userInteractionReportId')->references('id')->on('user_interactions_report')->onDelete('cascade')->onUpdate('cascade');
            $table->longText('drugId')->nullable()->comment = 'Reference to Drugs table';
            $table->longText('naturalMedicineId')->nullable()->comment = 'Reference to Therapy table';;
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
        Schema::dropIfExists('user_interactions');
    }
}
