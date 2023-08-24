<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProviderUserCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_user_code', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('userId')->comment('Reference from users table')->nullable()->index();
            $table->foreign('userId')->references('id')->on('users');
            $table->unsignedBigInteger('providerId')->comment('Reference from users table')->nullable()->index();
            $table->foreign('providerId')->references('id')->on('users');
            $table->string('code',6)->nullable();
            $table->enum('type', array('0','1'))->comment('0 = revoke, 1 = access')->nullable();
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
        Schema::dropIfExists('provider_user_code');
    }
}
