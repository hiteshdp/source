<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_payment_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id')->comment = 'Reference to Users table';
            $table->foreign('user_id')->references('id')->on('users');
            $table->index(['user_id']);
            $table->string('subscription_id','255')->nullable();
            $table->date('billing_cycle_date')->nullable();
            $table->date('current_period_start')->nullable();
            $table->date('current_period_end')->nullable();
            $table->string('customer','255')->nullable();
            $table->string('plan_id','255')->nullable();
            $table->string('amount','255')->nullable();
            $table->string('currency','255')->nullable();
            $table->string('interval_val','255')->nullable();
            $table->string('status','255')->nullable();
            $table->string('trial_start','255')->nullable();
            $table->string('trial_end','255')->nullable();
            $table->longText('payload');
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
        Schema::dropIfExists('users_payment_details');
    }
}
