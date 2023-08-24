<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('userId')->comment('Reference from users table')->nullable()->index();
            $table->foreign('userId')->references('id')->on('users');
            $table->string('title',200)->nullable();
            $table->string('institution',200)->nullable();
            $table->enum('isProviderApproved', array('0','1'))->comment('0 = false, 1 = true')->default('0');
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
        Schema::dropIfExists('providers');
    }
}
