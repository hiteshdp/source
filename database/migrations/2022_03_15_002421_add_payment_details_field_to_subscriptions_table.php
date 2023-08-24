<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentDetailsFieldToSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->timestamp('billing_cycle_date')->nullable()->after('stripe_id');
            $table->timestamp('current_period_start')->nullable()->after('billing_cycle_date');
            $table->timestamp('current_period_end')->nullable()->after('current_period_start');
            $table->string('customer','255')->nullable()->after('current_period_end');
            $table->string('amount','255')->nullable()->after('customer');
            $table->string('currency','255')->nullable()->after('amount');
            $table->string('interval_val','255')->nullable()->after('currency');
            $table->timestamp('trial_start_at')->nullable()->after('quantity');
            $table->timestamp('deleted_at')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('billing_cycle_date');
            $table->dropColumn('current_period_start');
            $table->dropColumn('current_period_end');
            $table->dropColumn('customer');
            $table->dropColumn('plan_id');
            $table->dropColumn('amount');
            $table->dropColumn('currency');
            $table->dropColumn('interval_val');
            $table->dropColumn('trial_start_at');
            $table->dropColumn('deleted_at');
        });
    }
}
