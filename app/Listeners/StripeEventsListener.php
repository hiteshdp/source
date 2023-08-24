<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Cashier\Events\WebhookReceived;
use App\Helpers\Helpers as Helper;
use DB;

class StripeEventsListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(WebhookReceived $event)
    {
        $payload_type = $event->payload['type'];
        switch ($payload_type) {
            case "invoice.payment_succeeded":

                // Get user id base on customer
                $getCustomer = DB::table('users')->where('email',$event->payload['data']['object']['customer_email'])->first();
                $user_id = 0;
                if(isset($getCustomer) && !empty($getCustomer)){
                    $user_id = $getCustomer->id;
                } 


                // Store payment logs when payment is succeeded
                $paymentLogId = DB::table('payment_log')->insertGetId(
                    array(
                           'user_id' =>$user_id, 
                           'customer' => $event->payload['data']['object']['customer'],
                           'stripe_id' =>  $event->payload['data']['object']['subscription'],
                           'payload'     =>   json_encode($event->payload), 
                           'type'   =>   $payload_type,
                           'created_at'   =>   date('Y-m-d H:i:s')
                    )
               );


               $store_record['user_id'] = $user_id;
               $store_record['name'] = 'default';
               $store_record['stripe_id'] = isset($event->payload['data']['object']['subscription'])?$event->payload['data']['object']['subscription']:Null;
               $store_record['billing_cycle_date'] = isset($event->payload['data']['object']['lines']['data'][0]['period']['end'])?date('Y-m-d H:i:s',$event->payload['data']['object']['lines']['data'][0]['period']['end']):Null;
               $store_record['current_period_start'] = isset($event->payload['data']['object']['lines']['data'][0]['period']['start'])?date('Y-m-d H:i:s',$event->payload['data']['object']['lines']['data'][0]['period']['start']):Null;
               $store_record['current_period_end'] = isset($event->payload['data']['object']['lines']['data'][0]['period']['end'])?date('Y-m-d H:i:s',$event->payload['data']['object']['lines']['data'][0]['period']['end']):Null;
               $store_record['customer'] = isset($event->payload['data']['object']['customer'])?$event->payload['data']['object']['customer']:Null;
               $store_record['amount'] = !empty($event->payload['data']['object']['lines']['data'][0]['plan']['amount'])?$event->payload['data']['object']['lines']['data'][0]['plan']['amount']/100:Null;
               $store_record['currency'] = isset($event->payload['data']['object']['lines']['data'][0]['plan']['currency'])?$event->payload['data']['object']['lines']['data'][0]['plan']['currency']:Null;
               $store_record['interval_val'] = isset($event->payload['data']['object']['lines']['data'][0]['plan']['interval'])?$event->payload['data']['object']['lines']['data'][0]['plan']['interval']:Null;
               $store_record['stripe_status'] = isset($event->payload['data']['object']['status'])?$event->payload['data']['object']['status']:Null;
               $store_record['stripe_price'] = isset($event->payload['data']['object']['lines']['data'][0]['plan']['id'])?$event->payload['data']['object']['lines']['data'][0]['plan']['id']:Null;
               $store_record['quantity'] = isset($event->payload['data']['object']['lines']['data'][0]['quantity'])?$event->payload['data']['object']['lines']['data'][0]['quantity']:Null;
               $store_record['invoice_url'] = $event->payload['data']['object']['invoice_pdf'];
               $store_record['created_at']   =   date('Y-m-d H:i:s');


               // Insert data
               $subscriptionId = DB::table('subscriptions')->insertGetId($store_record);

               // Update subscription id in payment log table
               DB::table('payment_log')->where('id',$paymentLogId)->update(array('subscription_id' => $subscriptionId));

               // Send email
               if(isset($getCustomer) && !empty($getCustomer)){
                    $store_record = (object)$store_record;
                    // Get user details
                    Helper::sendSubscriptionEmailNotification($getCustomer,$store_record);
                } 
              break;
            case "customer.subscription.trial_will_end":
                // Get user id base on customer
                $getCustomer = DB::table('subscriptions')->where('customer',$event->payload['data']['object']['customer'])->orderBy('id','desc')->first();
                $user_id = 0;
                if(isset($getCustomer) && !empty($getCustomer)){
                    $user_id = $getCustomer->user_id;
                }    


                // Store payment logs when trial end 
                $paymentLogId = DB::table('payment_log')->insertGetId(
                    array(
                        'user_id' =>$user_id, 
                        'customer' => $event->payload['data']['object']['customer'],
                        'stripe_id' => $event->payload['data']['object']['id'],
                        'payload'     =>  json_encode($event->payload), 
                        'type'   =>   $payload_type,
                        'created_at'   =>   date('Y-m-d H:i:s')
                    )
                );



                $store_record['user_id'] = $user_id;
                $store_record['name'] = 'default';
                $store_record['stripe_id'] = isset($event->payload['data']['object']['id'])?$event->payload['data']['object']['id']:Null;
                $store_record['billing_cycle_date'] = isset($event->payload['data']['object']['billing_cycle_anchor'])?date('Y-m-d H:i:s',$event->payload['data']['object']['billing_cycle_anchor']):Null;
                $store_record['current_period_start'] = isset($event->payload['data']['object']['current_period_start'])?date('Y-m-d H:i:s',$event->payload['data']['object']['current_period_start']):Null;
                $store_record['current_period_end'] = isset($event->payload['data']['object']['current_period_end'])?date('Y-m-d H:i:s',$event->payload['data']['object']['current_period_end']):Null;
                $store_record['customer'] =  isset($event->payload['data']['object']['customer'])?$event->payload['data']['object']['customer']:Null;
                $store_record['amount'] = !empty($event->payload['data']['object']['plan']['amount'])?$event->payload['data']['object']['plan']['amount']/100:Null;
                $store_record['currency'] = isset($event->payload['data']['object']['plan']['currency'])?$event->payload['data']['object']['plan']['currency']:Null;
                $store_record['interval_val'] = isset($event->payload['data']['object']['plan']['interval'])?$event->payload['data']['object']['plan']['interval']:Null;
                $store_record['stripe_status'] = isset($event->payload['data']['object']['status'])?$event->payload['data']['object']['status']:Null;
                $store_record['stripe_price'] = isset($event->payload['data']['object']['plan']['id'])?$event->payload['data']['object']['plan']['id']:Null;
                $store_record['quantity'] = isset($event->payload['data']['object']['quantity'])?$event->payload['data']['object']['quantity']:Null;
                $store_record['trial_start_at'] = isset($event->payload['data']['object']['trial_start'])?date('Y-m-d H:i:s',$event->payload['data']['object']['trial_start']):Null;
                $store_record['trial_ends_at'] = isset($event->payload['data']['object']['trial_end'])?date('Y-m-d H:i:s',$event->payload['data']['object']['trial_end']):Null; 
                $store_record['created_at'] = date('Y-m-d H:i:s');


                // Checked subscription avaiable or not
                $checkSubscriptions = DB::table('subscriptions')->where('stripe_id',$event->payload['data']['object']['id'])->orderBy('id','desc')->first();
                if(isset($checkSubscriptions) && !empty($checkSubscriptions)){
                    // Update data 
                    $subscriptionId = DB::table('subscriptions')->where('id',$checkSubscriptions->id)->update($store_record);

                    // Update subscription id in payment log table
                    DB::table('payment_log')->where('id',$paymentLogId)->update(array('subscription_id' => $checkSubscriptions->id));
                }else{
                    // Insert data
                    $subscriptionId = DB::table('subscriptions')->insertGetId($store_record);

                    // Update subscription id in payment log table
                    DB::table('payment_log')->where('id',$paymentLogId)->update(array('subscription_id' => $subscriptionId));
                }  

              break;
            case "customer.subscription.deleted":
                $customer = $event->payload['data']['object']['customer'];
                // Get user details
                $getUser = DB::table('users')->where('stripe_id',$customer)->first();  // Because by defulat store customer id in stripe id field

                $user_id = Null;
                if(isset($getUser) && !empty($getUser)){
                    $user_id = $getUser->id;
                }

                // Store payment logs when subscription is canceled
                DB::table('payment_log')->insert(
                    array(
                            'user_id' =>$user_id, 
                            'customer' => $event->payload['data']['object']['customer'],
                            'stripe_id' => $event->payload['data']['object']['id'],
                            'payload'     =>   json_encode($event->payload), 
                            'type'   =>   $payload_type,
                            'created_at'   =>   date('Y-m-d H:i:s')
                    )
                );
                break;
            case "invoice.payment_failed":
                $customer = $event->payload['data']['object']['customer'];

                // Get user details
                $getUser = DB::table('users')->where('stripe_id',$customer)->first();  // Because by defulat store customer id in stripe id field

                $user_id = Null;
                if(isset($getUser) && !empty($getUser)){
                    $user_id = $getUser->id;
                }
                
                // Store payment logs when subscription is canceled
                DB::table('payment_log')->insert(
                    array(
                            'user_id' =>$user_id, 
                            'customer' => $customer,
                            'stripe_id' => $event->payload['data']['object']['subscription'],
                            'payload'     =>   json_encode($event->payload), 
                            'type'   =>   $payload_type,
                            'created_at'   =>   date('Y-m-d H:i:s')
                    )
                );

                // Send email to admin and users
                if(isset($getUser) && !empty($getUser)){
                    $store_record['stripe_id'] = isset($event->payload['data']['object']['subscription'])?$event->payload['data']['object']['subscription']:Null;
                    $store_record['billing_cycle_date'] = isset($event->payload['data']['object']['lines']['data'][0]['period']['end'])?date('Y-m-d H:i:s',$event->payload['data']['object']['lines']['data'][0]['period']['end']):Null;
                    $store_record['current_period_start'] = isset($event->payload['data']['object']['lines']['data'][0]['period']['start'])?date('Y-m-d H:i:s',$event->payload['data']['object']['lines']['data'][0]['period']['start']):Null;
                    $store_record['current_period_end'] = isset($event->payload['data']['object']['lines']['data'][0]['period']['end'])?date('Y-m-d H:i:s',$event->payload['data']['object']['lines']['data'][0]['period']['end']):Null;
                    $store_record['customer'] = isset($event->payload['data']['object']['customer'])?$event->payload['data']['object']['customer']:Null;
                    $store_record['amount'] = !empty($event->payload['data']['object']['lines']['data'][0]['plan']['amount'])?$event->payload['data']['object']['lines']['data'][0]['plan']['amount']/100:Null;
                    $store_record['currency'] = isset($event->payload['data']['object']['lines']['data'][0]['plan']['currency'])?$event->payload['data']['object']['lines']['data'][0]['plan']['currency']:Null;
                    $store_record['interval_val'] = isset($event->payload['data']['object']['lines']['data'][0]['plan']['interval'])?$event->payload['data']['object']['lines']['data'][0]['plan']['interval']:Null;
                    $store_record['stripe_status'] = isset($event->payload['data']['object']['status'])?$event->payload['data']['object']['status']:Null;
                    $store_record['stripe_price'] = isset($event->payload['data']['object']['lines']['data'][0]['plan']['id'])?$event->payload['data']['object']['lines']['data'][0]['plan']['id']:Null;
                    $store_record['quantity'] = isset($event->payload['data']['object']['lines']['data'][0]['quantity'])?$event->payload['data']['object']['lines']['data'][0]['quantity']:Null;
                    $store_record['created_at']   =   date('Y-m-d H:i:s');
                    $store_record = (object)$store_record;
                    Helper::sendSubscriptionFailedEmailNotification($getUser,$store_record);
                }
                break;
            default:
                // code to be executed if n is different from all labels;
                DB::table('payment_log')->insert(
                    array(
                            'customer' => isset($event->payload['data']['object']['customer'])?$event->payload['data']['object']['customer']:Null,
                            'payload'     =>  json_encode($event->payload), 
                            'type'   =>   $payload_type,
                            'created_at'   =>   date('Y-m-d H:i:s')
                    )
                );
                break;
        }
    }
}
