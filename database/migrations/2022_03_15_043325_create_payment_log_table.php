<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('subscription_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('customer','255')->nullable();
            $table->string('stripe_id','255')->nullable();
            $table->longText('payload');
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
        Schema::dropIfExists('payment_log');
    }
}
