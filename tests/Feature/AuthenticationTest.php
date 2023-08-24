<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Session;
use App\Helpers\Helpers as Helper;
use voku\helper\HtmlDomParser;


class AuthenticationTest extends TestCase
{
     /**
     * A basic test example.
     *
     * @return void
     */
    public function testLoginTrue()
    {
        Session::start();
        $credential = [
            'email' => 'demo@wellkasa.com',
            'password' => 'Well@2018',
            '_token' => csrf_token()
        ];
         $this->post('login',$credential)->assertRedirect('/my-wellkasa-rx');
         //$this->post('login',$credential)->assertRedirect('/medicine-cabinet');
    }
}
