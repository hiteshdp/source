<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_members', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('addedByUserId')->comment = 'Reference to Users table';
            $table->foreign('addedByUserId')->references('id')->on('users');
            $table->index(['addedByUserId']);
            $table->string('first_name','20')->nullable();
            $table->string('last_name','20')->nullable();
            $table->integer('gender')->nullable()->comment("Reference to Master table");
            $table->integer('age')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('profile_picture')->nullable();
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
        Schema::dropIfExists('profile_members');
    }
}
