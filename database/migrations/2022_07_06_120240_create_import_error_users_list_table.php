<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportErrorUsersListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_error_users_list', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstName','255')->nullable();
            $table->string('lastName','255')->nullable();
            $table->string('email','255')->nullable();
            $table->string('gender','255')->nullable();
            $table->string('dateOfBirth','255')->nullable();
            $table->string('accountType','255')->nullable();
            $table->string('subscriptionType','255')->nullable();
            $table->string('firstName_comment','255')->nullable();
            $table->string('lastName_comment','255')->nullable();
            $table->string('email_comment','255')->nullable();
            $table->string('gender_comment','255')->nullable();
            $table->string('dateOfBirth_comment','255')->nullable();
            $table->string('accountType_comment','255')->nullable();
            $table->string('subscriptionType_comment','255')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('import_error_users_list');
    }
}
