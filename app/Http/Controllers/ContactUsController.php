<?php
/*
|-----------------------------------------------------------------------------
| LARAVEL APP BASE CLASS, PACKAGES DEFINE
|-----------------------------------------------------------------------------
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;


class ContactUsController extends Controller
{
    
    /*
    |--------------------------------------------------------------------------
    | Contact Us Controller
    |--------------------------------------------------------------------------
    |us.
    |
    | This controller has only show/display page of contact 
    */

    /**
     * This function complies load contact us view template.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('page.contact-us');
    }
}