<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSymptomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_symptoms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('userId')->comment = 'Reference to User table table';
            $table->foreign('userId')->references('id')->on('users');
            $table->unsignedInteger('profileMemberId')->nullable()->comment('Reference to profile members table')->index('profileMemberId');
            $table->foreign('profileMemberId')->references('id')->on('profile_members')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('symptomId')->comment('Reference to symptom table')->index('symptomId');
            $table->foreign('symptomId')->references('id')->on('symptom')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('status')->nullable()->default('1')->comment('0 = Disable, 1 = Active');
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
        Schema::dropIfExists('user_symptoms');
    }
}
