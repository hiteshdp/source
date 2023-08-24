<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProviderUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('userId')->comment('Reference from users table')->nullable()->index();
            $table->foreign('userId')->references('id')->on('users');
            $table->unsignedBigInteger('providerId')->comment('Reference from users table')->nullable()->index();
            $table->foreign('providerId')->references('id')->on('users');
            $table->timestamp('access_start_date')->nullable();
            $table->timestamp('access_end_date')->nullable();
            $table->timestamp('access_revoke_date')->nullable();
            $table->enum('access_notes', array('0','1'))->comment('0 = false, 1 = true')->default('1');
            $table->enum('access_meds', array('0','1'))->comment('0 = false, 1 = true')->default('1');
            $table->enum('access_symptoms', array('0','1'))->comment('0 = false, 1 = true')->default('1');
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
        Schema::dropIfExists('provider_user');
    }
}
