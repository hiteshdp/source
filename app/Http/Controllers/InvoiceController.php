<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ProfileMembers;
use App\Models\Subscriptions;
use DB;
use Crypt;

class InvoiceController extends Controller
{
    /**
     * Function to display current logged in user's invoice details in invoice Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($userId)
    {
        // decrypt the user id
        $userId = Crypt::decrypt($userId);
        $invoiceDetails = array();

        // Get subscription data from the table if exists
        $subscriptionArr = Subscriptions::where('user_id',$userId)->orderby('created_at','DESC')->get()->toArray();
        if(!empty($subscriptionArr)){
            foreach($subscriptionArr as $subscriptionArrKey => $subscriptionArrValue){
                $data['createdAt'] = !empty($subscriptionArrValue['created_at']) ? date('d-M-Y', strtotime($subscriptionArrValue['created_at'])) : '-';
                $data['amount'] = !empty($subscriptionArrValue['amount']) ? '$'.number_format($subscriptionArrValue['amount'], 2, '.', ''): '-';
                $data['status'] = !empty($subscriptionArrValue['stripe_status']) ? ucwords($subscriptionArrValue['stripe_status']) : '-';
                $data['stripeId'] = !empty($subscriptionArrValue['stripe_id']) ?  $subscriptionArrValue['stripe_id'] : '';
                $data['invoice_url'] = !empty($subscriptionArrValue['invoice_url']) ?  $subscriptionArrValue['invoice_url'] : '';
                
                $invoiceDetails[] = $data;
            }

            return view('page.invoices',compact('invoiceDetails'));
        
        }else{
            return redirect()->back()->withErrors("Invoice Details Not Found");
        }
        
    }
}
