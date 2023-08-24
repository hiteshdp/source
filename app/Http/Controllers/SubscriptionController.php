<?php

namespace App\Http\Controllers;

require_once('../vendor/autoload.php');

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\PaymentMethod;
use App\Helpers\Helpers as Helper;
use \Stripe\Stripe;
use App\Models\User;
use DB;
class SubscriptionController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    /**
    * Function For Get plan from stripe  
    */
    public function retrievePlans() {
        // Set stripe secret key
        $key = \config('services.stripe.secret');
        $stripe = new \Stripe\StripeClient($key);
        $plansraw = $stripe->plans->all();
        $plans = $plansraw->data;
        
        foreach($plans as $plan) {
            // Get product details from stripe
            $prod = $stripe->products->retrieve(
                $plan->product,[]
            );
            $plan->product = $prod;
        }
        return $plans;
    }
    public function showSubscription() {
        $plans = $this->retrievePlans();
        $user = Auth::user();
        return view('page.subscribe', [
            'user'=>$user,
            'intent' => $user->createSetupIntent(),
            'plans' => $plans,
            'billingCycleType'=>0,
            'paidAmount' => 0,
            'profileMemberCountVal' => 0
        ]);
    }

    /**
    * Function for Create customer on stripe, make payments and save subscription details in table
    */
    public function processSubscription(Request $request)
    {   
       // Get plan id from form input 
       $selectedPlanId = $request->selectedPlanId;
       $user = Auth::user();

       // Get payment methods from from input
       $paymentMethod = $request->input('payment_method');
      
       // Check customer already exist or not. if not exist then create customer on dashboard stripe
       $customer = $user->createOrGetStripeCustomer();

       // Add payment method 
       $user->addPaymentMethod($paymentMethod);

        try {
            
            // Create users subscriptions
            $subscribtion = $user->newSubscription('default', $selectedPlanId);
           
            // Checked if subscriptions is new then we give 60 days trail period
            $checkSubscriptions = DB::table('subscriptions')->where('user_id',$user->id)->first();
            if(isset($checkSubscriptions) && !empty($checkSubscriptions)){ // Checked existing customer
                $subscribtion = $subscribtion->create($paymentMethod);
            }else{
                $subscribtion = $subscribtion->trialDays(1)
                                ->create($paymentMethod);
            }

            if($subscribtion){

                // Get subscription details from 
                $key = \config('services.stripe.secret');
                
                $stripe = new \Stripe\StripeClient($key);
                $details = $stripe->subscriptions->retrieve(
                    $subscribtion->stripe_id,
                    []
                );
                
                if(isset($details) && !empty($details)){
                    // Store payment details in table
                    $subscribtion->billing_cycle_date = date('Y-m-d H:i:s',$details->billing_cycle_anchor);
                    $subscribtion->current_period_start = !empty($details->current_period_start)?date('Y-m-d H:i:s',$details->current_period_start):Null;
                    $subscribtion->current_period_end = !empty($details->current_period_end)?date('Y-m-d H:i:s',$details->current_period_end):Null;
                    $subscribtion->customer = $details->customer;
                    $subscribtion->amount = !empty($details->plan->amount)?$details->plan->amount/100:Null;
                    $subscribtion->currency = !empty($details->plan->currency)?$details->plan->currency:Null;
                    $subscribtion->interval_val = !empty($details->plan->interval)?$details->plan->interval:Null;
                    $subscribtion->trial_start_at = !empty($details->trial_start)?date('Y-m-d H:i:s',$details->trial_start):Null;
                    // if($subscribtion->save()){
                    //     // Store log in payment log table
                    //     DB::table('payment_log')->insert(['subscription_id'=>$subscribtion->id,'user_id' =>$user->id,'customer' => $subscribtion->customer,'stripe_id' =>$subscribtion->stripe_id,'payload' =>json_encode($details),'created_at'=>date('Y-m-d H:i:s')]);
                    // } 

                    // Update plan type flag
                    $updateUser = User::find($user->id);
                    $updateUser->planType = 2;
                    $updateUser->save();

                    
                    // Send email to user and admin
                    // Helper::sendSubscriptionEmailNotification($user,$subscribtion);

                    // Delete table last entry
                    DB::table('subscriptions')->where('id',$subscribtion->id)->delete();
                }
            
            }
            
            /*$key = \config('services.stripe.secret');
            $stripe = new \Stripe\StripeClient($key);
            
            // Checked if subscriptions is new then we give 60 days trail period
            $checkSubscriptions = DB::table('subscriptions')->where('user_id',$user->id)->first();
            if(isset($checkSubscriptions) && !empty($checkSubscriptions)){ // Checked existing customer
                $subscription = $stripe->subscriptions->create([
                    'customer' => $customer->id,
                    'items' => [
                      ['price' => $selectedPlanId]
                    ],
                ]);
            }else{
                $subscription = $stripe->subscriptions->create([
                    'customer' => $customer->id,
                    'items' => [
                      ['price' => $selectedPlanId],
                    ],
                    'trial_period_days' => '1'
                ]);
            }
            if($subscription){
                // Update plan type flag
                $updateUser = User::find($user->id);
                $updateUser->planType = 2;
                $updateUser->save();

                $details = array();
                $details['stripe_id'] = $subscription->id;
                $details['billing_cycle_date'] = date('Y-m-d H:i:s',$subscription->billing_cycle_anchor);
                $details['current_period_start'] = !empty($subscription->current_period_start)?date('Y-m-d H:i:s',$subscription->current_period_start):Null;;
                $details['current_period_end'] = !empty($subscription->current_period_end)?date('Y-m-d H:i:s',$subscription->current_period_end):Null;
                $details['customer'] = !empty($subscription->customer)?$subscription->customer:Null;
                $details['amount'] = !empty($subscription->plan->amount)?$subscription->plan->amount/100:Null;
                $details['currency'] = !empty($subscription->plan->currency)?$subscription->plan->currency:Null;
                $details['interval_val'] = !empty($subscription->plan->interval)?$subscription->plan->interval:Null;
                $details['stripe_status'] = !empty($subscription->status)?$subscription->status:Null;
                $details['trial_start_at'] = !empty($subscription->trial_start)?date('Y-m-d H:i:s',$subscription->trial_start):Null;
                $details['trial_ends_at'] = !empty($subscription->trial_end)?date('Y-m-d H:i:s',$subscription->trial_end):Null;

                $details = (object)$details;

                // Send email to user and admin
                Helper::sendSubscriptionEmailNotification($user,$details);
            }*/


        }catch (\Exception $e) {
           return back()->withErrors(['message' => 'Error creating subscription. ' . $e->getMessage()]);
        }
       
       return redirect('congratulations');
    }

    /*
     * Function for get all user subscriptions list from stripe dashboard
     */
    public function getSubscriptionList() {
        // Set stripe secret key
        $key = \config('services.stripe.secret');
        $user = Auth::user();
        // $subscriptionItem = $user->subscription()->get()->toArray();
        $stripe = new \Stripe\StripeClient($key);
        // Get all user subscriptions
        $dataActive = $stripe->subscriptions->all(['customer' => $user->stripe_id,'status'=>'all']);
        return view('page.subscription-list', compact('dataActive'));
    }

    /*
     * Frunction for cancel active subscription on stripe dashboard
    */
    public function cancelSubscription($id,Request $request){
        $key = \config('services.stripe.secret');
        $stripe = new \Stripe\StripeClient($key);
        $cancelPlan = $stripe->subscriptions->cancel(
                        $id,
                        []
                       );
        if($cancelPlan){
            // Update cancel status
            DB::table('subscriptions')->where('stripe_id',$id)->update(['stripe_status'=>'canceled','cancel_at' => date('Y-m-d H:i:s')]);
            
            // Send email to user and admin
            $user = Auth::user();

            // Get subscription details
            $getSubscriptionDetails = DB::table('subscriptions')->where('stripe_id',$id)->first();
            Helper::sendCancelSubscriptionEmailNotification($user,$getSubscriptionDetails);

            return redirect('my-profile')->with('message', 'Subscriptions canceled successfully');    
        }else{
            return back()->with('error', 'Something went wrong. Please try again later.'); 
        }        
             
    }

    /**
    * Function for save plan type and profile Member Count 
    */
    public function savePlanType(Request $request){
        $planType = $request->planType;
        $user = Auth::user();
        if(isset($planType) && !empty($planType)){
           $updateUser = User::find($user->id);
           $updateUser->planType =  $planType;
           $updateUser->profileMemberCount =  5;

           // Checked first time subscription  or existing user subscription
           $checkSubscriptions = DB::table('subscriptions')->where('user_id',$user->id)->first();
           if(!isset($checkSubscriptions) || empty($checkSubscriptions)){ // Checked existing customer
            $updateUser->remainingProfileMemberCount =  5;
           }

           if($updateUser->save()){
            return json_encode(array('status'=>'1'));
           }else{
            return json_encode(array('status'=>'0'));
           }
        }
    }

    /*
    * Function for get selected plan id and redirect subscribtion page with plan id 
    */
    public function addPaymentMethod(Request $request){
       $selectedPlanId = $request->selectedPlanId;
       $user = Auth::user();
       return view('page.subscribe', [
            'selectedPlanId'=>$selectedPlanId,
            'intent' => $user->createSetupIntent(),
       ]);
    }

    /**
     * Function for get static plan form stripe and redirect plan options page 
     */
    public function planOptions(Request $request){
        // Set stripe secret key
        $key = \config('services.stripe.secret');
        $stripe = new \Stripe\StripeClient($key);
        
        // Get all plan from stripe dashboard
        $plansraw = $stripe->plans->all();
        $plans = $plansraw->data;
        foreach($plans as $plan) {
            $prod = $stripe->products->retrieve(
                $plan->product,[]
            );
            $plan->product = $prod;
        }
        
        return view('page.plan-options', [
            'plans'=>$plans,
        ]);
    }


    public function trialEnd(Request $request){
        // Set stripe secret key
        $key = \config('services.stripe.secret');
        \Stripe\Stripe::setApiKey($key);
        $trialEnd = \Stripe\Subscription::update('sub_1KfdsZHtbsz4q7WstClXcIcO', [
                        'trial_end' => 'now',
                    ]);
        pr($trialEnd);            
    }
}
