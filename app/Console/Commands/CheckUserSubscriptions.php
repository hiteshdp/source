<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Helpers\Helpers as Helper;
use DB;

class CheckUserSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       $toDate = date('Y-m-d');
       // Checked user subscription date. If subscription is expired then change subscriptions flag
       $checkSubscription = DB::table('subscriptions')
                            ->whereDate('current_period_end','<',$toDate)
                            ->whereRaw('id IN (select MAX(id) FROM subscriptions GROUP BY user_id)')
                            ->orderBy('current_period_end','desc')
                            ->get()->toArray();
       // Checked any subscription expired or not                 
       if(isset($checkSubscription) && !empty($checkSubscription)){
           foreach($checkSubscription as $subscription){
               $user_id = $subscription->user_id;
                //  Subscriptions is expired then change user plan flag
                DB::table('users')->where('id',$user_id)->update(['planType'=>1,'stripe_id'=>null]);
           }
       }                     
    }
}
