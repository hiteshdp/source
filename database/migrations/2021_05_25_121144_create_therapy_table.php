<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTherapyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('therapy', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conditionId')->comment = 'Reference to Conditions table';
            $table->string('effectiveness')->comment = 'This field is used to show Effectiveness';
            $table->string('therapy')->comment = 'This field is used to show Therapy';
            $table->string('therapyType')->comment = 'This field is used to show Therapy Type';
            $table->integer('apiID')->comment = 'This field is used to show API Id from excel';
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
        Schema::dropIfExists('therapy');
    }
}
