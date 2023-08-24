<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Notification;
use App\Notifications\SendBarcodeNotification;
use Auth;

class BarcodeController extends Controller
{

    /**
     *  Get the product data based on the barcode
     */
    public function getProductByBarcode(Request $request){

        try {
            
            // Get the barcode value
            $barcodeValue = $request->barcode;
            // Get the product data by the barcode value
            $productData = Product::getByBarcode($barcodeValue);
            // Check if the product data is not empty then return the data with message
            if(!empty($productData)){
                return json_encode([
                    'data' => $productData,
                    'message'=> 'Found product for the barcode in DB. Please confirm and save to Cabinet',
                    'status' => 1
                ]);
            }else{
                // Else when the product data is empty then return the empty data with message
                return json_encode([
                    'data' => [],
                    'message'=> 'Barcode '.$barcodeValue.'<br> Sorry – Did not find in database.',
                    'sendBarcodeMailUrl' => route('send-barcode-mail',$barcodeValue),
                    'status' => 0
                ]);
            }            
            
        }catch (Exception $e) {
            /* Something went wrong while displaying details */
            $error_message = $e->getMessage();
            return json_encode([
                'message'=> $error_message,
                'status' => 0
            ]);
        }

    }

    /**
     *  Send the barcode suggestion in mail to admin 
     */
    public function sendBarcodeSuggestionMail(Request $request){

        try {

            // Get the barcode value from the input request
            $barcodeValue = $request->barcode;

            // Store the message for the mail body
            $sendData = [
                'body' => 'Below is the new suggested barcode <br> from user : '.Auth::user()->getUserName().' ('.Auth::user()->email .')
    
                <b>Barcode</b>: '.$barcodeValue.'
                ',
            ];
            // Send mail to admin
            $sendEmail = Notification::route('mail','admin@wellkasa.com')->notify(new SendBarcodeNotification($sendData));
            if(empty($sendEmail)){
                return redirect()->back()->with('message','Your barcode requested submitted successfully');
            }else{
                return redirect()->back()->with('error','Failed to submit the request, Please try again later.');
            }        
            
        }catch (Exception $e) {
            /* Something went wrong while sending details */
            $error_message = $e->getMessage();
            return json_encode([
                'message'=> $error_message,
                'status' => 0
            ]);
        }

    }

}